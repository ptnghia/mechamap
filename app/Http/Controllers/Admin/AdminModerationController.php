<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Thread;
use App\Models\Comment;
use App\Models\User;
use App\Models\Forum;
use App\Services\ModerationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Controller xử lý admin moderation
 * Quản lý trạng thái moderation cho threads và comments
 */
class AdminModerationController extends Controller
{
    protected $moderationService;

    public function __construct(ModerationService $moderationService)
    {
        $this->middleware(['auth', 'role:admin,moderator']);
        $this->moderationService = $moderationService;
    }

    /**
     * Dashboard moderation với statistics
     */
    public function dashboard()
    {
        $stats = $this->moderationService->getModerationStatistics();

        // Recent activity
        $recentThreads = Thread::with(['user', 'forum'])
            ->whereIn('moderation_status', ['pending', 'flagged'])
            ->latest()
            ->limit(10)
            ->get();

        $recentComments = Comment::with(['user', 'thread'])
            ->whereIn('moderation_status', ['pending', 'flagged'])
            ->latest()
            ->limit(10)
            ->get();

        // Today's moderation activity
        $todayActivity = [
            'approved_threads' => Thread::whereDate('updated_at', today())
                ->where('moderation_status', 'approved')
                ->count(),
            'rejected_threads' => Thread::whereDate('updated_at', today())
                ->where('moderation_status', 'rejected')
                ->count(),
            'approved_comments' => Comment::whereDate('updated_at', today())
                ->where('moderation_status', 'approved')
                ->count(),
            'rejected_comments' => Comment::whereDate('updated_at', today())
                ->where('moderation_status', 'rejected')
                ->count(),
        ];

        return view('admin.moderation.dashboard', compact(
            'stats',
            'recentThreads',
            'recentComments',
            'todayActivity'
        ));
    }

