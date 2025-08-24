<?php

namespace App\Http\Controllers\Dashboard\Community;

use App\Http\Controllers\Dashboard\BaseController;
use App\Models\Thread;
use App\Models\Forum;
use Illuminate\Http\Request;

/**
 * Thread Controller cho Dashboard Community
 *
 * Quản lý threads của user trong dashboard
 */
class ThreadController extends BaseController
{
    /**
     * Hiển thị danh sách threads của user
     */
    public function index(Request $request)
    {
        $filter = $request->get('filter', 'all');
        $status = $request->get('status');
        $type = $request->get('type');
        $solved = $request->get('solved');
        $search = $request->get('search');

        $query = Thread::with(['forum', 'tags'])
            ->withCount(['allComments as comments_count', 'bookmarks', 'ratings'])
            ->where('user_id', $this->user->id);

        // Apply filters
        if ($status) {
            $query->where('moderation_status', $status);
        }

        if ($type) {
            $query->where('thread_type', $type);
        }

        if ($solved === 'yes') {
            $query->where('is_solved', true);
        } elseif ($solved === 'no') {
            $query->where('is_solved', false);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $threads = $query->latest()->paginate(20);

        // Get statistics
        $stats = $this->getThreadStats();

        // Get forums for filter
        $forums = Forum::orderBy('name')->get();

        $breadcrumb = $this->getBreadcrumb([
            ['name' => 'Community', 'route' => null],
            ['name' => 'My Threads', 'route' => 'dashboard.community.threads']
        ]);

        return $this->dashboardResponse('dashboard.community.threads.index', [
            'threads' => $threads,
            'stats' => $stats,
            'forums' => $forums,
            'currentFilter' => $filter,
            'currentStatus' => $status,
            'currentType' => $type,
            'currentSolved' => $solved,
            'search' => $search,
            'breadcrumb' => $breadcrumb
        ]);
    }

    /**
     * Hiển thị threads đã follow
     */
    public function followedThreads(Request $request)
    {
        $search = $request->get('search');

        $query = $this->user->followedThreads()
            ->with(['user', 'forum', 'tags'])
            ->withCount(['allComments as comments_count', 'bookmarks', 'ratings']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $threads = $query->latest('thread_follows.created_at')->paginate(20);

        $breadcrumb = $this->getBreadcrumb([
            ['name' => 'Community', 'route' => null],
            ['name' => 'Followed Threads', 'route' => 'dashboard.community.followed-threads']
        ]);

        return $this->dashboardResponse('dashboard.community.threads.followed', [
            'threads' => $threads,
            'search' => $search,
            'breadcrumb' => $breadcrumb
        ]);
    }

    /**
     * Hiển thị threads đã tham gia (có comment)
     */
    public function participatedThreads(Request $request)
    {
        $search = $request->get('search');

        // Get threads where user has commented
        $threadIds = $this->user->comments()
            ->select('thread_id')
            ->distinct()
            ->pluck('thread_id');

        $query = Thread::with(['user', 'forum', 'tags'])
            ->withCount(['allComments as comments_count', 'bookmarks', 'ratings'])
            ->whereIn('id', $threadIds)
            ->where('user_id', '!=', $this->user->id); // Exclude own threads

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $threads = $query->latest()->paginate(20);

        $breadcrumb = $this->getBreadcrumb([
            ['name' => 'Community', 'route' => null],
            ['name' => 'Participated Threads', 'route' => 'dashboard.community.participated']
        ]);

        return $this->dashboardResponse('dashboard.community.threads.participated', [
            'threads' => $threads,
            'search' => $search,
            'breadcrumb' => $breadcrumb
        ]);
    }

    /**
     * Lấy thống kê threads
     */
    private function getThreadStats()
    {
        $total = Thread::where('user_id', $this->user->id)->count();
        $approved = Thread::where('user_id', $this->user->id)
            ->where('moderation_status', 'approved')->count();
        $pending = Thread::where('user_id', $this->user->id)
            ->where('moderation_status', 'under_review')->count();
        $solved = Thread::where('user_id', $this->user->id)
            ->where('is_solved', true)->count();

        $averageRating = Thread::where('user_id', $this->user->id)
            ->where('ratings_count', '>', 0)
            ->avg('average_rating');

        $totalViews = Thread::where('user_id', $this->user->id)->sum('view_count');
        $totalBookmarks = Thread::where('user_id', $this->user->id)->sum('bookmark_count');

        return [
            'total' => $total,
            'approved' => $approved,
            'pending' => $pending,
            'solved' => $solved,
            'unsolved' => $total - $solved,
            'average_rating' => round($averageRating, 2),
            'total_views' => $totalViews,
            'total_bookmarks' => $totalBookmarks,
            'followed_threads' => $this->user->followedThreads()->count(),
            'participated_threads' => $this->user->comments()
                ->select('thread_id')
                ->distinct()
                ->count(),
        ];
    }

    /**
     * API: Lấy threads data cho AJAX
     */
    public function getThreadsData(Request $request)
    {
        $threads = Thread::with(['forum', 'tags'])
            ->where('user_id', $this->user->id)
            ->when($request->status, function ($q, $status) {
                return $q->where('moderation_status', $status);
            })
            ->when($request->type, function ($q, $type) {
                return $q->where('thread_type', $type);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'threads' => $threads,
        ]);
    }

    /**
     * Bulk actions cho threads
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|string|in:delete,archive,mark_solved,mark_unsolved',
            'thread_ids' => 'required|array',
            'thread_ids.*' => 'exists:threads,id',
        ]);

        $threadIds = $request->thread_ids;
        $action = $request->action;

        // Verify ownership
        $threads = Thread::whereIn('id', $threadIds)
            ->where('user_id', $this->user->id)
            ->get();

        if ($threads->count() !== count($threadIds)) {
            return response()->json([
                'success' => false,
                'message' => 'Some threads do not belong to you.'
            ], 403);
        }

        $updated = 0;

        switch ($action) {
            case 'delete':
                $updated = Thread::whereIn('id', $threadIds)
                    ->where('user_id', $this->user->id)
                    ->delete();
                break;

            case 'mark_solved':
                $updated = Thread::whereIn('id', $threadIds)
                    ->where('user_id', $this->user->id)
                    ->update(['is_solved' => true]);
                break;

            case 'mark_unsolved':
                $updated = Thread::whereIn('id', $threadIds)
                    ->where('user_id', $this->user->id)
                    ->update(['is_solved' => false]);
                break;

            case 'archive':
                $updated = Thread::whereIn('id', $threadIds)
                    ->where('user_id', $this->user->id)
                    ->update(['is_archived' => true]);
                break;
        }

        return response()->json([
            'success' => true,
            'message' => "Successfully {$action}d {$updated} threads.",
            'updated_count' => $updated
        ]);
    }
}
