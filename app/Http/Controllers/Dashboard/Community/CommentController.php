<?php

namespace App\Http\Controllers\Dashboard\Community;

use App\Http\Controllers\Dashboard\BaseController;
use App\Models\Comment;
use App\Models\Thread;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * Comment Controller cho Dashboard Community
 *
 * Quản lý comments của user trong dashboard
 */
class CommentController extends BaseController
{
    /**
     * Hiển thị danh sách comments của user
     */
    public function index(Request $request)
    {
        $threadId = $request->get('thread_id');
        $status = $request->get('status');
        $search = $request->get('search');
        $sort = $request->get('sort', 'newest');

        $query = Comment::with(['thread' => function ($q) {
            $q->with(['user', 'forum']);
        }])
            ->where('user_id', $this->user->id);

        // Filter by thread
        if ($threadId) {
            $query->where('thread_id', $threadId);
        }

        // Filter by status
        if ($status) {
            $query->where('moderation_status', $status);
        }

        // Search in comment content
        if ($search) {
            $query->where('content', 'like', "%{$search}%");
        }

        // Apply sorting
        switch ($sort) {
            case 'oldest':
                $query->oldest();
                break;
            case 'likes':
                $query->orderByDesc('likes_count');
                break;
            case 'newest':
            default:
                $query->latest();
                break;
        }

        $comments = $query->paginate(20);

        // Get threads for filter
        $threads = Thread::where('user_id', $this->user->id)
            ->orderBy('title')
            ->get(['id', 'title']);

        // Get statistics
        $stats = $this->getCommentStats();

        return $this->dashboardResponse('dashboard.community.comments.index', [
            'comments' => $comments,
            'threads' => $threads,
            'stats' => $stats,
            'currentThreadId' => $threadId,
            'currentStatus' => $status,
            'search' => $search,
            'currentSort' => $sort]);
    }

    /**
     * Hiển thị comment cụ thể
     */
    public function show(Comment $comment)
    {
        if ($comment->user_id !== $this->user->id) {
            abort(403, 'You can only view your own comments.');
        }

        $comment->load(['thread.user', 'thread.forum', 'parent', 'replies.user']);

        return $this->dashboardResponse('dashboard.community.comments.show', [
            'comment' => $comment]);
    }

    /**
     * Chỉnh sửa comment
     */
    public function edit(Comment $comment)
    {
        if ($comment->user_id !== $this->user->id) {
            abort(403, 'You can only edit your own comments.');
        }

        return $this->dashboardResponse('dashboard.community.comments.edit', [
            'comment' => $comment]);
    }

    /**
     * Cập nhật comment
     */
    public function update(Request $request, Comment $comment)
    {
        if ($comment->user_id !== $this->user->id) {
            abort(403, 'You can only edit your own comments.');
        }

        $request->validate([
            'content' => 'required|string|min:10|max:10000']);

        $comment->update([
            'content' => $request->content,
            'edited_at' => now()]);

        return redirect()->route('dashboard.community.comments.show', $comment)
            ->with('success', 'Comment updated successfully.');
    }

    /**
     * Xóa comment
     */
    public function destroy(Comment $comment): JsonResponse
    {
        if ($comment->user_id !== $this->user->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $comment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Comment deleted successfully.'
        ]);
    }

    /**
     * Lấy thống kê comments
     */
    private function getCommentStats()
    {
        $total = Comment::where('user_id', $this->user->id)->count();
        $verified = Comment::where('user_id', $this->user->id)
            ->where('verification_status', 'verified')->count();
        $flagged = Comment::where('user_id', $this->user->id)
            ->where('is_flagged', true)->count();
        $solutions = Comment::where('user_id', $this->user->id)
            ->where('is_solution', true)->count();

        $totalLikes = Comment::where('user_id', $this->user->id)->sum('like_count');

        $threadsCommentedOn = Comment::where('user_id', $this->user->id)
            ->select('thread_id')
            ->distinct()
            ->count();

        return [
            'total' => $total,
            'verified' => $verified,
            'flagged' => $flagged,
            'solutions' => $solutions,
            'total_likes' => $totalLikes,
            'threads_commented_on' => $threadsCommentedOn,
            'average_likes' => $total > 0 ? round($totalLikes / $total, 2) : 0,
            'this_week' => Comment::where('user_id', $this->user->id)
                ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
                ->count(),
            'this_month' => Comment::where('user_id', $this->user->id)
                ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
                ->count()];
    }

    /**
     * Bulk actions cho comments
     */
    public function bulkAction(Request $request): JsonResponse
    {
        $request->validate([
            'action' => 'required|string|in:delete,approve,flag',
            'comment_ids' => 'required|array',
            'comment_ids.*' => 'exists:comments,id']);

        $commentIds = $request->comment_ids;
        $action = $request->action;

        // Verify ownership
        $comments = Comment::whereIn('id', $commentIds)
            ->where('user_id', $this->user->id)
            ->get();

        if ($comments->count() !== count($commentIds)) {
            return response()->json([
                'success' => false,
                'message' => 'Some comments do not belong to you.'
            ], 403);
        }

        $updated = 0;

        switch ($action) {
            case 'delete':
                $updated = Comment::whereIn('id', $commentIds)
                    ->where('user_id', $this->user->id)
                    ->delete();
                break;

            case 'approve':
                $updated = Comment::whereIn('id', $commentIds)
                    ->where('user_id', $this->user->id)
                    ->update(['moderation_status' => 'approved']);
                break;

            case 'flag':
                $updated = Comment::whereIn('id', $commentIds)
                    ->where('user_id', $this->user->id)
                    ->update(['moderation_status' => 'flagged']);
                break;
        }

        return response()->json([
            'success' => true,
            'message' => "Successfully {$action}d {$updated} comments.",
            'updated_count' => $updated
        ]);
    }

    /**
     * Export comments
     */
    public function exportComments(Request $request)
    {
        $format = $request->get('format', 'json');

        $comments = Comment::with(['thread'])
            ->where('user_id', $this->user->id)
            ->get()
            ->map(function ($comment) {
                return [
                    'thread_title' => $comment->thread->title,
                    'thread_url' => route('threads.show', $comment->thread),
                    'content' => strip_tags($comment->content),
                    'likes_count' => $comment->likes_count,
                    'status' => $comment->moderation_status,
                    'created_at' => $comment->created_at->toISOString()];
            });

        $filename = 'comments_' . $this->user->username . '_' . now()->format('Y-m-d');

        if ($format === 'csv') {
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"{$filename}.csv\""];

            $callback = function () use ($comments) {
                $file = fopen('php://output', 'w');
                fputcsv($file, ['Thread Title', 'Thread URL', 'Content', 'Likes', 'Status', 'Created At']);

                foreach ($comments as $comment) {
                    fputcsv($file, [
                        $comment['thread_title'],
                        $comment['thread_url'],
                        $comment['content'],
                        $comment['likes_count'],
                        $comment['status'],
                        $comment['created_at']]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

        // Default to JSON
        return response()->json($comments)
            ->header('Content-Disposition', "attachment; filename=\"{$filename}.json\"");
    }
}
