<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\GroupConversationService;
use App\Services\GroupApprovalService;
use App\Services\GroupPermissionService;
use App\Models\Conversation;
use App\Models\ConversationType;
use App\Models\GroupRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BaseMessagesController extends Controller
{
    protected GroupConversationService $groupService;
    protected GroupApprovalService $approvalService;
    protected GroupPermissionService $permissionService;

    public function __construct()
    {
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
            'title' => $conversation->title ?: ($otherParticipant ? "Cuộc trò chuyện với {$otherParticipant->name}" : 'Cuộc trò chuyện'),
            'type' => 'private',
            'is_group' => false,
            'other_participant' => $otherParticipant ? [
                'id' => $otherParticipant->id,
                'name' => $otherParticipant->name,
                'avatar' => $otherParticipant->avatar ?? '/images/default-avatar.png',
                'is_online' => $otherParticipant->is_online ?? false,
            ] : null,
            'last_message' => $conversation->lastMessage ? [
                'content' => $conversation->lastMessage->content,
                'created_at' => $conversation->lastMessage->created_at,
                'user_name' => $conversation->lastMessage->user->name ?? 'Unknown',
                'is_own' => $conversation->lastMessage->user_id === Auth::id(),
            ] : null,
            'unread_count' => $conversation->unread_count,
            'created_at' => $conversation->created_at,
        ];
    }

    /**
     * Get conversation types available for current user
     */
    protected function getAvailableConversationTypes()
    {
        $user = Auth::user();

        return ConversationType::active()
                              ->availableForRole($user->role)
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
        return GroupRequest::where('creator_id', Auth::id())
                          ->with('conversationType')
                          ->orderBy('requested_at', 'desc')
                          ->get()
                          ->map(function ($request) {
                              return [
                                  'id' => $request->id,
                                  'title' => $request->title,
                                  'status' => $request->status->value,
                                  'status_label' => $request->status->getDisplayName(),
                                  'status_color' => $request->status->getColor(),
                                  'conversation_type' => $request->conversationType->name,
                                  'requested_at' => $request->requested_at,
                                  'can_edit' => $request->canBeEdited(),
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

            'pending_requests' => GroupRequest::where('creator_id', $userId)
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
}
