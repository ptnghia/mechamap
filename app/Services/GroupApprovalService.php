<?php

namespace App\Services;

use App\Models\GroupRequest;
use App\Models\ConversationType;
use App\Models\User;
use App\Enums\GroupRequestStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class GroupApprovalService
{
    protected GroupConversationService $groupService;
    protected UnifiedNotificationService $notificationService;

    public function __construct(
        GroupConversationService $groupService,
        UnifiedNotificationService $notificationService
    ) {
        $this->groupService = $groupService;
        $this->notificationService = $notificationService;
    }

    /**
     * Submit a new group request
     */
    public function submitRequest(array $data, User $creator): GroupRequest
    {
        // Validate creator role
        $conversationType = ConversationType::findOrFail($data['conversation_type_id']);
        $this->validateCreatorRole($creator, $conversationType);

        return DB::transaction(function () use ($data, $creator, $conversationType) {
            // Create request
            $request = GroupRequest::create([
                'conversation_type_id' => $data['conversation_type_id'],
                'title' => $data['title'],
                'description' => $data['description'],
                'justification' => $data['justification'] ?? null,
                'expected_members' => $data['expected_members'] ?? 10,
                'creator_id' => $creator->id,
                'status' => GroupRequestStatus::PENDING,
                'requested_at' => now(),
            ]);

            // Auto-approve if not required
            if (!$conversationType->requires_approval) {
                return $this->approveRequest($request, null, 'Auto-approved based on conversation type settings');
            }

            // Send notification to admins
            $this->notifyAdminsOfNewRequest($request);

            // Send confirmation to creator
            $this->notifyCreatorOfSubmission($request);

            Log::info('Group request submitted', [
                'request_id' => $request->id,
                'creator_id' => $creator->id,
                'type' => $conversationType->slug,
            ]);

            return $request;
        });
    }

    /**
     * Review a group request
     */
    public function reviewRequest(GroupRequest $request, User $admin, string $action, ?string $notes = null): GroupRequest
    {
        $this->validateAdminPermission($admin);
        $this->validateRequestStatus($request, $action);

        $oldStatus = $request->status;

        return DB::transaction(function () use ($request, $admin, $action, $notes, $oldStatus) {
            match($action) {
                'approve' => $this->approveRequest($request, $admin, $notes),
                'reject' => $this->rejectRequest($request, $admin, $notes),
                'need_revision' => $this->requestRevision($request, $admin, $notes),
                'under_review' => $this->markUnderReview($request, $admin),
                default => throw new Exception("Invalid action: {$action}")
            };

            // Log the action
            $this->logApprovalAction($request, $admin, $oldStatus, $action, $notes);

            return $request->fresh();
        });
    }

    /**
     * Approve a group request
     */
    private function approveRequest(GroupRequest $request, ?User $admin, ?string $notes): GroupRequest
    {
        $request->update([
            'status' => GroupRequestStatus::APPROVED,
            'reviewed_by' => $admin?->id,
            'reviewed_at' => now(),
            'admin_notes' => $notes,
        ]);

        // Create the actual group conversation
        $conversation = $this->groupService->createGroupFromRequest($request);

        // Notify creator
        $this->notifyCreatorOfApproval($request, $conversation);

        Log::info('Group request approved', [
            'request_id' => $request->id,
            'conversation_id' => $conversation->id,
            'admin_id' => $admin?->id,
        ]);

        return $request;
    }

    /**
     * Reject a group request
     */
    private function rejectRequest(GroupRequest $request, User $admin, ?string $reason): GroupRequest
    {
        $request->update([
            'status' => GroupRequestStatus::REJECTED,
            'reviewed_by' => $admin->id,
            'reviewed_at' => now(),
            'rejection_reason' => $reason,
            'admin_notes' => $reason,
        ]);

        // Notify creator
        $this->notifyCreatorOfRejection($request);

        Log::info('Group request rejected', [
            'request_id' => $request->id,
            'admin_id' => $admin->id,
            'reason' => $reason,
        ]);

        return $request;
    }

    /**
     * Request revision for a group request
     */
    private function requestRevision(GroupRequest $request, User $admin, ?string $feedback): GroupRequest
    {
        $request->update([
            'status' => GroupRequestStatus::NEEDS_REVISION,
            'reviewed_by' => $admin->id,
            'reviewed_at' => now(),
            'admin_notes' => $feedback,
        ]);

        // Notify creator
        $this->notifyCreatorOfRevisionRequest($request);

        Log::info('Group request needs revision', [
            'request_id' => $request->id,
            'admin_id' => $admin->id,
            'feedback' => $feedback,
        ]);

        return $request;
    }

    /**
     * Mark request as under review
     */
    private function markUnderReview(GroupRequest $request, User $admin): GroupRequest
    {
        $request->update([
            'status' => GroupRequestStatus::UNDER_REVIEW,
            'reviewed_by' => $admin->id,
            'reviewed_at' => now(),
        ]);

        Log::info('Group request under review', [
            'request_id' => $request->id,
            'admin_id' => $admin->id,
        ]);

        return $request;
    }

    /**
     * Update an existing request (for revision)
     */
    public function updateRequest(GroupRequest $request, array $data, User $creator): GroupRequest
    {
        // Validate that request can be edited
        if (!$request->canBeEdited()) {
            throw new Exception('Request không thể chỉnh sửa ở trạng thái hiện tại');
        }

        // Validate creator
        if ($request->creator_id !== $creator->id) {
            throw new Exception('Chỉ người tạo request mới có thể chỉnh sửa');
        }

        return DB::transaction(function () use ($request, $data) {
            $request->update([
                'title' => $data['title'] ?? $request->title,
                'description' => $data['description'] ?? $request->description,
                'justification' => $data['justification'] ?? $request->justification,
                'expected_members' => $data['expected_members'] ?? $request->expected_members,
                'status' => GroupRequestStatus::PENDING, // Reset to pending after revision
                'reviewed_by' => null,
                'reviewed_at' => null,
                'admin_notes' => null,
                'rejection_reason' => null,
            ]);

            // Notify admins of updated request
            $this->notifyAdminsOfUpdatedRequest($request);

            Log::info('Group request updated', [
                'request_id' => $request->id,
                'creator_id' => $request->creator_id,
            ]);

            return $request;
        });
    }

    /**
     * Get pending requests for admin review
     */
    public function getPendingRequests(int $limit = 10): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return GroupRequest::with(['creator', 'conversationType'])
                          ->whereIn('status', [
                              GroupRequestStatus::PENDING,
                              GroupRequestStatus::UNDER_REVIEW,
                              GroupRequestStatus::NEEDS_REVISION
                          ])
                          ->orderBy('requested_at', 'asc')
                          ->paginate($limit);
    }

    /**
     * Get request statistics
     */
    public function getRequestStatistics(): array
    {
        $stats = [
            'total' => GroupRequest::count(),
            'pending' => GroupRequest::where('status', GroupRequestStatus::PENDING)->count(),
            'under_review' => GroupRequest::where('status', GroupRequestStatus::UNDER_REVIEW)->count(),
            'approved' => GroupRequest::where('status', GroupRequestStatus::APPROVED)->count(),
            'rejected' => GroupRequest::where('status', GroupRequestStatus::REJECTED)->count(),
            'needs_revision' => GroupRequest::where('status', GroupRequestStatus::NEEDS_REVISION)->count(),
        ];

        $stats['approval_rate'] = $stats['total'] > 0
            ? round(($stats['approved'] / $stats['total']) * 100, 2)
            : 0;

        return $stats;
    }

    /**
     * Validate creator role
     */
    private function validateCreatorRole(User $creator, ConversationType $conversationType): void
    {
        if (!$conversationType->canBeCreatedBy($creator->role)) {
            throw new Exception("Role '{$creator->role}' không được phép tạo loại group này");
        }
    }

    /**
     * Validate admin permission
     */
    private function validateAdminPermission(User $admin): void
    {
        $allowedRoles = ['super_admin', 'system_admin', 'content_admin'];

        if (!in_array($admin->role, $allowedRoles)) {
            throw new Exception('Bạn không có quyền duyệt group requests');
        }
    }

    /**
     * Validate request status for action
     */
    private function validateRequestStatus(GroupRequest $request, string $action): void
    {
        $validTransitions = [
            'approve' => [GroupRequestStatus::PENDING, GroupRequestStatus::UNDER_REVIEW, GroupRequestStatus::NEEDS_REVISION],
            'reject' => [GroupRequestStatus::PENDING, GroupRequestStatus::UNDER_REVIEW, GroupRequestStatus::NEEDS_REVISION],
            'need_revision' => [GroupRequestStatus::PENDING, GroupRequestStatus::UNDER_REVIEW],
            'under_review' => [GroupRequestStatus::PENDING, GroupRequestStatus::NEEDS_REVISION],
        ];

        if (!isset($validTransitions[$action]) || !in_array($request->status, $validTransitions[$action])) {
            throw new Exception("Không thể thực hiện action '{$action}' với status hiện tại");
        }
    }

    /**
     * Log approval action
     */
    private function logApprovalAction(GroupRequest $request, User $admin, GroupRequestStatus $oldStatus, string $action, ?string $notes): void
    {
        Log::info('Group request action performed', [
            'request_id' => $request->id,
            'admin_id' => $admin->id,
            'old_status' => $oldStatus->value,
            'new_status' => $request->status->value,
            'action' => $action,
            'notes' => $notes,
        ]);
    }

    /**
     * Notify admins of new request
     */
    private function notifyAdminsOfNewRequest(GroupRequest $request): void
    {
        $admins = User::whereIn('role', ['super_admin', 'system_admin', 'content_admin'])->get();

        foreach ($admins as $admin) {
            $this->notificationService->send(
                $admin,
                'group_request_submitted',
                'Yêu cầu tạo group mới',
                "Có yêu cầu tạo group '{$request->title}' từ {$request->creator->name}",
                [
                    'request_id' => $request->id,
                    'creator_name' => $request->creator->name,
                    'group_title' => $request->title,
                    'conversation_type' => $request->conversationType->name,
                    'action_url' => route('admin.groups.requests.show', $request->id),
                ],
                ['database', 'mail']
            );
        }
    }

    /**
     * Notify creator of submission
     */
    private function notifyCreatorOfSubmission(GroupRequest $request): void
    {
        $this->notificationService->send(
            $request->creator,
            'group_request_submitted_confirmation',
            'Yêu cầu tạo group đã được gửi',
            "Yêu cầu tạo group '{$request->title}' đã được gửi và đang chờ duyệt",
            [
                'request_id' => $request->id,
                'group_title' => $request->title,
                'status' => $request->status->getDisplayName(),
                'action_url' => route('dashboard.messages.groups.request.status', $request->id),
            ],
            ['database']
        );
    }

    /**
     * Notify creator of approval
     */
    private function notifyCreatorOfApproval(GroupRequest $request, $conversation): void
    {
        $this->notificationService->send(
            $request->creator,
            'group_request_approved',
            'Yêu cầu tạo group đã được duyệt',
            "Group '{$request->title}' của bạn đã được duyệt và tạo thành công!",
            [
                'request_id' => $request->id,
                'conversation_id' => $conversation->id,
                'group_title' => $request->title,
                'action_url' => route('dashboard.messages.show', $conversation->id),
            ],
            ['database', 'mail']
        );
    }

    /**
     * Notify creator of rejection
     */
    private function notifyCreatorOfRejection(GroupRequest $request): void
    {
        $this->notificationService->send(
            $request->creator,
            'group_request_rejected',
            'Yêu cầu tạo group bị từ chối',
            "Yêu cầu tạo group '{$request->title}' đã bị từ chối",
            [
                'request_id' => $request->id,
                'group_title' => $request->title,
                'rejection_reason' => $request->rejection_reason,
                'action_url' => route('dashboard.messages.groups.request.status', $request->id),
            ],
            ['database', 'mail']
        );
    }

    /**
     * Notify creator of revision request
     */
    private function notifyCreatorOfRevisionRequest(GroupRequest $request): void
    {
        $this->notificationService->send(
            $request->creator,
            'group_request_needs_revision',
            'Yêu cầu tạo group cần chỉnh sửa',
            "Yêu cầu tạo group '{$request->title}' cần được chỉnh sửa",
            [
                'request_id' => $request->id,
                'group_title' => $request->title,
                'admin_notes' => $request->admin_notes,
                'action_url' => route('dashboard.messages.groups.request.edit', $request->id),
            ],
            ['database', 'mail']
        );
    }

    /**
     * Notify admins of updated request
     */
    private function notifyAdminsOfUpdatedRequest(GroupRequest $request): void
    {
        $admins = User::whereIn('role', ['super_admin', 'system_admin', 'content_admin'])->get();

        foreach ($admins as $admin) {
            $this->notificationService->send(
                $admin,
                'group_request_updated',
                'Yêu cầu tạo group đã được cập nhật',
                "Yêu cầu tạo group '{$request->title}' đã được cập nhật bởi {$request->creator->name}",
                [
                    'request_id' => $request->id,
                    'creator_name' => $request->creator->name,
                    'group_title' => $request->title,
                    'action_url' => route('admin.groups.requests.show', $request->id),
                ],
                ['database']
            );
        }
    }
}