    /**
     * Hiển thị threads cần moderation
     */
    public function threads(Request $request)
    {
        $status = $request->status ?? 'pending';
        $perPage = $request->per_page ?? 20;

        $query = $this->moderationService->getThreadsForModeration()
            ->with(['user', 'forum', 'tags'])
            ->withCount(['comments', 'reports']);

        // Filter theo status
        if ($status !== 'all') {
            $query->where('moderation_status', $status);
        }

        // Filter theo forum
        if ($request->forum_id) {
            $query->where('forum_id', $request->forum_id);
        }

        // Filter theo user
        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        // Filter theo thread type
        if ($request->thread_type) {
            $query->where('thread_type', $request->thread_type);
        }

        // Search
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('content', 'like', '%' . $request->search . '%');
            });
        }

        // Sorting
        switch ($request->sort) {
            case 'oldest':
                $query->oldest();
                break;
            case 'reports':
                $query->orderByDesc('reports_count');
                break;
            case 'user':
                $query->join('users', 'threads.user_id', '=', 'users.id')
                    ->orderBy('users.name');
                break;
            default:
                $query->latest();
        }

        $threads = $query->paginate($perPage);

        // Sidebar data
        $forums = Forum::withCount(['threads' => function ($q) {
            $q->whereIn('moderation_status', ['pending', 'flagged', 'rejected']);
        }])->get();

        $statusCounts = [
            'pending' => Thread::where('moderation_status', 'pending')->count(),
            'flagged' => Thread::where('moderation_status', 'flagged')->count(),
            'rejected' => Thread::where('moderation_status', 'rejected')->count(),
            'approved' => Thread::where('moderation_status', 'approved')->count(),
        ];

        return view('admin.moderation.threads', compact(
            'threads',
            'forums',
            'statusCounts',
            'status'
        ));
    }

    /**
     * Hiển thị comments cần moderation
     */
    public function comments(Request $request)
    {
        $status = $request->status ?? 'pending';
        $perPage = $request->per_page ?? 20;

        $query = Comment::with(['user', 'thread', 'reports'])
            ->withCount('reports');

        // Filter theo status
        if ($status !== 'all') {
            $query->where('moderation_status', $status);
        }

        // Filter theo thread
        if ($request->thread_id) {
            $query->where('thread_id', $request->thread_id);
        }

        // Filter theo user
        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        // Search
        if ($request->search) {
            $query->where('content', 'like', '%' . $request->search . '%');
        }

        // Spam detection
        if ($request->spam_check) {
            $query->where(function ($q) {
                $q->where('content', 'like', '%http%')
                    ->orWhere('content', 'like', '%www.%')
                    ->orWhere('content', 'like', '%@%')
                    ->orWhereRaw('LENGTH(content) < 10');
            });
        }

        $comments = $query->latest()->paginate($perPage);

        $statusCounts = [
            'pending' => Comment::where('moderation_status', 'pending')->count(),
            'flagged' => Comment::where('moderation_status', 'flagged')->count(),
            'rejected' => Comment::where('moderation_status', 'rejected')->count(),
            'approved' => Comment::where('moderation_status', 'approved')->count(),
        ];

        return view('admin.moderation.comments', compact('comments', 'statusCounts', 'status'));
    }

    /**
     * Hiển thị chi tiết thread cho modal (AJAX)
     */
    public function showThread(Thread $thread)
    {
        $thread->load(['user', 'forum', 'tags', 'comments' => function ($query) {
            $query->with('user')->latest()->limit(5);
        }, 'reports.reporter']);

        return view('admin.moderation.partials.thread-details', compact('thread'))->render();
    }

    /**
     * Hiển thị chi tiết comment cho modal (AJAX)
     */
    public function showComment(Comment $comment)
    {
        $comment->load(['user', 'thread.forum', 'parent.user', 'reports.reporter']);

        return view('admin.moderation.partials.comment-details', compact('comment'))->render();
    }

    /**
     * Cập nhật trạng thái moderation cho thread
     */
    public function updateThreadStatus(Request $request, Thread $thread): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected,flagged',
            'reason' => 'nullable|string|max:500',
            'notify_user' => 'boolean'
        ]);

        try {
            $oldStatus = $thread->moderation_status;

            $success = $this->moderationService->updateThreadModerationStatus(
                $thread,
                $request->status,
                Auth::user(),
                $request->reason
            );

            if ($success) {
                // Send notification nếu được yêu cầu
                if ($request->notify_user && $request->status === 'rejected') {
                    $this->sendModerationNotification($thread, $request->reason);
                }

                Log::info('Thread moderation status updated', [
                    'thread_id' => $thread->id,
                    'old_status' => $oldStatus,
                    'new_status' => $request->status,
                    'moderator_id' => Auth::id(),
                    'reason' => $request->reason
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Trạng thái thread đã được cập nhật',
                    'new_status' => $request->status
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Không thể cập nhật trạng thái thread'
            ], 500);
        } catch (\Exception $e) {
            Log::error('Error updating thread moderation status', [
                'thread_id' => $thread->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi cập nhật trạng thái'
            ], 500);
        }
    }

    /**
     * Cập nhật trạng thái moderation cho comment
     */
    public function updateCommentStatus(Request $request, Comment $comment): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected,flagged,spam',
            'reason' => 'nullable|string|max:500',
            'notify_user' => 'boolean'
        ]);

        try {
            $oldStatus = $comment->moderation_status;

            $success = $this->moderationService->updateCommentModerationStatus(
                $comment,
                $request->status,
                Auth::user(),
                $request->reason
            );

            if ($success) {
                Log::info('Comment moderation status updated', [
                    'comment_id' => $comment->id,
                    'old_status' => $oldStatus,
                    'new_status' => $request->status,
                    'moderator_id' => Auth::id(),
                    'reason' => $request->reason
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Trạng thái comment đã được cập nhật',
                    'new_status' => $request->status
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Không thể cập nhật trạng thái comment'
            ], 500);
        } catch (\Exception $e) {
            Log::error('Comment moderation update failed', [
                'error' => $e->getMessage(),
                'comment_id' => $comment->id,
                'status' => $request->status
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra'
            ], 500);
        }
    }

    /**
     * Bulk update threads status
     */
    public function bulkUpdateThreads(Request $request): JsonResponse
    {
        $request->validate([
            'thread_ids' => 'required|array',
            'thread_ids.*' => 'exists:threads,id',
            'status' => 'required|in:pending,approved,rejected,flagged',
            'reason' => 'nullable|string|max:500'
        ]);

        try {
            $threads = Thread::whereIn('id', $request->thread_ids)->get();

            $success = $this->moderationService->bulkUpdateThreadsModerationStatus(
                $threads,
                $request->status,
                Auth::user(),
                $request->reason
            );

            if ($success) {
                return response()->json([
                    'success' => true,
                    'message' => 'Đã cập nhật trạng thái cho ' . count($request->thread_ids) . ' threads',
                    'updated_count' => count($request->thread_ids)
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Không thể cập nhật trạng thái threads'
            ], 500);
        } catch (\Exception $e) {
            Log::error('Error bulk updating threads', [
                'thread_ids' => $request->thread_ids,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi cập nhật hàng loạt'
            ], 500);
        }
    }

    /**
     * Quick approve thread (AJAX)
     */
    public function quickApproveThread(Thread $thread): JsonResponse
    {
        try {
            $success = $this->moderationService->updateThreadModerationStatus(
                $thread,
                'approved',
                Auth::user(),
                'Quick approval'
            );

            if ($success) {
                return response()->json([
                    'success' => true,
                    'message' => 'Thread đã được duyệt'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Không thể duyệt thread'
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra'
            ], 500);
        }
    }

    /**
     * Quick reject thread (AJAX)
     */
    public function quickRejectThread(Thread $thread): JsonResponse
    {
        try {
            $success = $this->moderationService->updateThreadModerationStatus(
                $thread,
                'rejected',
                Auth::user(),
                'Quick rejection'
            );

            if ($success) {
                return response()->json([
                    'success' => true,
                    'message' => 'Thread đã bị từ chối'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Không thể từ chối thread'
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra'
            ], 500);
        }
    }

    /**
     * Hiển thị user activity (để theo dõi spam users)
     */
    public function userActivity(Request $request)
    {
        $query = User::with(['threads', 'comments'])
            ->withCount([
                'threads as pending_threads_count' => function ($q) {
                    $q->where('moderation_status', 'pending');
                },
                'threads as rejected_threads_count' => function ($q) {
                    $q->where('moderation_status', 'rejected');
                },
                'comments as pending_comments_count' => function ($q) {
                    $q->where('moderation_status', 'pending');
                },
                'reports as received_reports_count'
            ]);

        // Filter users có nhiều content cần moderation
        if ($request->filter === 'problematic') {
            $query->having('pending_threads_count', '>', 0)
                ->orHaving('rejected_threads_count', '>', 2)
                ->orHaving('received_reports_count', '>', 0);
        }

        // Filter theo registration date
        if ($request->period) {
            switch ($request->period) {
                case 'today':
                    $query->whereDate('created_at', today());
                    break;
                case 'week':
                    $query->where('created_at', '>=', now()->subWeek());
                    break;
                case 'month':
                    $query->where('created_at', '>=', now()->subMonth());
                    break;
            }
        }

        $users = $query->latest()->paginate(25);

        return view('admin.moderation.user-activity', compact('users'));
    }

    /**
     * Get moderation statistics for AJAX
     */
    public function getStatistics(): JsonResponse
    {
        try {
            $stats = $this->moderationService->getModerationStatistics();

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể lấy thống kê'
            ], 500);
        }
    }

    /**
     * Gửi thông báo moderation cho user
     */
    private function sendModerationNotification(Thread $thread, ?string $reason)
    {
        // TODO: Implement notification system
        // Có thể gửi email, in-app notification, etc.

        Log::info('Moderation notification sent', [
            'thread_id' => $thread->id,
            'user_id' => $thread->user_id,
            'reason' => $reason
        ]);
    }
}
