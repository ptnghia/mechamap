<?php

namespace App\Http\Controllers\Dashboard\Common;

use App\Http\Controllers\Dashboard\BaseController;
use App\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

/**
 * Conversation Controller cho Dashboard
 *
 * Quản lý tin nhắn và cuộc trò chuyện
 */
class ConversationController extends BaseController
{
    /**
     * Hiển thị danh sách conversations
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $filter = $request->get('filter');
        $sortBy = $request->get('sort_by', 'updated_at');
        $sortOrder = $request->get('sort_order', 'desc');

        // Get conversations with participants and last message
        $query = Conversation::whereHas('participants', function ($query) {
            $query->where('user_id', $this->user->id);
        })
        ->with([
            'participants.user:id,name,email,username,avatar',
            'lastMessage:id,conversation_id,user_id,content,created_at'
        ]);

        // Apply search
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhereHas('participants.user', function ($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                               ->orWhere('username', 'like', "%{$search}%");
                  })
                  ->orWhereHas('messages', function ($messageQuery) use ($search) {
                      $messageQuery->where('content', 'like', "%{$search}%");
                  });
            });
        }

        // Apply filters
        if ($filter === 'unread') {
            $query->whereHas('messages', function ($messageQuery) {
                $messageQuery->where('user_id', '!=', $this->user->id)
                    ->whereHas('conversation.participants', function ($participantQuery) {
                        $participantQuery->where('user_id', $this->user->id)
                            ->where(function ($q) {
                                $q->whereNull('last_read_at')
                                  ->orWhereColumn('messages.created_at', '>', 'conversation_participants.last_read_at');
                            });
                    });
            });
        } elseif ($filter === 'started') {
            // Skip this filter since conversations table doesn't have created_by column
            // We can implement this later if needed
        } elseif ($filter === 'active') {
            $query->where('updated_at', '>=', now()->subDays(7));
        }

        // Apply sorting
        $query->orderBy($sortBy, $sortOrder);

        $conversations = $query->paginate(15);

        // Calculate filter counts for badges
        $filterCounts = [
            'all' => Conversation::whereHas('participants', function ($query) {
                $query->where('user_id', $this->user->id);
            })->count(),
            'unread' => Conversation::whereHas('participants', function ($query) {
                $query->where('user_id', $this->user->id);
            })->whereHas('messages', function ($messageQuery) {
                $messageQuery->where('user_id', '!=', $this->user->id)
                    ->whereHas('conversation.participants', function ($participantQuery) {
                        $participantQuery->where('user_id', $this->user->id)
                            ->where(function ($q) {
                                $q->whereNull('last_read_at')
                                  ->orWhereColumn('messages.created_at', '>', 'conversation_participants.last_read_at');
                            });
                    });
            })->count(),
            'started' => 0, // Skip this count since conversations table doesn't have created_by column
            'active' => Conversation::whereHas('participants', function ($query) {
                $query->where('user_id', $this->user->id);
            })->where('updated_at', '>=', now()->subDays(7))->count()];

        return $this->dashboardResponse('dashboard.common.conversations.index', [
            'conversations' => $conversations,
            'search' => $search,
            'filter' => $filter,
            'filterCounts' => $filterCounts]);
    }

    /**
     * Hiển thị conversation cụ thể
     */
    public function show(Conversation $conversation)
    {
        // Check if user is participant
        if (!$conversation->participants->contains('user_id', $this->user->id)) {
            abort(403, 'You are not a participant in this conversation.');
        }

        // Mark conversation as read for current user
        $conversation->participants()
            ->where('user_id', $this->user->id)
            ->update(['last_read_at' => now()]);

        // Load messages with pagination
        $messages = $conversation->messages()
            ->with(['user'])
            ->latest()
            ->paginate(50);

        // Get other participants
        $otherParticipants = $conversation->participants
            ->where('id', '!=', $this->user->id);

        return $this->dashboardResponse('dashboard.common.conversations.show', [
            'conversation' => $conversation,
            'messages' => $messages,
            'otherParticipants' => $otherParticipants]);
    }

    /**
     * Tạo conversation mới
     */
    public function create(Request $request)
    {
        $search = $request->get('search');
        $users = collect();

        if ($search) {
            $users = User::where('id', '!=', $this->user->id)
                ->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                          ->orWhere('username', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%");
                })
                ->take(10)
                ->get();
        }

