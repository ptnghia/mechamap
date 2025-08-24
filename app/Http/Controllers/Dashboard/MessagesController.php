<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use App\Services\GroupConversationService;
use App\Services\GroupApprovalService;
use App\Services\GroupPermissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

class MessagesController extends BaseController
{
    protected $groupService;
    protected $approvalService;
    protected $permissionService;

    public function __construct()
    {
        parent::__construct();
        $this->groupService = app(GroupConversationService::class);
        $this->approvalService = app(GroupApprovalService::class);
        $this->permissionService = app(GroupPermissionService::class);
    }

    /**
     * Get dashboard-specific conversation data
     */
    protected function getDashboardConversations()
    {
        $conversations = Conversation::whereHas('participants', function ($query) {
            $query->where('user_id', Auth::id());
        })
        ->orWhereHas('members', function ($query) {
            $query->where('user_id', Auth::id())->where('is_active', true);
        })
        ->with([
            'participants.user:id,name,email,avatar',
            'members.user:id,name,email,avatar',
            'lastMessage:id,conversation_id,user_id,content,created_at',
            'conversationType:id,name,slug'
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

        return $conversations->map(function ($conversation) {
            if ($conversation->is_group) {
                return $this->transformGroupConversation($conversation);
            } else {
                return $this->transformPrivateConversation($conversation);
            }
        });
    }

    /**
     * Transform group conversation for dashboard view
     */
    protected function transformGroupConversation($conversation)
    {
        $userMember = $conversation->members->where('user_id', Auth::id())->first();
        $memberCount = $conversation->activeMembers()->count();

        return [
            'id' => $conversation->id,
            'title' => $conversation->title,
            'type' => 'group',
            'is_group' => true,
            'conversation_type' => $conversation->conversationType?->name,
            'member_count' => $memberCount,
            'max_members' => $conversation->max_members,
            'user_role' => $userMember?->role?->getDisplayName(),
            'user_role_color' => $userMember?->role?->getColor(),
            'user_permissions' => $this->permissionService->getUserPermissions(Auth::user(), $conversation),
            'last_message' => $conversation->lastMessage ? [
                'content' => $conversation->lastMessage->content,
                'created_at' => $conversation->lastMessage->created_at,
                'user_name' => $conversation->lastMessage->user->name ?? 'System',
                'is_system' => $conversation->lastMessage->is_system_message,
            ] : null,
            'unread_count' => $conversation->unread_count,
            'is_public' => $conversation->is_public,
            'created_at' => $conversation->created_at,
        ];
    }

    /**
     * Transform private conversation for dashboard view
     */
    protected function transformPrivateConversation($conversation)
    {
        $otherParticipant = $conversation->participants
            ->where('user_id', '!=', Auth::id())
            ->first()?->user;

        return [
            'id' => $conversation->id,
            'title' => $conversation->title ?: ($otherParticipant?->name ?? 'Unknown User'),
            'type' => 'private',
            'is_group' => false,
            'other_participant' => [
                'id' => $otherParticipant?->id,
                'name' => $otherParticipant?->name ?? 'Unknown User',
                'email' => $otherParticipant?->email,
                'avatar' => $otherParticipant?->avatar ?? '/images/default-avatar.png',
                'is_online' => $otherParticipant?->isOnline() ?? false,
            ],
            'last_message' => $conversation->lastMessage ? [
                'content' => $conversation->lastMessage->content,
                'created_at' => $conversation->lastMessage->created_at,
                'user_name' => $conversation->lastMessage->user->name ?? 'System',
                'is_system' => $conversation->lastMessage->is_system_message,
            ] : null,
            'unread_count' => $conversation->unread_count,
            'created_at' => $conversation->created_at,
        ];
    }

    /**
     * Display messages dashboard
     */
    public function index(Request $request)
    {
        $conversations = $this->getDashboardConversations();
        $stats = $this->getDashboardStats();
        $conversationTypes = $this->getAvailableConversationTypes();
        $groupRequests = $this->getUserGroupRequests();
        $canCreateGroupConversations = $this->canCreateGroupConversations();

        // Filter conversations if requested
        $filter = $request->get('filter', 'all');
        if ($filter === 'groups') {
            $conversations = $conversations->where('is_group', true);
        } elseif ($filter === 'private') {
            $conversations = $conversations->where('is_group', false);
        } elseif ($filter === 'unread') {
            $conversations = $conversations->where('unread_count', '>', 0);
        }

        // Search conversations
        $search = $request->get('search');
        if ($search) {
            $conversations = $conversations->filter(function ($conv) use ($search) {
                return stripos($conv['title'], $search) !== false;
            });
        }

        return $this->dashboardResponse('dashboard.messages.index', compact(
            'conversations',
            'stats',
            'conversationTypes',
            'groupRequests',
            'canCreateGroupConversations',
            'filter',
            'search'
        ));
    }

    /**
     * Show specific conversation
     */
    public function show(Request $request, $id)
    {
        $conversation = Conversation::with([
            'participants.user',
            'members.user',
            'conversationType',
            'messages' => function ($query) {
                $query->with('user:id,name,avatar')
                      ->orderBy('created_at', 'asc')
                      ->limit(50); // Load last 50 messages
            }
        ])->findOrFail($id);

        // Check if user has access to this conversation
        if (!$conversation->hasMember(Auth::id()) &&
            !$conversation->participants()->where('user_id', Auth::id())->exists()) {
            abort(403, 'Bạn không có quyền truy cập cuộc trò chuyện này');
        }

        // Mark as read
        $conversation->markAsRead(Auth::id());

        // Get conversation data for sidebar
        $conversations = $this->getDashboardConversations();

        // Get user permissions for this conversation
        $userPermissions = [];
        if ($conversation->is_group) {
            $userPermissions = $this->permissionService->getUserPermissions(Auth::user(), $conversation);
        }

        // Get group members if it's a group conversation
        $groupMembers = [];
        if ($conversation->is_group) {
            $groupMembers = $conversation->activeMembers()
                                       ->with('user:id,name,email,avatar')
                                       ->get()
                                       ->map(function ($member) {
                                           return [
                                               'id' => $member->user->id,
                                               'name' => $member->user->name,
                                               'email' => $member->user->email,
                                               'avatar' => $member->user->avatar ?? '/images/default-avatar.png',
                                               'role' => $member->role->value,
                                               'role_label' => $member->role->getDisplayName(),
                                               'role_color' => $member->role->getColor(),
                                               'joined_at' => $member->joined_at,
                                               'is_online' => $member->user->is_online ?? false,
                                           ];
                                       });
        }

        // Get other participant for private conversations
        $otherParticipant = null;
        if (!$conversation->is_group) {
            $otherParticipant = $conversation->participants
                                           ->where('user_id', '!=', Auth::id())
                                           ->first()?->user;
        }

        return $this->dashboardResponse('dashboard.messages.show', compact(
            'conversation',
            'conversations',
            'userPermissions',
            'groupMembers',
            'otherParticipant'
        ));
    }

    /**
     * Create new conversation form
     */
    public function create()
    {
        $users = User::where('id', '!=', Auth::id())
                    ->select('id', 'name', 'email', 'avatar')
                    ->orderBy('name')
                    ->get();

        $conversationTypes = $this->getAvailableConversationTypes();

        return $this->dashboardResponse('dashboard.messages.create', compact('users', 'conversationTypes'));
    }

    /**
     * Store new conversation
     */
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:private,group',
            'recipient_id' => 'required_if:type,private|exists:users,id',
            'title' => 'required_if:type,private|string|max:255',
            'message' => 'required|string|max:1000',
        ]);

