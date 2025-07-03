<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Thread;
use App\Models\Comment;
use App\Models\ThreadRating;
use App\Models\ThreadBookmark;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ModerationController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin|moderator']);
    }

    /**
     * Dashboard tổng quan moderation
     */
    public function dashboard()
    {
        \Log::info('ModerationController::dashboard called', [
            'user' => auth()->user()->email,
            'timestamp' => now()
        ]);
        $stats = [
            'threads' => [
                'total' => Thread::count(),
                'pending' => Thread::where('moderation_status', 'under_review')->count(),
                'flagged' => Thread::whereNotNull('flagged_at')->count(),
                'spam' => Thread::where('is_spam', true)->count(),
                'approved' => Thread::where('moderation_status', 'approved')->count(),
            ],
            'comments' => [
                'total' => Comment::count(),
                'flagged' => Comment::where('is_flagged', true)->count(),
                'spam' => Comment::where('is_spam', true)->count(),
            ],
            'ratings' => [
                'total' => ThreadRating::count(),
                'average' => ThreadRating::avg('rating'),
                'distribution' => ThreadRating::select('rating', DB::raw('count(*) as count'))
                    ->groupBy('rating')
                    ->orderBy('rating')
                    ->get(),
            ],
            'bookmarks' => [
                'total' => ThreadBookmark::count(),
                'unique_users' => ThreadBookmark::distinct('user_id')->count(),
                'unique_threads' => ThreadBookmark::distinct('thread_id')->count(),
            ]
        ];

        // Today's activity stats
        $todayActivity = [
            'approved_threads' => Thread::where('moderation_status', 'approved')
                ->whereDate('moderated_at', today())
                ->count(),
            'rejected_threads' => Thread::where('moderation_status', 'rejected')
                ->whereDate('moderated_at', today())
                ->count(),
            'approved_comments' => Comment::where('verification_status', 'verified')
                ->whereDate('verified_at', today())
                ->count(),
            'rejected_comments' => Comment::where('verification_status', 'rejected')
                ->whereDate('verified_at', today())
                ->count(),
        ];

        // Recent threads needing moderation
        $recentThreads = Thread::where('moderation_status', 'under_review')
            ->orWhereNotNull('flagged_at')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Recent comments needing moderation
        $recentComments = Comment::where('is_flagged', true)
            ->orWhere('is_spam', true)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('admin.moderation.dashboard', compact('stats', 'todayActivity', 'recentThreads', 'recentComments'));
    }

    /**
     * Danh sách threads cần moderation
     */
    public function threads(Request $request)
    {
        $query = Thread::with(['user', 'forum', 'ratings'])
            ->withCount(['allComments as comments_count', 'bookmarks']);

        // Filter theo status
        if ($request->status) {
            switch ($request->status) {
                case 'pending':
                    $query->where('moderation_status', 'under_review');
                    break;
                case 'flagged':
                    $query->where('is_flagged', true);
                    break;
                case 'spam':
                    $query->where('is_spam', true);
                    break;
                case 'approved':
                    $query->where('moderation_status', 'approved');
                    break;
            }
        }

        // Filter theo quality score
        if ($request->min_quality) {
            $query->where('quality_score', '>=', $request->min_quality);
        }

        if ($request->max_quality) {
            $query->where('quality_score', '<=', $request->max_quality);
        }

        // Filter theo thread type
        if ($request->thread_type) {
            $query->where('thread_type', $request->thread_type);
        }

        $threads = $query->latest()->paginate(20);
        $forums = \App\Models\Forum::orderBy('name')->get();

        return view('admin.moderation.threads', compact('threads', 'forums'));
    }

    /**
     * Show thread details for moderation
     */
    public function showThread(Thread $thread)
    {
        $thread->load(['user', 'forum', 'comments.user', 'ratings']);

        $html = view('admin.moderation.thread-details', compact('thread'))->render();

        return response($html);
    }

    /**
     * Approve thread
     */
    public function approveThread(Request $request, Thread $thread)
    {
        $thread->update([
            'moderation_status' => 'approved',
            'is_flagged' => false,
            'is_spam' => false,
            'moderation_notes' => $request->notes,
            'flagged_by' => null,
            'flagged_at' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Thread đã được approve'
        ]);
    }

    /**
     * Reject thread (mark as spam)
     */
    public function rejectThread(Request $request, Thread $thread)
    {
        $thread->update([
            'moderation_status' => 'spam',
            'is_spam' => true,
            'is_flagged' => true,
            'moderation_notes' => $request->notes,
            'flagged_by' => Auth::id(),
            'flagged_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Thread đã được reject'
        ]);
    }

    /**
     * Flag thread for review
     */
    public function flagThread(Request $request, Thread $thread)
    {
        $thread->update([
            'moderation_status' => 'flagged',
            'is_flagged' => true,
            'moderation_notes' => $request->notes,
            'flagged_by' => Auth::id(),
            'flagged_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Thread đã được flag để review'
        ]);
    }

    /**
     * Update thread status (for AJAX calls)
     */
    public function updateThreadStatus(Request $request, Thread $thread)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected,flagged,pending'
        ]);

        switch ($request->status) {
            case 'approved':
                $thread->update([
                    'moderation_status' => 'approved',
                    'is_flagged' => false,
                    'is_spam' => false,
                    'moderation_notes' => $request->notes ?? 'Approved via quick action',
                    'flagged_by' => null,
                    'flagged_at' => null,
                ]);
                $message = 'Thread đã được duyệt';
                break;

            case 'rejected':
                $thread->update([
                    'moderation_status' => 'spam',
                    'is_spam' => true,
                    'is_flagged' => true,
                    'moderation_notes' => $request->notes ?? 'Rejected via quick action',
                    'flagged_by' => Auth::id(),
                    'flagged_at' => now(),
                ]);
                $message = 'Thread đã bị từ chối';
                break;

            case 'flagged':
                $thread->update([
                    'moderation_status' => 'flagged',
                    'is_flagged' => true,
                    'moderation_notes' => $request->notes ?? 'Flagged via quick action',
                    'flagged_by' => Auth::id(),
                    'flagged_at' => now(),
                ]);
                $message = 'Thread đã được đánh dấu để review';
                break;

            case 'pending':
                $thread->update([
                    'moderation_status' => 'under_review',
                    'is_flagged' => false,
                    'is_spam' => false,
                    'moderation_notes' => $request->notes ?? 'Set to pending via quick action',
                ]);
                $message = 'Thread đã được đặt về trạng thái chờ duyệt';
                break;
        }

        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }

    /**
     * Bulk actions cho threads
     */
    public function bulkActionThreads(Request $request)
    {
        $request->validate([
            'action' => 'required|in:approve,reject,flag,delete',
            'thread_ids' => 'required|array',
            'thread_ids.*' => 'exists:threads,id',
            'notes' => 'nullable|string|max:500'
        ]);

        $threads = Thread::whereIn('id', $request->thread_ids);
        $count = $threads->count();

        switch ($request->action) {
            case 'approve':
                $threads->update([
                    'moderation_status' => 'approved',
                    'is_flagged' => false,
                    'is_spam' => false,
                    'moderation_notes' => $request->notes,
                ]);
                $message = "Đã approve {$count} threads";
                break;

            case 'reject':
                $threads->update([
                    'moderation_status' => 'spam',
                    'is_spam' => true,
                    'is_flagged' => true,
                    'moderation_notes' => $request->notes,
                    'flagged_by' => Auth::id(),
                    'flagged_at' => now(),
                ]);
                $message = "Đã reject {$count} threads";
                break;

            case 'flag':
                $threads->update([
                    'moderation_status' => 'flagged',
                    'is_flagged' => true,
                    'moderation_notes' => $request->notes,
                    'flagged_by' => Auth::id(),
                    'flagged_at' => now(),
                ]);
                $message = "Đã flag {$count} threads để review";
                break;

            case 'delete':
                $threads->delete();
                $message = "Đã xóa {$count} threads";
                break;
        }

        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }

    /**
     * Bulk update threads status
     */
    public function bulkUpdateThreads(Request $request)
    {
        $request->validate([
            'thread_ids' => 'required|array',
            'thread_ids.*' => 'exists:threads,id',
            'status' => 'required|in:approved,rejected',
            'reason' => 'nullable|string|max:500'
        ]);

        $threads = Thread::whereIn('id', $request->thread_ids);
        $count = $threads->count();

        if ($request->status === 'approved') {
            $threads->update([
                'moderation_status' => 'approved',
                'is_flagged' => false,
                'is_spam' => false,
                'moderation_notes' => $request->reason ?? 'Bulk approved',
                'flagged_by' => null,
                'flagged_at' => null,
            ]);
            $message = "Đã duyệt {$count} threads";
        } else {
            $threads->update([
                'moderation_status' => 'spam',
                'is_spam' => true,
                'is_flagged' => true,
                'moderation_notes' => $request->reason ?? 'Bulk rejected',
                'flagged_by' => Auth::id(),
                'flagged_at' => now(),
            ]);
            $message = "Đã từ chối {$count} threads";
        }

        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }

    /**
     * Danh sách comments cần moderation
     */
    public function comments(Request $request)
    {
        $query = Comment::with(['user', 'thread'])
            ->withCount(['likes']);

        if ($request->status === 'flagged') {
            $query->where('is_flagged', true);
        } elseif ($request->status === 'spam') {
            $query->where('is_spam', true);
        }

        $comments = $query->latest()->paginate(20);
        $threads = \App\Models\Thread::orderBy('title')->get();

        return view('admin.moderation.comments', compact('comments', 'threads'));
    }

    /**
     * Approve comment
     */
    public function approveComment(Comment $comment)
    {
        $comment->update([
            'is_flagged' => false,
            'is_spam' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Comment đã được approve'
        ]);
    }

    /**
     * Delete comment
     */
    public function deleteComment(Comment $comment)
    {
        $comment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Comment đã được xóa'
        ]);
    }

    /**
     * Show comment details
     */
    public function showComment(Comment $comment)
    {
        $comment->load(['user', 'thread', 'thread.forum']);

        return view('admin.moderation.comment-details', compact('comment'));
    }

    /**
     * Update comment status
     */
    public function updateCommentStatus(Request $request, Comment $comment)
    {
        $status = $request->input('status');

        switch ($status) {
            case 'approved':
                $comment->update([
                    'is_flagged' => false,
                    'is_spam' => false,
                ]);
                $message = 'Comment đã được duyệt';
                break;

            case 'rejected':
                $comment->update([
                    'is_flagged' => true,
                    'is_spam' => false,
                ]);
                $message = 'Comment đã bị từ chối';
                break;

            case 'spam':
                $comment->update([
                    'is_flagged' => true,
                    'is_spam' => true,
                ]);
                $message = 'Comment đã được đánh dấu là spam';
                break;

            default:
                return response()->json([
                    'success' => false,
                    'message' => 'Trạng thái không hợp lệ'
                ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }

    /**
     * Bulk update comments
     */
    public function bulkUpdateComments(Request $request)
    {
        $request->validate([
            'comment_ids' => 'required|array',
            'comment_ids.*' => 'exists:comments,id',
            'status' => 'required|in:approved,rejected,spam',
            'reason' => 'nullable|string|max:255'
        ]);

        $commentIds = $request->input('comment_ids');
        $status = $request->input('status');
        $reason = $request->input('reason');

        $updateData = [];
        switch ($status) {
            case 'approved':
                $updateData = [
                    'is_flagged' => false,
                    'is_spam' => false,
                ];
                $message = 'Đã duyệt ' . count($commentIds) . ' comment';
                break;

            case 'rejected':
                $updateData = [
                    'is_flagged' => true,
                    'is_spam' => false,
                ];
                $message = 'Đã từ chối ' . count($commentIds) . ' comment';
                break;

            case 'spam':
                $updateData = [
                    'is_flagged' => true,
                    'is_spam' => true,
                ];
                $message = 'Đã đánh dấu spam ' . count($commentIds) . ' comment';
                break;
        }

        Comment::whereIn('id', $commentIds)->update($updateData);

        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }

    /**
     * Statistics page
     */
    public function statistics()
    {
        // Thread stats by type
        $threadsByType = Thread::select('thread_type', DB::raw('count(*) as count'))
            ->groupBy('thread_type')
            ->get();

        // Thread stats by status
        $threadsByStatus = Thread::select('moderation_status', DB::raw('count(*) as count'))
            ->groupBy('moderation_status')
            ->get();

        // Quality distribution
        $qualityDistribution = Thread::select(
            DB::raw('CASE
                    WHEN quality_score >= 80 THEN "Excellent (80-100)"
                    WHEN quality_score >= 60 THEN "Good (60-79)"
                    WHEN quality_score >= 40 THEN "Average (40-59)"
                    WHEN quality_score >= 20 THEN "Poor (20-39)"
                    ELSE "Very Poor (0-19)"
                END as quality_range'),
            DB::raw('count(*) as count')
        )
            ->groupBy('quality_range')
            ->get();

        // Rating trends (last 30 days)
        $ratingTrends = ThreadRating::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('AVG(rating) as avg_rating'),
            DB::raw('COUNT(*) as total_ratings')
        )
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Top rated threads
        $topRatedThreads = Thread::with(['user'])
            ->where('ratings_count', '>=', 3)
            ->orderBy('average_rating', 'desc')
            ->limit(10)
            ->get();

        return view('admin.moderation.statistics', compact(
            'threadsByType',
            'threadsByStatus',
            'qualityDistribution',
            'ratingTrends',
            'topRatedThreads'
        ));
    }

    /**
     * User moderation activity
     */
    public function userActivity(Request $request)
    {
        $query = Thread::with(['user'])
            ->select(
                'user_id',
                DB::raw('COUNT(*) as thread_count'),
                DB::raw('AVG(quality_score) as avg_quality'),
                DB::raw('AVG(average_rating) as avg_rating'),
                DB::raw('SUM(bookmark_count) as total_bookmarks'),
                DB::raw('SUM(CASE WHEN is_spam = 1 THEN 1 ELSE 0 END) as spam_count'),
                DB::raw('SUM(CASE WHEN is_flagged = 1 THEN 1 ELSE 0 END) as flagged_count')
            )
            ->groupBy('user_id')
            ->having('thread_count', '>=', 1);

        if ($request->min_quality) {
            $query->having('avg_quality', '>=', $request->min_quality);
        }

        if ($request->sort === 'quality') {
            $query->orderBy('avg_quality', 'desc');
        } elseif ($request->sort === 'rating') {
            $query->orderBy('avg_rating', 'desc');
        } else {
            $query->orderBy('thread_count', 'desc');
        }

        $userStats = $query->paginate(20);

        return view('admin.moderation.user-activity', compact('userStats'));
    }

    /**
     * Danh sách báo cáo vi phạm
     */
    public function reports(Request $request)
    {
        $query = \App\Models\Report::with(['user', 'reportable', 'reporter'])
            ->orderBy('created_at', 'desc');

        // Filter theo status
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // Filter theo type
        if ($request->type) {
            $query->where('reportable_type', $request->type);
        }

        // Filter theo priority
        if ($request->priority) {
            $query->where('priority', $request->priority);
        }

        // Search
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('reason', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        $reports = $query->paginate(20);

        // Statistics
        $stats = [
            'total' => \App\Models\Report::count(),
            'pending' => \App\Models\Report::where('status', 'pending')->count(),
            'resolved' => \App\Models\Report::where('status', 'resolved')->count(),
            'dismissed' => \App\Models\Report::where('status', 'dismissed')->count(),
            'high_priority' => \App\Models\Report::where('priority', 'high')->where('status', 'pending')->count(),
        ];

        return view('admin.moderation.reports', compact('reports', 'stats'));
    }

    /**
     * Giải quyết báo cáo
     */
    public function resolveReport(Request $request, $reportId)
    {
        $report = \App\Models\Report::findOrFail($reportId);

        $report->update([
            'status' => 'resolved',
            'resolved_by' => auth()->id(),
            'resolved_at' => now(),
            'resolution_note' => $request->resolution_note,
        ]);

        // Log action
        \Log::info('Report resolved', [
            'report_id' => $reportId,
            'resolved_by' => auth()->user()->name,
            'resolution_note' => $request->resolution_note,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Báo cáo đã được giải quyết thành công.'
        ]);
    }

    /**
     * Bỏ qua báo cáo
     */
    public function dismissReport(Request $request, $reportId)
    {
        $report = \App\Models\Report::findOrFail($reportId);

        $report->update([
            'status' => 'dismissed',
            'resolved_by' => auth()->id(),
            'resolved_at' => now(),
            'resolution_note' => $request->resolution_note,
        ]);

        // Log action
        \Log::info('Report dismissed', [
            'report_id' => $reportId,
            'dismissed_by' => auth()->user()->name,
            'resolution_note' => $request->resolution_note,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Báo cáo đã được bỏ qua.'
        ]);
    }

    /**
     * Bulk actions cho reports
     */
    public function bulkActionReports(Request $request)
    {
        $reportIds = $request->report_ids;
        $action = $request->action;

        if (!$reportIds || !$action) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng chọn báo cáo và hành động.'
            ]);
        }

        $reports = \App\Models\Report::whereIn('id', $reportIds);

        switch ($action) {
            case 'resolve':
                $reports->update([
                    'status' => 'resolved',
                    'resolved_by' => auth()->id(),
                    'resolved_at' => now(),
                ]);
                $message = 'Đã giải quyết ' . count($reportIds) . ' báo cáo.';
                break;

            case 'dismiss':
                $reports->update([
                    'status' => 'dismissed',
                    'resolved_by' => auth()->id(),
                    'resolved_at' => now(),
                ]);
                $message = 'Đã bỏ qua ' . count($reportIds) . ' báo cáo.';
                break;

            case 'high_priority':
                $reports->update(['priority' => 'high']);
                $message = 'Đã đặt ' . count($reportIds) . ' báo cáo thành ưu tiên cao.';
                break;

            default:
                return response()->json([
                    'success' => false,
                    'message' => 'Hành động không hợp lệ.'
                ]);
        }

        // Log bulk action
        \Log::info('Bulk action on reports', [
            'action' => $action,
            'report_ids' => $reportIds,
            'performed_by' => auth()->user()->name,
        ]);

        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }
}