        return $this->dashboardResponse('dashboard.common.conversations.create', [
            'users' => $users,
            'search' => $search]);
    }

    /**
     * Lưu conversation mới
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'recipient_id' => 'required|exists:users,id',
            'title' => 'nullable|string|max:255',
            'first_message' => 'required|string|max:5000']);

        $participantIds = [$request->recipient_id, $this->user->id];

        // Check if conversation already exists with same participants
        $existingConversation = Conversation::whereHas('participants', function ($query) use ($participantIds) {
            $query->whereIn('user_id', $participantIds);
        }, '=', count($participantIds))->first();

        if ($existingConversation) {
            // Add message to existing conversation
            $existingConversation->messages()->create([
                'user_id' => $this->user->id,
                'content' => $request->first_message]);

            $existingConversation->touch(); // Update updated_at

            return redirect()->route('dashboard.conversations.show', $existingConversation)
                ->with('success', 'Message sent successfully.');
        }

        // Create new conversation
        $conversation = Conversation::create([
            'title' => $request->title]);

        // Add participants
        foreach ($participantIds as $userId) {
            ConversationParticipant::create([
                'conversation_id' => $conversation->id,
                'user_id' => $userId,
                'last_read_at' => $userId === $this->user->id ? now() : null]);
        }

        // Add first message
        $conversation->messages()->create([
            'user_id' => $this->user->id,
            'content' => $request->first_message]);

        return redirect()->route('dashboard.conversations.show', $conversation)
            ->with('success', 'Conversation created successfully.');
    }

    /**
     * Reply to a conversation
     */
    public function reply(Request $request, Conversation $conversation): RedirectResponse
    {
        // Check if user is participant
        if (!$conversation->participants->contains('user_id', $this->user->id)) {
            abort(403, 'You are not a participant in this conversation.');
        }

        $request->validate([
            'content' => 'required|string|max:5000']);

        // Add reply message
        $conversation->messages()->create([
            'user_id' => $this->user->id,
            'content' => $request->content]);

        // Update conversation timestamp
        $conversation->touch();

        // Update last_read_at for current user
        $conversation->participants()
            ->where('user_id', $this->user->id)
            ->update(['last_read_at' => now()]);

        return redirect()->route('dashboard.conversations.show', $conversation)
            ->with('success', 'Message sent successfully.');
    }

    /**
     * Search conversations via AJAX
     */
    public function search(Request $request)
    {
        $query = $request->get('q');
        $filter = $request->get('filter');

        if (!$query || strlen($query) < 2) {
            return response()->json(['data' => []]);
        }

        $conversationsQuery = Conversation::whereHas('participants', function ($participantQuery) {
            $participantQuery->where('user_id', $this->user->id);
        })
        ->with([
            'participants.user:id,name,email,username,avatar',
            'lastMessage:id,conversation_id,user_id,content,created_at'
        ])
        ->where(function ($q) use ($query) {
            $q->where('title', 'like', "%{$query}%")
              ->orWhereHas('participants.user', function ($userQuery) use ($query) {
                  $userQuery->where('name', 'like', "%{$query}%")
                           ->orWhere('username', 'like', "%{$query}%");
              })
              ->orWhereHas('messages', function ($messageQuery) use ($query) {
                  $messageQuery->where('content', 'like', "%{$query}%");
              });
        });

        // Apply filter if provided
        if ($filter === 'unread') {
            $conversationsQuery->whereHas('messages', function ($messageQuery) {
                $messageQuery->where('user_id', '!=', $this->user->id)
                    ->whereHas('conversation.participants', function ($participantQuery) {
                        $participantQuery->where('user_id', $this->user->id)
                            ->where(function ($q) {
                                $q->whereNull('last_read_at')
                                  ->orWhereColumn('messages.created_at', '>', 'conversation_participants.last_read_at');
                            });
                    });
            });
        }

        $conversations = $conversationsQuery->take(10)->get();

        $results = $conversations->map(function ($conversation) {
            $otherParticipant = $conversation->participants->where('user_id', '!=', $this->user->id)->first()->user ?? null;

            return [
                'id' => $conversation->id,
                'title' => $conversation->title,
                'url' => route('dashboard.conversations.show', $conversation),
                'other_participant' => $otherParticipant ? [
                    'name' => $otherParticipant->name,
                    'avatar' => $otherParticipant->getAvatarUrl()] : null,
                'last_message' => $conversation->lastMessage ? [
                    'content' => $conversation->lastMessage->content,
                    'created_at' => $conversation->lastMessage->created_at] : null];
        });

        return response()->json(['data' => $results]);
    }

    /**
     * Gửi message trong conversation
     */
    public function storeMessage(Request $request, Conversation $conversation): RedirectResponse
    {
        // Check if user is participant
        if (!$conversation->participants->contains($this->user)) {
            abort(403, 'You are not a participant in this conversation.');
        }

        $request->validate([
            'message' => 'required|string|max:5000']);

        $conversation->messages()->create([
            'user_id' => $this->user->id,
            'content' => $request->message]);

        $conversation->touch(); // Update updated_at

        return redirect()->route('dashboard.conversations.show', $conversation)
            ->with('success', 'Message sent successfully.');
    }

    /**
     * Xóa conversation
     */
    public function destroy(Conversation $conversation): RedirectResponse
    {
        // Check if user is participant
        if (!$conversation->participants->contains($this->user)) {
            abort(403, 'You are not a participant in this conversation.');
        }

        // Remove user from conversation
        $conversation->participants()->detach($this->user->id);

        // If no participants left, delete conversation
        if ($conversation->participants()->count() === 0) {
            $conversation->delete();
        }

        return redirect()->route('dashboard.conversations')
            ->with('success', 'You have left the conversation.');
    }
}
