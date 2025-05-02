<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Alert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AlertController extends Controller
{
    /**
     * Get a list of alerts for the authenticated user
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $query = Auth::user()->alerts();
            
            // Filter by read status
            if ($request->has('read')) {
                if ($request->boolean('read')) {
                    $query->read();
                } else {
                    $query->unread();
                }
            }
            
            // Sort by
            $sortBy = $request->input('sort_by', 'created_at');
            $sortOrder = $request->input('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);
            
            // Paginate
            $perPage = $request->input('per_page', 15);
            $alerts = $query->paginate($perPage);
            
            // Get unread count
            $unreadCount = Auth::user()->alerts()->unread()->count();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'alerts' => $alerts,
                    'unread_count' => $unreadCount,
                ],
                'message' => 'Lấy danh sách thông báo thành công.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi lấy danh sách thông báo.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Mark an alert as read
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAsRead($id)
    {
        try {
            $alert = Alert::findOrFail($id);
            
            // Check if the alert belongs to the authenticated user
            if ($alert->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền đánh dấu thông báo này đã đọc.'
                ], 403);
            }
            
            // Mark as read
            $alert->markAsRead();
            
            // Get unread count
            $unreadCount = Auth::user()->alerts()->unread()->count();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'unread_count' => $unreadCount,
                ],
                'message' => 'Đánh dấu thông báo đã đọc thành công.'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thông báo.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi đánh dấu thông báo đã đọc.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Delete an alert
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $alert = Alert::findOrFail($id);
            
            // Check if the alert belongs to the authenticated user
            if ($alert->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền xóa thông báo này.'
                ], 403);
            }
            
            // Delete alert
            $alert->delete();
            
            // Get unread count
            $unreadCount = Auth::user()->alerts()->unread()->count();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'unread_count' => $unreadCount,
                ],
                'message' => 'Xóa thông báo thành công.'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thông báo.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi xóa thông báo.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Mark all alerts as read
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAllAsRead()
    {
        try {
            // Get all unread alerts
            $alerts = Auth::user()->alerts()->unread()->get();
            
            // Mark all as read
            foreach ($alerts as $alert) {
                $alert->markAsRead();
            }
            
            return response()->json([
                'success' => true,
                'data' => [
                    'unread_count' => 0,
                ],
                'message' => 'Đánh dấu tất cả thông báo đã đọc thành công.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi đánh dấu tất cả thông báo đã đọc.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
