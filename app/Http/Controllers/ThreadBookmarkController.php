<?php

namespace App\Http\Controllers;

use App\Models\Thread;
use App\Models\ThreadBookmark;
use App\Models\ThreadBookmarkFolder;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Controller xử lý bookmark threads
 * Quản lý việc bookmark threads với folders và notes
 */
class ThreadBookmarkController extends Controller
{
    /**
     * Bookmark một thread với folder và note
     */
    public function store(Request $request, Thread $thread): JsonResponse
    {
        try {
            // Validate thread exists and is accessible
            if (!$thread || !$thread->exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Thread không tồn tại hoặc đã bị xóa'
                ], 404);
            }

            $request->validate([
                'folder_name' => 'nullable|string|max:100',
                'notes' => 'nullable|string|max:500',
            ]);

            $user = Auth::user();

            // Kiểm tra đã bookmark chưa
            $existingBookmark = ThreadBookmark::where('user_id', $user->id)
                ->where('thread_id', $thread->id)
                ->first();

            if ($existingBookmark) {
                return response()->json([
                    'success' => false,
                    'message' => 'Thread đã được bookmark rồi',
                    'bookmarked' => true
                ], 409);
            }

            // Tạo bookmark mới
            $bookmark = ThreadBookmark::create([
                'user_id' => $user->id,
                'thread_id' => $thread->id,
                'folder' => $request->folder_name ?? null,
                'notes' => $request->notes,
            ]);

            // Increment bookmark count trong thread
            $thread->increment('bookmark_count');

            return response()->json([
                'success' => true,
                'message' => 'Thread đã được bookmark thành công',
                'bookmarked' => true,
                'bookmark' => [
                    'id' => $bookmark->id,
                    'folder' => $bookmark->folder,
                    'notes' => $bookmark->notes,
                    'created_at' => $bookmark->created_at->format('d/m/Y H:i'),
                ],
                'bookmark_count' => $thread->fresh()->bookmark_count
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Thread không tồn tại'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Lỗi khi bookmark thread: ' . $e->getMessage(), [
                'thread_id' => $thread->id ?? 'unknown',
                'user_id' => Auth::id(),
                'error' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi bookmark thread'
            ], 500);
        }
    }

    /**
     * Xóa bookmark của thread
     */
    public function destroy(Thread $thread): JsonResponse
    {
        try {
            // Validate thread exists and is accessible
            if (!$thread || !$thread->exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Thread không tồn tại hoặc đã bị xóa'
                ], 404);
            }

            $user = Auth::user();

            $bookmark = ThreadBookmark::where('user_id', $user->id)
                ->where('thread_id', $thread->id)
                ->first();

            if (!$bookmark) {
                return response()->json([
                    'success' => false,
                    'message' => 'Thread chưa được bookmark',
                    'bookmarked' => false
                ], 404);
            }

            // Xóa bookmark
            $bookmark->delete();

            // Decrement bookmark count trong thread
            $thread->decrement('bookmark_count');

            return response()->json([
                'success' => true,
                'message' => 'Đã xóa bookmark thành công',
                'bookmarked' => false,
                'bookmark_count' => $thread->fresh()->bookmark_count
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Thread không tồn tại'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Lỗi khi xóa bookmark thread: ' . $e->getMessage(), [
                'thread_id' => $thread->id ?? 'unknown',
                'user_id' => Auth::id(),
                'error' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xóa bookmark'
            ], 500);
        }
    }

    /**
     * Lấy danh sách folders để bookmark
     */
    public function getFolders(): JsonResponse
    {
        try {
            $user = Auth::user();

            // Lấy danh sách folders từ bookmarks hiện có
            $folders = ThreadBookmark::where('user_id', $user->id)
                ->whereNotNull('folder')
                ->distinct()
                ->pluck('folder')
                ->map(function ($folder) {
                    return [
                        'name' => $folder,
                        'value' => $folder
                    ];
                })
                ->values();

            return response()->json([
                'success' => true,
                'folders' => $folders
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi khi lấy danh sách folders: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tải folders'
            ], 500);
        }
    }

    /**
     * Tạo folder mới cho bookmark (đơn giản hóa)
     */
    public function createFolder(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'name' => 'required|string|max:100',
            ]);

            $user = Auth::user();

            // Kiểm tra tên folder đã tồn tại chưa
            $existingFolder = ThreadBookmark::where('user_id', $user->id)
                ->where('folder', $request->name)
                ->exists();

            if ($existingFolder) {
                return response()->json([
                    'success' => false,
                    'message' => 'Folder với tên này đã tồn tại'
                ], 409);
            }

            return response()->json([
                'success' => true,
                'message' => 'Folder có thể được sử dụng',
                'folder' => [
                    'name' => $request->name,
                    'value' => $request->name,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi khi tạo folder: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tạo folder'
            ], 500);
        }
    }

    /**
     * Cập nhật folder của bookmark
     */
    public function updateBookmarkFolder(Request $request, ThreadBookmark $bookmark): JsonResponse
    {
        try {
            $request->validate([
                'folder_name' => 'nullable|string|max:100',
                'notes' => 'nullable|string|max:500',
            ]);

            $user = Auth::user();

            // Kiểm tra quyền sở hữu bookmark
            if ($bookmark->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không có quyền chỉnh sửa bookmark này'
                ], 403);
            }

            // Cập nhật bookmark
            $bookmark->update([
                'folder' => $request->folder_name,
                'notes' => $request->notes,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật bookmark thành công',
                'bookmark' => [
                    'id' => $bookmark->id,
                    'folder' => $bookmark->folder,
                    'notes' => $bookmark->notes,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi khi cập nhật bookmark: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi cập nhật bookmark'
            ], 500);
        }
    }

    /**
     * Kiểm tra trạng thái bookmark của thread
     */
    public function status(Thread $thread): JsonResponse
    {
        try {
            $user = Auth::user();

            $bookmark = ThreadBookmark::where('user_id', $user->id)
                ->where('thread_id', $thread->id)
                ->first();

            return response()->json([
                'success' => true,
                'bookmarked' => $bookmark !== null,
                'bookmark' => $bookmark,
                'bookmark_count' => $thread->bookmark_count
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi kiểm tra trạng thái bookmark'
            ], 500);
        }
    }
}
