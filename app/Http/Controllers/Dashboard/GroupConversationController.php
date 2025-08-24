<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Conversation;
use App\Models\ConversationType;
use App\Models\GroupRequest;
use App\Models\User;
use App\Enums\GroupRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

class GroupConversationController extends BaseController
{
    /**
     * Show group conversations
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Get user's group conversations
        $groupConversations = Conversation::where('is_group', true)
            ->whereHas('members', function ($query) use ($user) {
                $query->where('user_id', $user->id)->where('is_active', true);
            })
            ->with(['conversationType', 'members.user', 'lastMessage'])
            ->orderBy('updated_at', 'desc')
            ->paginate(20);

        // Get user's group requests
        $groupRequests = $this->getUserGroupRequests();

        // Get available conversation types
        $conversationTypes = $this->getAvailableConversationTypes();

        $stats = [
            'total_groups' => $groupConversations->total(),
            'active_groups' => $groupConversations->where('is_active', true)->count(),
            'pending_requests' => $groupRequests->where('status', 'pending')->count(),
            'approved_requests' => $groupRequests->where('status', 'approved')->count(),
        ];

        return $this->dashboardResponse('dashboard.messages.groups.index', compact(
            'groupConversations',
            'groupRequests',
            'conversationTypes',
            'stats'
        ));
    }

    /**
     * Show create group form
     */
    public function create()
    {
        if (!$this->canCreateGroupConversations()) {
            abort(403, 'Bạn không có quyền tạo group conversations');
        }

        $conversationTypes = $this->getAvailableConversationTypes();
        $groupRequests = $this->getUserGroupRequests();

        // Get users that can be invited to groups
        $availableUsers = User::where('id', '!=', Auth::id())
            ->whereIn('role', ['member', 'senior_member', 'verified_partner', 'manufacturer', 'supplier'])
            ->select('id', 'name', 'email', 'role', 'avatar')
            ->orderBy('name')
            ->get();

        return $this->dashboardResponse('dashboard.messages.groups.create', compact('conversationTypes', 'groupRequests', 'availableUsers'));
    }

