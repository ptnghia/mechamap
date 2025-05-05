<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Thread;
use App\Models\Comment;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    /**
     * Report a thread
     *
     * @param Request $request
     * @param string $slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function reportThread(Request $request, $slug)
    {
        try {
            $thread = Thread::where('slug', $slug)->firstOrFail();

            // Validate request
            $request->validate([
                'reason' => 'required|string|max:255',
                'description' => 'nullable|string',
            ]);

            // Check if already reported by this user
            $existingReport = Report::where('user_id', Auth::id())
                ->where('reportable_id', $thread->id)
                ->where('reportable_type', Thread::class)
                ->first();

            if ($existingReport) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn đã báo cáo chủ đề này rồi.'
                ], 400);
            }

            // Create report
            $report = new Report([
                'user_id' => Auth::id(),
                'reason' => $request->reason,
                'description' => $request->description,
                'status' => 'pending',
            ]);

            $thread->reports()->save($report);

            return response()->json([
                'success' => true,
                'message' => 'Báo cáo đã được gửi thành công.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi gửi báo cáo.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Report a comment
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function reportComment(Request $request, $id)
    {
        try {
            $comment = Comment::findOrFail($id);

            // Validate request
            $request->validate([
                'reason' => 'required|string|max:255',
                'description' => 'nullable|string',
            ]);

            // Check if already reported by this user
            $existingReport = Report::where('user_id', Auth::id())
                ->where('reportable_id', $comment->id)
                ->where('reportable_type', Comment::class)
                ->first();

            if ($existingReport) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn đã báo cáo bình luận này rồi.'
                ], 400);
            }

            // Create report
            $report = new Report([
                'user_id' => Auth::id(),
                'reason' => $request->reason,
                'description' => $request->description,
                'status' => 'pending',
            ]);

            $comment->reports()->save($report);

            return response()->json([
                'success' => true,
                'message' => 'Báo cáo đã được gửi thành công.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi gửi báo cáo.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get reports for admin
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            // Check if user is admin or moderator
            if (Auth::user()->role !== 'admin' && Auth::user()->role !== 'moderator') {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền xem danh sách báo cáo.'
                ], 403);
            }

            // Get pagination parameters
            $perPage = $request->input('per_page', 15);
            $status = $request->input('status');

            // Build query
            $query = Report::with(['user', 'reportable']);

            // Filter by status
            if ($status) {
                $query->where('status', $status);
            }

            // Sort by created_at desc
            $query->orderBy('created_at', 'desc');

            // Paginate
            $reports = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $reports,
                'message' => 'Lấy danh sách báo cáo thành công.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi lấy danh sách báo cáo.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update report status
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            // Check if user is admin or moderator
            if (Auth::user()->role !== 'admin' && Auth::user()->role !== 'moderator') {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền cập nhật trạng thái báo cáo.'
                ], 403);
            }

            // Validate request
            $request->validate([
                'status' => 'required|in:pending,resolved,rejected',
            ]);

            // Find report
            $report = Report::findOrFail($id);

            // Update status
            $report->status = $request->status;
            $report->save();

            return response()->json([
                'success' => true,
                'data' => $report,
                'message' => 'Cập nhật trạng thái báo cáo thành công.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi cập nhật trạng thái báo cáo.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
