<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Models\Message;
use App\Models\User;
use App\Services\UnifiedNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ConversationController extends Controller
{
    /**
     * Get a list of conversations for the authenticated user
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            // Get user's conversations with additional data for chat widget
            $query = Conversation::whereHas('participants', function ($query) {
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
            }]);

            // Sort by
            $sortBy = $request->input('sort_by', 'updated_at');
            $sortOrder = $request->input('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Paginate
            $perPage = $request->input('per_page', 15);
            $conversations = $query->with(['participants.user', 'lastMessage'])->paginate($perPage);

            // Add additional information
            $conversations->getCollection()->transform(function ($conversation) {
                // Add unread messages count
                $conversation->unread_messages_count = $conversation->unreadMessagesCount(Auth::id());

                // Add other participants
                $otherParticipants = $conversation->participants->filter(function ($participant) {
                    return $participant->user_id !== Auth::id();
                })->map(function ($participant) {
                    $user = $participant->user;
                    $user->avatar_url = $user->getAvatarUrl();
                    return $user;
                })->values();

                $conversation->other_participants = $otherParticipants;

                // Remove participants to avoid duplication
                unset($conversation->participants);

                return $conversation;
            });

            return response()->json([
                'success' => true,
                'data' => $conversations,
                'message' => 'Lấy danh sách cuộc trò chuyện thành công.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi lấy danh sách cuộc trò chuyện.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a conversation by ID
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $conversation = Conversation::findOrFail($id);

            // Check if the user is a participant
            $isParticipant = $conversation->participants()->where('user_id', Auth::id())->exists();
            if (!$isParticipant) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền xem cuộc trò chuyện này.'
                ], 403);
            }

            // Load relationships
            $conversation->load(['participants.user']);

            // Add other participants
            $otherParticipants = $conversation->participants->filter(function ($participant) {
                return $participant->user_id !== Auth::id();
            })->map(function ($participant) {
                $user = $participant->user;
                $user->avatar_url = $user->getAvatarUrl();
                return $user;
            })->values();

            $conversation->other_participants = $otherParticipants;

            // Remove participants to avoid duplication
            unset($conversation->participants);

            // Get messages
            $perPage = request()->input('per_page', 15);
            $messages = $conversation->messages()
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);

            // Add user avatar URL
            $messages->getCollection()->transform(function ($message) {
                if ($message->user) {
                    $message->user->avatar_url = $message->user->getAvatarUrl();
                }
                return $message;
            });

            // Mark conversation as read
            $conversation->markAsRead(Auth::id());

            return response()->json([
                'success' => true,
                'data' => [
                    'conversation' => $conversation,
                    'messages' => $messages,
                ],
                'message' => 'Lấy thông tin cuộc trò chuyện thành công.'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy cuộc trò chuyện.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi lấy thông tin cuộc trò chuyện.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create a new conversation
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            // Validate request
            $request->validate([
                'title' => 'nullable|string|max:255',
                'participants' => 'required|array|min:1',
                'participants.*' => 'required|exists:users,id',
                'message' => 'required|string',
            ]);

            // Add current user to participants
            $participantIds = $request->participants;
            if (!in_array(Auth::id(), $participantIds)) {
                $participantIds[] = Auth::id();
            }

            // Check if conversation already exists between these users
            if (count($participantIds) === 2) {
                $existingConversation = $this->findExistingConversation($participantIds);
                if ($existingConversation) {
                    // Add new message to existing conversation
                    $message = Message::create([
                        'conversation_id' => $existingConversation->id,
                        'user_id' => Auth::id(),
                        'content' => $request->message,
                    ]);

                    // Create alerts for other participants
                    $this->createMessageAlerts($existingConversation, $message);

                    // Update conversation's updated_at
                    $existingConversation->touch();

                    return response()->json([
                        'success' => true,
                        'data' => [
                            'conversation' => $existingConversation,
                            'message' => $message,
                        ],
                        'message' => 'Tin nhắn đã được gửi đến cuộc trò chuyện hiện có.'
                    ]);
                }
            }

            // Create new conversation
            DB::beginTransaction();

            $conversation = Conversation::create([
                'title' => $request->title,
            ]);

            // Add participants
            foreach ($participantIds as $userId) {
                ConversationParticipant::create([
                    'conversation_id' => $conversation->id,
                    'user_id' => $userId,
                    'last_read_at' => $userId === Auth::id() ? now() : null,
                ]);
            }

            // Add first message
            $message = Message::create([
                'conversation_id' => $conversation->id,
                'user_id' => Auth::id(),
                'content' => $request->message,
            ]);

            // Create alerts for other participants
            $this->createMessageAlerts($conversation, $message);

            DB::commit();

            // Load relationships
            $conversation->load(['participants.user']);

            // Add other participants
            $otherParticipants = $conversation->participants->filter(function ($participant) {
                return $participant->user_id !== Auth::id();
            })->map(function ($participant) {
                $user = $participant->user;
                $user->avatar_url = $user->getAvatarUrl();
                return $user;
            })->values();

            $conversation->other_participants = $otherParticipants;

            // Remove participants to avoid duplication
            unset($conversation->participants);

            return response()->json([
                'success' => true,
                'data' => [
                    'conversation' => $conversation,
                    'message' => $message,
                ],
                'message' => 'Tạo cuộc trò chuyện mới thành công.'
            ], 201);
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi tạo cuộc trò chuyện mới.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get messages for a conversation
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMessages($id)
    {
        try {
            $conversation = Conversation::findOrFail($id);

            // Check if the user is a participant
            $isParticipant = $conversation->participants()->where('user_id', Auth::id())->exists();
            if (!$isParticipant) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền xem cuộc trò chuyện này.'
                ], 403);
            }

            // Get messages with pagination
            $messages = $conversation->messages()
                ->with('user:id,name,email,avatar')
                ->orderBy('created_at', 'asc')
                ->paginate(50);

            return response()->json([
                'success' => true,
                'data' => $messages->items(),
                'pagination' => [
                    'current_page' => $messages->currentPage(),
                    'last_page' => $messages->lastPage(),
                    'per_page' => $messages->perPage(),
                    'total' => $messages->total(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi lấy tin nhắn.',
                'error' => $e->getMessage()
            ], 500);
        }
    }



    /**
     * Send a new message to a conversation
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendMessage(Request $request, $id)
    {
        try {
            // Validate request
            $request->validate([
                'content' => 'required|string',
            ]);

            $conversation = Conversation::findOrFail($id);

            // Check if the user is a participant
            $isParticipant = $conversation->participants()->where('user_id', Auth::id())->exists();
            if (!$isParticipant) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền gửi tin nhắn đến cuộc trò chuyện này.'
                ], 403);
            }

            // Create message
            $message = Message::create([
                'conversation_id' => $conversation->id,
                'user_id' => Auth::id(),
                'content' => $request->content,
            ]);

            // Mark conversation as read for current user
            $conversation->markAsRead(Auth::id());

            // Update conversation's updated_at
            $conversation->touch();

            // Create alerts for other participants
            $this->createMessageAlerts($conversation, $message);

            // Load user
            $message->load('user');

            // Add user avatar URL
            if ($message->user) {
                $message->user->avatar_url = $message->user->getAvatarUrl();
            }

            return response()->json([
                'success' => true,
                'data' => $message,
                'message' => 'Gửi tin nhắn thành công.'
            ], 201);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy cuộc trò chuyện.'
            ], 404);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi gửi tin nhắn.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark a conversation as read
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAsRead($id)
    {
        try {
            $conversation = Conversation::findOrFail($id);

            // Check if the user is a participant
            $isParticipant = $conversation->participants()->where('user_id', Auth::id())->exists();
            if (!$isParticipant) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền đánh dấu cuộc trò chuyện này đã đọc.'
                ], 403);
            }

            // Mark conversation as read
            $conversation->markAsRead(Auth::id());

            return response()->json([
                'success' => true,
                'message' => 'Đánh dấu cuộc trò chuyện đã đọc thành công.'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy cuộc trò chuyện.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi đánh dấu cuộc trò chuyện đã đọc.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Find an existing conversation between users
     *
     * @param array $userIds
     * @return Conversation|null
     */
    private function findExistingConversation(array $userIds)
    {
        // Get all conversations where the first user is a participant
        $conversations = Conversation::whereHas('participants', function ($query) use ($userIds) {
            $query->where('user_id', $userIds[0]);
        })->get();

        // Filter conversations where all users are participants
        foreach ($conversations as $conversation) {
            $participantIds = $conversation->participants->pluck('user_id')->toArray();

            // Check if the conversation has exactly the same participants
            if (count($participantIds) === count($userIds) && empty(array_diff($userIds, $participantIds))) {
                return $conversation;
            }
        }

        return null;
    }

    /**
     * Create alerts for message recipients
     *
     * @param Conversation $conversation
     * @param Message $message
     * @return void
     */
    private function createMessageAlerts(Conversation $conversation, Message $message)
    {
        // Get other participants
        $otherParticipants = $conversation->participants->filter(function ($participant) {
            return $participant->user_id !== Auth::id();
        });

        // Get sender name
        $senderName = Auth::user()->name;

        // Create notifications for each participant
        foreach ($otherParticipants as $participant) {
            $user = \App\Models\User::find($participant->user_id);
            if ($user) {
                UnifiedNotificationService::send(
                    $user,
                    'message_received',
                    'Tin nhắn mới',
                    "{$senderName} đã gửi cho bạn một tin nhắn mới.",
                    [
                        'conversation_id' => $conversation->id,
                        'sender_id' => Auth::id(),
                        'sender_name' => $senderName,
                        'action_url' => route('conversations.show', $conversation->id),
                        'priority' => 'normal',
                        'category' => 'social'
                    ],
                    ['database'] // Only database notification
                );
            }
        }
    }
}