        if ($request->type === 'private') {
            return $this->createPrivateConversation($request);
        } else {
            return $this->createGroupRequest($request);
        }
    }

    /**
     * Send message to conversation
     */
    public function sendMessage(Request $request, $id)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $conversation = Conversation::findOrFail($id);

        // Check if user has access
        if (!$conversation->hasMember(Auth::id()) &&
            !$conversation->participants()->where('user_id', Auth::id())->exists()) {
            abort(403, 'Bạn không có quyền gửi tin nhắn trong cuộc trò chuyện này');
        }

        // Check if user can send messages (for groups)
        if ($conversation->is_group) {
            if (!$this->permissionService->hasPermission(Auth::user(), $conversation, 'send_messages')) {
                abort(403, 'Bạn không có quyền gửi tin nhắn trong group này');
            }
        }

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'user_id' => Auth::id(),
            'content' => $request->content,
            'is_system_message' => false,
        ]);

        // Update conversation timestamp
        $conversation->touch();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => [
                    'id' => $message->id,
                    'content' => $message->content,
                    'user_name' => Auth::user()->name,
                    'created_at' => $message->created_at->format('H:i'),
                    'is_own' => true,
                ]
            ]);
        }

        return redirect()->route('dashboard.messages.show', $conversation->id);
    }

    /**
     * Get messages for conversation (AJAX)
     */
    public function getMessages(Request $request, $id): JsonResponse
    {
        $conversation = Conversation::findOrFail($id);

        // Check access
        if (!$conversation->hasMember(Auth::id()) &&
            !$conversation->participants()->where('user_id', Auth::id())->exists()) {
            abort(403);
        }

        $messages = $conversation->messages()
                                ->with('user:id,name,avatar')
                                ->orderBy('created_at', 'desc')
                                ->limit(50)
                                ->get()
                                ->reverse()
                                ->values();

        return response()->json([
            'success' => true,
            'messages' => $messages->map(function ($message) {
                return [
                    'id' => $message->id,
                    'content' => $message->content,
                    'user_id' => $message->user_id,
                    'user_name' => $message->user->name ?? 'System',
                    'user_avatar' => $message->user->avatar ?? '/images/default-avatar.png',
                    'is_system_message' => $message->is_system_message,
                    'system_message_type' => $message->system_message_type,
                    'created_at' => $message->created_at->format('H:i'),
                    'created_at_full' => $message->created_at->format('d/m/Y H:i'),
                    'is_own' => $message->user_id === Auth::id(),
                ];
            })
        ]);
    }

    /**
     * Create private conversation
     */
    private function createPrivateConversation(Request $request)
    {
        $recipientId = $request->recipient_id;

        // Check if conversation already exists
        $existingConversation = Conversation::where('is_group', false)
            ->whereHas('participants', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->whereHas('participants', function ($query) use ($recipientId) {
                $query->where('user_id', $recipientId);
            })
            ->first();

        if ($existingConversation) {
            // Add message to existing conversation
            Message::create([
                'conversation_id' => $existingConversation->id,
                'user_id' => Auth::id(),
                'content' => $request->message,
            ]);

            return redirect()->route('dashboard.messages.show', $existingConversation->id)
                           ->with('success', 'Tin nhắn đã được gửi');
        }

        // Create new conversation
        return DB::transaction(function () use ($request, $recipientId) {
            $conversation = Conversation::create([
                'title' => $request->title,
                'is_group' => false,
                'max_members' => 2,
            ]);

            // Add participants
            $conversation->participants()->createMany([
                ['user_id' => Auth::id()],
                ['user_id' => $recipientId],
            ]);

            // Add first message
            Message::create([
                'conversation_id' => $conversation->id,
                'user_id' => Auth::id(),
                'content' => $request->message,
            ]);

            return redirect()->route('dashboard.messages.show', $conversation->id)
                           ->with('success', 'Cuộc trò chuyện đã được tạo');
        });
    }

    /**
     * Create group request (will be handled by GroupConversationController)
     */
    private function createGroupRequest(Request $request)
    {
        return redirect()->route('dashboard.messages.groups.create')
                       ->with('info', 'Vui lòng sử dụng form tạo group để tạo cuộc trò chuyện nhóm');
    }

    /**
     * Get conversation types available for current user
     */
    protected function getAvailableConversationTypes()
    {
        $user = Auth::user();

        return \App\Models\ConversationType::where('is_active', true)
                              ->whereJsonContains('created_by_roles', $user->role)
                              ->get()
                              ->map(function ($type) {
                                  return [
                                      'id' => $type->id,
                                      'name' => $type->name,
                                      'slug' => $type->slug,
                                      'description' => $type->description,
                                      'max_members' => $type->max_members,
                                      'requires_approval' => $type->requires_approval,
                                  ];
                              });
    }

    /**
     * Get user's group requests
     */
    protected function getUserGroupRequests()
    {
        return \App\Models\GroupRequest::where('creator_id', Auth::id())
                          ->with('conversationType')
                          ->orderBy('requested_at', 'desc')
                          ->get()
                          ->map(function ($request) {
                              return [
                                  'id' => $request->id,
                                  'title' => $request->title,
                                  'status' => $request->status->value ?? $request->status,
                                  'status_label' => ucfirst(str_replace('_', ' ', $request->status->value ?? $request->status)),
                                  'status_color' => $this->getStatusColor($request->status->value ?? $request->status),
                                  'conversation_type' => $request->conversationType->name,
                                  'requested_at' => $request->requested_at,
                                  'can_edit' => in_array($request->status->value ?? $request->status, ['pending', 'needs_revision']),
                              ];
                          });
    }

    /**
     * Get dashboard statistics
     */
    protected function getDashboardStats()
    {
        $userId = Auth::id();

        return [
            'total_conversations' => Conversation::whereHas('participants', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })->orWhereHas('members', function ($query) use ($userId) {
                $query->where('user_id', $userId)->where('is_active', true);
            })->count(),

            'group_conversations' => Conversation::where('is_group', true)
                                               ->whereHas('members', function ($query) use ($userId) {
                                                   $query->where('user_id', $userId)->where('is_active', true);
                                               })->count(),

            'private_conversations' => Conversation::where('is_group', false)
                                                 ->whereHas('participants', function ($query) use ($userId) {
                                                     $query->where('user_id', $userId);
                                                 })->count(),

            'unread_messages' => $this->getTotalUnreadCount(),

            'pending_requests' => \App\Models\GroupRequest::where('creator_id', $userId)
                                            ->whereIn('status', ['pending', 'under_review', 'needs_revision'])
                                            ->count(),
        ];
    }

    /**
     * Get total unread message count
     */
    protected function getTotalUnreadCount()
    {
        return Conversation::whereHas('participants', function ($query) {
            $query->where('user_id', Auth::id());
        })
        ->orWhereHas('members', function ($query) {
            $query->where('user_id', Auth::id())->where('is_active', true);
        })
        ->withCount(['messages as unread_count' => function ($query) {
            $query->whereHas('conversation.participants', function ($q) {
                $q->where('user_id', Auth::id())
                  ->where(function ($q2) {
                      $q2->whereNull('last_read_at')
                         ->orWhereColumn('messages.created_at', '>', 'conversation_participants.last_read_at');
                  });
            });
        }])
        ->get()
        ->sum('unread_count');
    }

    /**
     * Check if user can create group conversations
     */
    protected function canCreateGroupConversations()
    {
        $user = Auth::user();
        $allowedRoles = ['member', 'senior_member', 'verified_partner', 'manufacturer', 'supplier'];

        return in_array($user->role, $allowedRoles);
    }

    /**
     * Get status color for group request
     */
    protected function getStatusColor($status)
    {
        return match($status) {
            'pending' => 'warning',
            'under_review' => 'info',
            'approved' => 'success',
            'rejected' => 'danger',
            'needs_revision' => 'warning',
            default => 'secondary'
        };
    }
}
