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
        $stats = [
            'threads' => [
                'total' => Thread::count(),
                'pending' => Thread::where('moderation_status', 'under_review')->count(),
                'flagged' => Thread::where('is_flagged', true)->count(),
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
                'with_folders' => ThreadBookmark::whereNotNull('folder')->count(),
                'with_notes' => ThreadBookmark::whereNotNull('notes')->count(),
            ]
        ];

        return view('admin.moderation.dashboard', compact('stats'));
    }

    /**
     * Danh sách threads cần moderation
     */
    public function threads(Request $request)
    {
        $query = Thread::with(['user', 'forum', 'ratings'])
            ->withCount(['comments', 'bookmarks']);

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

        return view('admin.moderation.threads', compact('threads'));
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

        return view('admin.moderation.comments', compact('comments'));
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
}
