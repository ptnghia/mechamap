<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class ChatController extends Controller
{
    /**
     * Hiển thị trang chat admin
     */
    public function index(): View
    {
        // Lấy conversations của admin hiện tại
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

        $breadcrumbs = [
            ['title' => 'Admin Chat', 'url' => route('admin.chat.index')]
        ];

        return view('admin.chat.index', compact('conversations', 'breadcrumbs'));
    }

    /**
     * Hiển thị trang tạo tin nhắn mới
     */
    public function create(): View
    {
        $breadcrumbs = [
            ['title' => 'Admin Chat', 'url' => route('admin.chat.index')],
            ['title' => 'Tin nhắn mới', 'url' => route('admin.chat.create')]
        ];

        return view('admin.chat.create', compact('breadcrumbs'));
    }

    /**
     * Hiển thị conversation cụ thể
     */
    public function show($id): View
    {
        $conversation = Conversation::whereHas('participants', function ($query) {
            $query->where('user_id', Auth::id());
        })
        ->with([
            'participants.user:id,name,email,avatar',
            'messages.user:id,name,avatar'
        ])
        ->findOrFail($id);

        // Đánh dấu đã đọc
        ConversationParticipant::where('conversation_id', $id)
            ->where('user_id', Auth::id())
            ->update(['last_read_at' => now()]);

        $breadcrumbs = [
            ['title' => 'Admin Chat', 'url' => route('admin.chat.index')],
            ['title' => 'Trò chuyện', 'url' => route('admin.chat.show', $id)]
        ];

        return view('admin.chat.show', compact('conversation', 'breadcrumbs'));
    }

    /**
     * Tạo conversation mới
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'recipient_id' => ['required', 'exists:users,id', 'different:' . Auth::id()],
            'content' => ['required', 'string', 'max:1000']
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            // Check if conversation already exists
            $existingConversation = $this->findConversationBetweenUsers(Auth::id(), $request->recipient_id);

            if ($existingConversation) {
                // Gửi tin nhắn vào conversation hiện có
                Message::create([
                    'conversation_id' => $existingConversation->id,
                    'user_id' => Auth::id(),
                    'content' => $request->content
                ]);

                $existingConversation->touch(); // Update updated_at

                return redirect()->route('admin.chat.show', $existingConversation->id)
                    ->with('success', 'Tin nhắn đã được gửi thành công!');
            }

            // Tạo conversation mới
            $conversation = Conversation::create();

            // Thêm participants
            ConversationParticipant::create([
                'conversation_id' => $conversation->id,
                'user_id' => Auth::id(),
                'joined_at' => now()
            ]);

            ConversationParticipant::create([
                'conversation_id' => $conversation->id,
                'user_id' => $request->recipient_id,
                'joined_at' => now()
            ]);

            // Tạo tin nhắn đầu tiên
            Message::create([
                'conversation_id' => $conversation->id,
                'user_id' => Auth::id(),
                'content' => $request->content
            ]);

            return redirect()->route('admin.chat.show', $conversation->id)
                ->with('success', 'Conversation đã được tạo và tin nhắn đã được gửi!');

        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Gửi tin nhắn trong conversation
     */
    public function sendMessage(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'content' => ['required', 'string', 'max:1000']
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Nội dung tin nhắn không hợp lệ'], 422);
        }

        try {
            $conversation = Conversation::whereHas('participants', function ($query) {
                $query->where('user_id', Auth::id());
            })->findOrFail($id);

            $message = Message::create([
                'conversation_id' => $conversation->id,
                'user_id' => Auth::id(),
                'content' => $request->content
            ]);

            $conversation->touch(); // Update updated_at

            // Load user relationship
            $message->load('user:id,name,avatar');

            return response()->json([
                'success' => true,
                'message' => [
                    'id' => $message->id,
                    'content' => $message->content,
                    'user_id' => $message->user_id,
                    'user_name' => $message->user->name,
                    'user_avatar' => $message->user->avatar,
                    'created_at' => $message->created_at->format('H:i'),
                    'is_own' => true
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Có lỗi xảy ra khi gửi tin nhắn'], 500);
        }
    }

    /**
     * Search users for new conversation
     */
    public function searchUsers(Request $request)
    {
        $query = $request->get('q', '');

        if (strlen($query) < 2) {
            return response()->json(['data' => []]);
        }

        $users = User::where('id', '!=', Auth::id())
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%")
                  ->orWhere('username', 'like', "%{$query}%");
            })
            ->select('id', 'name', 'email', 'username', 'avatar', 'role', 'created_at')
            ->orderBy('name')
            ->limit(20)
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'username' => $user->username,
                    'avatar' => $user->avatar,
                    'role' => $user->role,
                    'member_since' => $user->created_at->format('Y-m-d')
                ];
            });

        return response()->json(['data' => $users]);
    }

    /**
     * Get unread messages count
     */
    public function getUnreadCount()
    {
        $count = Message::whereHas('conversation.participants', function ($query) {
            $query->where('user_id', Auth::id())
                  ->where(function ($q) {
                      $q->whereNull('last_read_at')
                         ->orWhereColumn('messages.created_at', '>', 'conversation_participants.last_read_at');
                  });
        })->count();

        return response()->json(['count' => $count]);
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
