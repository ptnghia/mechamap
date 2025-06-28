<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    /**
     * Display the chat page
     */
    public function index()
    {
        // Get user's conversations with unread count
        $conversations = Conversation::whereHas('participants', function ($query) {
            $query->where('user_id', Auth::id());
        })
        ->with([
            'participants.user:id,name,email,avatar',
            'lastMessage:id,conversation_id,user_id,content,created_at'
        ])
        ->withCount(['messages as unread_count' => function ($query) {
            $query->whereHas('conversation.participants', function ($q) {
                $q->where('user_id', Auth::id())
                  ->where(function ($q2) {
                      $q2->whereNull('last_read_at')
                         ->orWhereColumn('messages.created_at', '>', 'conversation_participants.last_read_at');
                  });
            });
        }])
        ->latest('updated_at')
        ->get();

        // Transform conversations for view
        $conversations = $conversations->map(function ($conversation) {
            $otherParticipant = $conversation->participants
                ->where('user_id', '!=', Auth::id())
                ->first()?->user;

            return [
                'id' => $conversation->id,
                'title' => $conversation->title,
                'other_participant' => $otherParticipant ? [
                    'id' => $otherParticipant->id,
                    'name' => $otherParticipant->name,
                    'email' => $otherParticipant->email,
                    'avatar' => $otherParticipant->avatar ?? '/images/default-avatar.png',
                    'is_online' => false, // Will implement online status later
                ] : null,
                'last_message' => $conversation->lastMessage ? [
                    'id' => $conversation->lastMessage->id,
                    'content' => $conversation->lastMessage->content,
                    'user_id' => $conversation->lastMessage->user_id,
                    'created_at' => $conversation->lastMessage->created_at,
                    'is_mine' => $conversation->lastMessage->user_id == Auth::id(),
                ] : null,
                'unread_count' => $conversation->unread_count ?? 0,
                'updated_at' => $conversation->updated_at,
            ];
        });

        return view('chat.index', compact('conversations'));
    }

    /**
     * Show specific conversation
     */
    public function show($id)
    {
        $conversation = Conversation::findOrFail($id);

        // Check if user is participant
        $isParticipant = $conversation->participants()->where('user_id', Auth::id())->exists();
        if (!$isParticipant) {
            abort(403, 'Bạn không có quyền xem cuộc trò chuyện này.');
        }

        // Get messages
        $messages = $conversation->messages()
            ->with('user:id,name,email,avatar')
            ->orderBy('created_at', 'asc')
            ->get();

        // Get other participant
        $otherParticipant = $conversation->participants()
            ->where('user_id', '!=', Auth::id())
            ->with('user:id,name,email,avatar')
            ->first()?->user;

        // Mark as read
        $participant = $conversation->participants()->where('user_id', Auth::id())->first();
        if ($participant) {
            $participant->update(['last_read_at' => now()]);
        }

        return view('chat.show', compact('conversation', 'messages', 'otherParticipant'));
    }

    /**
     * Send a message
     */
    public function sendMessage(Request $request, $id)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $conversation = Conversation::findOrFail($id);

        // Check if user is participant
        $isParticipant = $conversation->participants()->where('user_id', Auth::id())->exists();
        if (!$isParticipant) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Create message
        $message = Message::create([
            'conversation_id' => $conversation->id,
            'user_id' => Auth::id(),
            'content' => $request->content,
        ]);

        // Update conversation timestamp
        $conversation->touch();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => [
                    'id' => $message->id,
                    'content' => $message->content,
                    'user_id' => $message->user_id,
                    'created_at' => $message->created_at,
                    'user' => [
                        'id' => Auth::id(),
                        'name' => Auth::user()->name,
                        'avatar' => Auth::user()->avatar ?? '/images/default-avatar.png',
                    ]
                ]
            ]);
        }

        return redirect()->route('chat.show', $id);
    }

    /**
     * Start new conversation
     */
    public function create()
    {
        // Get users for new conversation (exclude current user)
        $users = User::where('id', '!=', Auth::id())
            ->select('id', 'name', 'email', 'avatar')
            ->orderBy('name')
            ->get();

        return view('chat.create', compact('users'));
    }

    /**
     * Store new conversation
     */
    public function store(Request $request)
    {
        $request->validate([
            'recipient_id' => 'required|exists:users,id|different:' . Auth::id(),
            'message' => 'required|string|max:1000',
        ]);

        // Check if conversation already exists between these two users
        $existingConversation = $this->findConversationBetweenUsers(Auth::id(), $request->recipient_id);

        if ($existingConversation) {
            // Add message to existing conversation
            Message::create([
                'conversation_id' => $existingConversation->id,
                'user_id' => Auth::id(),
                'content' => $request->message,
            ]);

            $existingConversation->touch();

            return redirect()->route('chat.show', $existingConversation->id)
                ->with('success', 'Tin nhắn đã được gửi!');
        }

        // Create new conversation
        $conversation = Conversation::create(['title' => null]);

        // Add participants
        ConversationParticipant::create([
            'conversation_id' => $conversation->id,
            'user_id' => Auth::id(),
        ]);

        ConversationParticipant::create([
            'conversation_id' => $conversation->id,
            'user_id' => $request->recipient_id,
        ]);

        // Add first message
        Message::create([
            'conversation_id' => $conversation->id,
            'user_id' => Auth::id(),
            'content' => $request->message,
        ]);

        return redirect()->route('chat.show', $conversation->id)
            ->with('success', 'Cuộc trò chuyện mới đã được tạo!');
    }

    /**
     * Get unread messages count for notifications
     */
    public function getUnreadCount()
    {
        $unreadCount = Message::whereHas('conversation.participants', function ($query) {
            $query->where('user_id', Auth::id())
                  ->where(function ($q) {
                      $q->whereNull('last_read_at')
                         ->orWhereColumn('messages.created_at', '>', 'conversation_participants.last_read_at');
                  });
        })
        ->where('user_id', '!=', Auth::id()) // Exclude own messages
        ->count();

        return response()->json(['unread_count' => $unreadCount]);
    }

    /**
     * Search users for new conversation
     */
    public function searchUsers(Request $request)
    {
        $query = $request->get('q', '');

        $users = User::where('id', '!=', Auth::id())
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%")
                  ->orWhere('username', 'like', "%{$query}%");
            })
            ->select('id', 'name', 'email', 'username', 'avatar')
            ->limit(10)
            ->get();

        return response()->json(['data' => $users]);
    }

    /**
     * Find existing conversation between two users
     */
    private function findConversationBetweenUsers($userId1, $userId2)
    {
        return Conversation::whereHas('participants', function ($query) use ($userId1) {
            $query->where('user_id', $userId1);
        })
        ->whereHas('participants', function ($query) use ($userId2) {
            $query->where('user_id', $userId2);
        })
        ->whereDoesntHave('participants', function ($query) use ($userId1, $userId2) {
            // Ensure conversation has exactly 2 participants
            $query->whereNotIn('user_id', [$userId1, $userId2]);
        })
        ->first();
    }
}
