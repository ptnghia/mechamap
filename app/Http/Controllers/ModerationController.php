<?php

namespace App\Http\Controllers;

use App\Models\Thread;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ModerationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // TODO: Thêm middleware để kiểm tra quyền moderator
        // $this->middleware('can:moderate-content');
    }

    // =================
    // THREAD MODERATION
    // =================

    /**
     * Flag một thread.
     */
    public function flagThread(Request $request, Thread $thread): JsonResponse
    {
        $request->validate([
            'notes' => 'nullable|string|max:1000',
        ]);

        $thread->flag(Auth::user(), $request->notes);

        return response()->json([
            'success' => true,
            'message' => 'Thread đã được flag thành công',
            'data' => [
                'is_flagged' => $thread->is_flagged,
                'flagged_at' => $thread->flagged_at,
                'moderation_notes' => $thread->moderation_notes,
            ]
        ]);
    }

    /**
     * Unflag một thread.
     */
    public function unflagThread(Thread $thread): JsonResponse
    {
        $thread->unflag();

        return response()->json([
            'success' => true,
            'message' => 'Thread đã được unflag thành công',
            'data' => [
                'is_flagged' => $thread->is_flagged,
            ]
        ]);
    }

    /**
     * Đánh dấu thread là spam.
     */
    public function markThreadAsSpam(Thread $thread): JsonResponse
    {
        $thread->update(['is_spam' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Thread đã được đánh dấu là spam',
            'data' => [
                'is_spam' => $thread->is_spam,
            ]
        ]);
    }

    /**
     * Bỏ đánh dấu thread là spam.
     */
    public function unmarkThreadAsSpam(Thread $thread): JsonResponse
    {
        $thread->update(['is_spam' => false]);

        return response()->json([
            'success' => true,
            'message' => 'Thread đã được bỏ đánh dấu spam',
            'data' => [
                'is_spam' => $thread->is_spam,
            ]
        ]);
    }

    /**
     * Cập nhật trạng thái moderation của thread.
     */
    public function updateThreadModerationStatus(Request $request, Thread $thread): JsonResponse
    {
        $request->validate([
            'status' => ['required', Rule::in(['pending', 'approved', 'rejected'])],
            'notes' => 'nullable|string|max:1000',
        ]);

        $thread->update([
            'moderation_status' => $request->status,
            'moderation_notes' => $request->notes,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Trạng thái moderation đã được cập nhật',
            'data' => [
                'moderation_status' => $thread->moderation_status,
                'moderation_notes' => $thread->moderation_notes,
            ]
        ]);
    }

    /**
     * Archive một thread.
     */
    public function archiveThread(Request $request, Thread $thread): JsonResponse
    {
        $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        $thread->archive($request->reason);

        return response()->json([
            'success' => true,
            'message' => 'Thread đã được archive thành công',
            'data' => [
                'archived_at' => $thread->archived_at,
                'archived_reason' => $thread->archived_reason,
            ]
        ]);
    }

    /**
     * Unarchive một thread.
     */
    public function unarchiveThread(Thread $thread): JsonResponse
    {
        $thread->unarchive();

        return response()->json([
            'success' => true,
            'message' => 'Thread đã được unarchive thành công',
            'data' => [
                'archived_at' => $thread->archived_at,
                'archived_reason' => $thread->archived_reason,
            ]
        ]);
    }

    /**
     * Ẩn một thread.
     */
    public function hideThread(Request $request, Thread $thread): JsonResponse
    {
        $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        $thread->hide($request->reason);

        return response()->json([
            'success' => true,
            'message' => 'Thread đã được ẩn thành công',
            'data' => [
                'hidden_at' => $thread->hidden_at,
                'hidden_reason' => $thread->hidden_reason,
            ]
        ]);
    }

    /**
     * Hiện một thread.
     */
    public function unhideThread(Thread $thread): JsonResponse
    {
        $thread->unhide();

        return response()->json([
            'success' => true,
            'message' => 'Thread đã được hiện thành công',
            'data' => [
                'hidden_at' => $thread->hidden_at,
                'hidden_reason' => $thread->hidden_reason,
            ]
        ]);
    }

    // =================
    // COMMENT MODERATION
    // =================

    /**
     * Flag một comment.
     */
    public function flagComment(Comment $comment): JsonResponse
    {
        $comment->flag();

        return response()->json([
            'success' => true,
            'message' => 'Comment đã được flag thành công',
            'data' => [
                'is_flagged' => $comment->is_flagged,
                'reports_count' => $comment->reports_count,
            ]
        ]);
    }

    /**
     * Unflag một comment.
     */
    public function unflagComment(Comment $comment): JsonResponse
    {
        $comment->unflag();

        return response()->json([
            'success' => true,
            'message' => 'Comment đã được unflag thành công',
            'data' => [
                'is_flagged' => $comment->is_flagged,
            ]
        ]);
    }

    /**
     * Đánh dấu comment là spam.
     */
    public function markCommentAsSpam(Comment $comment): JsonResponse
    {
        $comment->markAsSpam();

        return response()->json([
            'success' => true,
            'message' => 'Comment đã được đánh dấu là spam',
            'data' => [
                'is_spam' => $comment->is_spam,
            ]
        ]);
    }

    /**
     * Bỏ đánh dấu comment là spam.
     */
    public function unmarkCommentAsSpam(Comment $comment): JsonResponse
    {
        $comment->unmarkSpam();

        return response()->json([
            'success' => true,
            'message' => 'Comment đã được bỏ đánh dấu spam',
            'data' => [
                'is_spam' => $comment->is_spam,
            ]
        ]);
    }

    /**
     * Đánh dấu comment là solution.
     */
    public function markCommentAsSolution(Comment $comment): JsonResponse
    {
        // Chỉ author của thread hoặc moderator mới có thể đánh dấu solution
        $thread = $comment->thread;
        if (Auth::id() !== $thread->user_id && !Auth::user()->hasRole('moderator')) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền đánh dấu solution cho thread này',
            ], 403);
        }

        $comment->markAsSolution();

        return response()->json([
            'success' => true,
            'message' => 'Comment đã được đánh dấu là solution',
            'data' => [
                'is_solution' => $comment->is_solution,
                'thread_solved' => $thread->fresh()->is_solved,
            ]
        ]);
    }

    /**
     * Bỏ đánh dấu comment là solution.
     */
    public function unmarkCommentAsSolution(Comment $comment): JsonResponse
    {
        $thread = $comment->thread;
        if (Auth::id() !== $thread->user_id && !Auth::user()->hasRole('moderator')) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền bỏ đánh dấu solution cho thread này',
            ], 403);
        }

        $comment->unmarkSolution();

        return response()->json([
            'success' => true,
            'message' => 'Comment đã được bỏ đánh dấu solution',
            'data' => [
                'is_solution' => $comment->is_solution,
                'thread_solved' => $thread->fresh()->is_solved,
            ]
        ]);
    }

    // =================
    // BATCH OPERATIONS
    // =================

    /**
     * Batch moderation cho nhiều threads.
     */
    public function batchModerationThreads(Request $request): JsonResponse
    {
        $request->validate([
            'thread_ids' => 'required|array',
            'thread_ids.*' => 'exists:threads,id',
            'action' => ['required', Rule::in(['flag', 'unflag', 'spam', 'unspam', 'archive', 'unarchive', 'hide', 'unhide'])],
            'reason' => 'nullable|string|max:500',
        ]);

        $threads = Thread::whereIn('id', $request->thread_ids)->get();
        $successCount = 0;

        foreach ($threads as $thread) {
            try {
                switch ($request->action) {
                    case 'flag':
                        $thread->flag(Auth::user(), $request->reason);
                        break;
                    case 'unflag':
                        $thread->unflag();
                        break;
                    case 'spam':
                        $thread->update(['is_spam' => true]);
                        break;
                    case 'unspam':
                        $thread->update(['is_spam' => false]);
                        break;
                    case 'archive':
                        $thread->archive($request->reason);
                        break;
                    case 'unarchive':
                        $thread->unarchive();
                        break;
                    case 'hide':
                        $thread->hide($request->reason);
                        break;
                    case 'unhide':
                        $thread->unhide();
                        break;
                }
                $successCount++;
            } catch (\Exception $e) {
                // Log lỗi nhưng tiếp tục xử lý
                \Log::error('Batch moderation error for thread ' . $thread->id . ': ' . $e->getMessage());
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Đã xử lý thành công {$successCount}/{$threads->count()} threads",
            'data' => [
                'processed_count' => $successCount,
                'total_count' => $threads->count(),
            ]
        ]);
    }

    // =================
    // MODERATION DASHBOARD
    // =================

    /**
     * Lấy danh sách threads cần moderation.
     */
    public function getPendingThreads(Request $request): JsonResponse
    {
        $query = Thread::with(['user', 'forum', 'category'])
            ->where(function ($query) {
                $query->where('is_flagged', true)
                    ->orWhere('is_spam', true)
                    ->orWhere('moderation_status', 'pending')
                    ->orWhere('reports_count', '>', 0);
            });

        // Filters
        if ($request->has('type')) {
            switch ($request->type) {
                case 'flagged':
                    $query->where('is_flagged', true);
                    break;
                case 'spam':
                    $query->where('is_spam', true);
                    break;
                case 'pending':
                    $query->where('moderation_status', 'pending');
                    break;
                case 'reported':
                    $query->where('reports_count', '>', 0);
                    break;
            }
        }

        $threads = $query->orderBy('updated_at', 'desc')
            ->paginate($request->get('per_page', 20));

        return response()->json([
            'success' => true,
            'data' => $threads,
        ]);
    }

    /**
     * Lấy danh sách comments cần moderation.
     */
    public function getPendingComments(Request $request): JsonResponse
    {
        $query = Comment::with(['user', 'thread'])
            ->where(function ($query) {
                $query->where('is_flagged', true)
                    ->orWhere('is_spam', true)
                    ->orWhere('reports_count', '>', 0);
            });

        // Filters
        if ($request->has('type')) {
            switch ($request->type) {
                case 'flagged':
                    $query->where('is_flagged', true);
                    break;
                case 'spam':
                    $query->where('is_spam', true);
                    break;
                case 'reported':
                    $query->where('reports_count', '>', 0);
                    break;
            }
        }

        $comments = $query->orderBy('updated_at', 'desc')
            ->paginate($request->get('per_page', 20));

        return response()->json([
            'success' => true,
            'data' => $comments,
        ]);
    }
}
