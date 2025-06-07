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
            $request->validate([
                'folder_id' => 'nullable|exists:thread_bookmark_folders,id',
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
                'folder_id' => $request->folder_id,
                'notes' => $request->notes,
                'bookmarked_at' => now(),
            ]);

            // Increment bookmark count trong thread
            $thread->increment('bookmark_count');

            // Load folder info nếu có
            $bookmark->load('folder');

            return response()->json([
                'success' => true,
                'message' => 'Thread đã được bookmark thành công',
                'bookmarked' => true,
                'bookmark' => [
                    'id' => $bookmark->id,
                    'folder' => $bookmark->folder ? [
                        'id' => $bookmark->folder->id,
                        'name' => $bookmark->folder->name,
                        'color' => $bookmark->folder->color,
                    ] : null,
                    'notes' => $bookmark->notes,
                    'bookmarked_at' => $bookmark->bookmarked_at->format('d/m/Y H:i'),
                ],
                'bookmark_count' => $thread->fresh()->bookmark_count
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi khi bookmark thread: ' . $e->getMessage(), [
                'thread_id' => $thread->id,
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
        } catch (\Exception $e) {
            Log::error('Lỗi khi xóa bookmark thread: ' . $e->getMessage(), [
                'thread_id' => $thread->id,
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

            $folders = ThreadBookmarkFolder::where('user_id', $user->id)
                ->orderBy('name')
                ->get(['id', 'name', 'color', 'description']);

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
     * Tạo folder mới cho bookmark
     */
    public function createFolder(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'name' => 'required|string|max:100',
                'color' => 'nullable|string|max:7',
                'description' => 'nullable|string|max:255',
            ]);

            $user = Auth::user();

            // Kiểm tra tên folder đã tồn tại chưa
            $existingFolder = ThreadBookmarkFolder::where('user_id', $user->id)
                ->where('name', $request->name)
                ->exists();

            if ($existingFolder) {
                return response()->json([
                    'success' => false,
                    'message' => 'Folder với tên này đã tồn tại'
                ], 409);
            }

            $folder = ThreadBookmarkFolder::create([
                'user_id' => $user->id,
                'name' => $request->name,
                'color' => $request->color ?? '#3B82F6',
                'description' => $request->description,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tạo folder thành công',
                'folder' => [
                    'id' => $folder->id,
                    'name' => $folder->name,
                    'color' => $folder->color,
                    'description' => $folder->description,
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
                'folder_id' => 'nullable|exists:thread_bookmark_folders,id',
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

            // Nếu có folder_id, kiểm tra folder thuộc về user
            if ($request->folder_id) {
                $folder = ThreadBookmarkFolder::where('id', $request->folder_id)
                    ->where('user_id', $user->id)
                    ->first();

                if (!$folder) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Folder không tồn tại hoặc không thuộc về bạn'
                    ], 404);
                }
            }

            // Cập nhật bookmark
            $bookmark->update([
                'folder_id' => $request->folder_id,
                'notes' => $request->notes,
            ]);

            // Load folder info
            $bookmark->load('folder');

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật bookmark thành công',
                'bookmark' => [
                    'id' => $bookmark->id,
                    'folder' => $bookmark->folder ? [
                        'id' => $bookmark->folder->id,
                        'name' => $bookmark->folder->name,
                        'color' => $bookmark->folder->color,
                    ] : null,
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
}