    /**
     * Submit group request
     */
    public function submitRequest(Request $request)
    {
        if (!$this->canCreateGroupConversations()) {
            abort(403, 'Bạn không có quyền tạo group conversations');
        }

        $request->validate([
            'conversation_type_id' => 'required|exists:conversation_types,id',
            'title' => 'required|string|max:200',
            'description' => 'required|string|max:1000',
            'justification' => 'nullable|string|max:500',
            'expected_members' => 'required|integer|min:3|max:100',
        ]);

        try {
            $groupRequest = $this->approvalService->submitRequest($request->all(), Auth::user());

            $message = $groupRequest->status->value === 'approved'
                ? 'Group đã được tạo thành công!'
                : 'Yêu cầu tạo group đã được gửi và đang chờ duyệt';

            return redirect()->route('dashboard.messages.groups.create')
                           ->with('success', $message);

        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    /**
     * Show group settings
     */
    public function settings(Request $request, $id)
    {
        $conversation = Conversation::with(['conversationType', 'members.user'])
                                  ->where('is_group', true)
                                  ->findOrFail($id);

        // Check if user can manage group
        if (!$this->permissionService->hasPermission(Auth::user(), $conversation, 'change_settings')) {
            abort(403, 'Bạn không có quyền quản lý group này');
        }

        $userPermissions = $this->permissionService->getUserPermissions(Auth::user(), $conversation);
        $availableRoles = $this->permissionService->getAvailableRoles(Auth::user(), $conversation);
        $memberStats = $this->permissionService->getMemberStatistics($conversation);

        return view('dashboard.messages.groups.settings', compact(
            'conversation',
            'userPermissions',
            'availableRoles',
            'memberStats'
        ));
    }

    /**
     * Update group settings
     */
    public function updateSettings(Request $request, $id)
    {
        $conversation = Conversation::where('is_group', true)->findOrFail($id);

        if (!$this->permissionService->hasPermission(Auth::user(), $conversation, 'change_settings')) {
            abort(403, 'Bạn không có quyền quản lý group này');
        }

        $request->validate([
            'title' => 'required|string|max:200',
            'group_description' => 'nullable|string|max:1000',
            'group_rules' => 'nullable|string|max:2000',
            'is_public' => 'boolean',
        ]);

        try {
            $this->groupService->updateGroupSettings($conversation, $request->only([
                'title', 'group_description', 'group_rules', 'is_public'
            ]));

            return back()->with('success', 'Cài đặt group đã được cập nhật');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Add member to group
     */
    public function addMember(Request $request, $id): JsonResponse
    {
        $conversation = Conversation::where('is_group', true)->findOrFail($id);

        if (!$this->permissionService->hasPermission(Auth::user(), $conversation, 'invite_members')) {
            abort(403, 'Bạn không có quyền mời thành viên');
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|in:member,moderator,admin',
        ]);

        try {
            $role = GroupRole::from($request->role);

            // Check if user can assign this role
            if (!$this->permissionService->canPromoteToRole(Auth::user(), $role, $conversation)) {
                return response()->json(['error' => 'Bạn không có quyền gán role này'], 403);
            }

            $member = $this->groupService->addMember(
                $conversation,
                $request->user_id,
                $role,
                Auth::id()
            );

            return response()->json([
                'success' => true,
                'message' => 'Thành viên đã được thêm vào group',
                'member' => [
                    'id' => $member->user->id,
                    'name' => $member->user->name,
                    'role' => $member->role->getDisplayName(),
                    'role_color' => $member->role->getColor(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Remove member from group
     */
    public function removeMember(Request $request, $id, $userId): JsonResponse
    {
        $conversation = Conversation::where('is_group', true)->findOrFail($id);
        $targetUser = User::findOrFail($userId);

        if (!$this->permissionService->canRemoveMember(Auth::user(), $targetUser, $conversation)) {
            abort(403, 'Bạn không có quyền xóa thành viên này');
        }

        try {
            $this->groupService->removeMember($conversation, $userId, Auth::id());

            return response()->json([
                'success' => true,
                'message' => 'Thành viên đã được xóa khỏi group'
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Change member role
     */
    public function changeMemberRole(Request $request, $id, $userId): JsonResponse
    {
        $conversation = Conversation::where('is_group', true)->findOrFail($id);
        $targetUser = User::findOrFail($userId);

        $request->validate([
            'role' => 'required|in:member,moderator,admin',
        ]);

        $newRole = GroupRole::from($request->role);

        if (!$this->permissionService->canPromoteToRole(Auth::user(), $newRole, $conversation)) {
            abort(403, 'Bạn không có quyền gán role này');
        }

        try {
            $this->groupService->changeMemberRole($conversation, $userId, $newRole, Auth::id());

            return response()->json([
                'success' => true,
                'message' => 'Role thành viên đã được cập nhật',
                'new_role' => $newRole->getDisplayName(),
                'new_role_color' => $newRole->getColor(),
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Leave group
     */
    public function leaveGroup(Request $request, $id): JsonResponse
    {
        $conversation = Conversation::where('is_group', true)->findOrFail($id);

        if (!$this->permissionService->hasPermission(Auth::user(), $conversation, 'leave_group')) {
            abort(403, 'Bạn không thể rời khỏi group này');
        }

        try {
            $this->groupService->removeMember($conversation, Auth::id());

            return response()->json([
                'success' => true,
                'message' => 'Bạn đã rời khỏi group',
                'redirect' => route('dashboard.messages.groups.index')
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Transfer ownership
     */
    public function transferOwnership(Request $request, $id): JsonResponse
    {
        $conversation = Conversation::where('is_group', true)->findOrFail($id);

        if (!$conversation->isCreator(Auth::id())) {
            abort(403, 'Chỉ creator mới có thể transfer ownership');
        }

        $request->validate([
            'new_owner_id' => 'required|exists:users,id',
        ]);

        try {
            $this->groupService->transferOwnership(
                $conversation,
                $request->new_owner_id,
                Auth::id()
            );

            return response()->json([
                'success' => true,
                'message' => 'Quyền sở hữu group đã được chuyển'
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Search users for invitation
     */
    public function searchUsers(Request $request): JsonResponse
    {
        $query = $request->get('q', '');

        if (strlen($query) < 2) {
            return response()->json(['users' => []]);
        }

        $users = User::where('name', 'like', "%{$query}%")
                    ->orWhere('email', 'like', "%{$query}%")
                    ->where('id', '!=', Auth::id())
                    ->select('id', 'name', 'email', 'avatar')
                    ->limit(10)
                    ->get();

        return response()->json([
            'users' => $users->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'avatar' => $user->avatar ?? '/images/default-avatar.png',
                ];
            })
        ]);
    }

    /**
     * Get conversation types available for current user
     */
    protected function getAvailableConversationTypes()
    {
        $user = Auth::user();

        return ConversationType::where('is_active', true)
                              ->whereJsonContains('created_by_roles', $user->role)
                              ->get();
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
                              $status = $request->status instanceof \App\Enums\GroupRequestStatus
                                      ? $request->status->value
                                      : $request->status;

                              return [
                                  'id' => $request->id,
                                  'title' => $request->title,
                                  'description' => $request->description,
                                  'status' => $status,
                                  'status_label' => $request->status instanceof \App\Enums\GroupRequestStatus
                                                  ? $request->status->getDisplayName()
                                                  : ucfirst(str_replace('_', ' ', $status)),
                                  'status_color' => $this->getStatusColor($status),
                                  'conversation_type' => $request->conversationType->name ?? 'N/A',
                                  'requested_at' => $request->requested_at,
                                  'can_edit' => in_array($status, ['pending', 'needs_revision']),
                              ];
                          });
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
