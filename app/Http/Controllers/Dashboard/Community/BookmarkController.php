<?php

namespace App\Http\Controllers\Dashboard\Community;

use App\Http\Controllers\Dashboard\BaseController;
use App\Models\ThreadBookmark;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * Bookmark Controller cho Dashboard Community
 *
 * Quản lý bookmarks của user trong dashboard
 */
class BookmarkController extends BaseController
{
    /**
     * Hiển thị danh sách bookmarks
     */
    public function index(Request $request)
    {
        $folder = $request->get('folder');
        $search = $request->get('search');
        $sort = $request->get('sort', 'newest');

        $query = ThreadBookmark::with(['thread' => function ($q) {
            $q->with(['user', 'forum'])
                ->withCount(['allComments as comments_count', 'ratings']);
        }])
            ->where('user_id', $this->user->id);

        // Filter by folder
        if ($folder) {
            $query->where('folder', $folder);
        }

        // Search in thread title or notes
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('notes', 'like', "%{$search}%")
                    ->orWhereHas('thread', function ($threadQuery) use ($search) {
                        $threadQuery->where('title', 'like', "%{$search}%");
                    });
            });
        }

        // Apply sorting
        switch ($sort) {
            case 'oldest':
                $query->oldest();
                break;
            case 'title':
                $query->join('threads', 'thread_bookmarks.thread_id', '=', 'threads.id')
                    ->orderBy('threads.title');
                break;
            case 'newest':
            default:
                $query->latest();
                break;
        }

        $bookmarks = $query->paginate(20);

        // Get folders for filter
        $folders = $this->getBookmarkFolders();

        // Get statistics
        $stats = $this->getBookmarkStats();

        return $this->dashboardResponse('dashboard.community.bookmarks.index', [
            'bookmarks' => $bookmarks,
            'folders' => $folders,
            'stats' => $stats,
            'currentFolder' => $folder,
            'search' => $search,
            'currentSort' => $sort
        ]);
    }

    /**
     * Tạo folder mới
     */
    public function createFolder(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:7', // hex color
        ]);

        $folder = ThreadBookmarkFolder::create([
            'user_id' => $this->user->id,
            'name' => $request->name,
            'description' => $request->description,
            'color' => $request->color ?? '#007bff',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Folder created successfully.',
            'folder' => $folder
        ]);
    }

    /**
     * Cập nhật folder
     */
    public function updateFolder(Request $request, ThreadBookmarkFolder $folder): JsonResponse
    {
        if ($folder->user_id !== $this->user->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:7',
        ]);

        $folder->update($request->only(['name', 'description', 'color']));

        return response()->json([
            'success' => true,
            'message' => 'Folder updated successfully.',
            'folder' => $folder
        ]);
    }

    /**
     * Xóa folder
     */
    public function deleteFolder(ThreadBookmarkFolder $folder): JsonResponse
    {
        if ($folder->user_id !== $this->user->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        // Move bookmarks to default folder
        ThreadBookmark::where('folder_id', $folder->id)
            ->update(['folder_id' => null]);

        $folder->delete();

        return response()->json([
            'success' => true,
            'message' => 'Folder deleted successfully.'
        ]);
    }

    /**
     * Cập nhật bookmark
     */
    public function updateBookmark(Request $request, ThreadBookmark $bookmark): JsonResponse
    {
        if ($bookmark->user_id !== $this->user->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'folder_id' => 'nullable|exists:thread_bookmark_folders,id',
            'notes' => 'nullable|string|max:500',
        ]);

        // Verify folder ownership if provided
        if ($request->folder_id) {
            $folder = ThreadBookmarkFolder::find($request->folder_id);
            if (!$folder || $folder->user_id !== $this->user->id) {
                return response()->json(['success' => false, 'message' => 'Invalid folder'], 400);
            }
        }

        $bookmark->update($request->only(['folder_id', 'notes']));

        return response()->json([
            'success' => true,
            'message' => 'Bookmark updated successfully.',
            'bookmark' => $bookmark->load('thread')
        ]);
    }

    /**
     * Xóa bookmark
     */
    public function deleteBookmark(ThreadBookmark $bookmark): JsonResponse
    {
        if ($bookmark->user_id !== $this->user->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $bookmark->delete();

        return response()->json([
            'success' => true,
            'message' => 'Bookmark deleted successfully.'
        ]);
    }

    /**
     * Bulk delete bookmarks
     */
    public function bulkDeleteBookmarks(Request $request): JsonResponse
    {
        $request->validate([
            'bookmark_ids' => 'required|array',
            'bookmark_ids.*' => 'exists:thread_bookmarks,id',
        ]);

        $deleted = ThreadBookmark::whereIn('id', $request->bookmark_ids)
            ->where('user_id', $this->user->id)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => "Deleted {$deleted} bookmarks successfully.",
            'deleted_count' => $deleted
        ]);
    }

    /**
     * Lấy danh sách folders
     */
    private function getBookmarkFolders()
    {
        // Lấy danh sách folders từ ThreadBookmark (distinct folder names)
        $folders = ThreadBookmark::where('user_id', $this->user->id)
            ->whereNotNull('folder')
            ->select('folder')
            ->selectRaw('COUNT(*) as bookmarks_count')
            ->groupBy('folder')
            ->orderBy('folder')
            ->get()
            ->map(function ($item) {
                return (object) [
                    'name' => $item->folder,
                    'bookmarks_count' => $item->bookmarks_count,
                ];
            });

        return $folders;
    }

    /**
     * Lấy thống kê bookmarks
     */
    private function getBookmarkStats()
    {
        $total = ThreadBookmark::where('user_id', $this->user->id)->count();
        $withNotes = ThreadBookmark::where('user_id', $this->user->id)
            ->whereNotNull('notes')
            ->where('notes', '!=', '')
            ->count();

        // Lấy folder stats từ ThreadBookmark
        $folderStats = ThreadBookmark::where('user_id', $this->user->id)
            ->whereNotNull('folder')
            ->select('folder')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('folder')
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->folder,
                    'count' => $item->count,
                    'color' => '#3B82F6' // Default color
                ];
            });

        return [
            'total' => $total,
            'with_notes' => $withNotes,
            'without_notes' => $total - $withNotes,
            'folders_count' => $folderStats->count(),
            'folder_stats' => $folderStats,
            'this_week' => ThreadBookmark::where('user_id', $this->user->id)
                ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
                ->count(),
            'this_month' => ThreadBookmark::where('user_id', $this->user->id)
                ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
                ->count(),
        ];
    }

    /**
     * Export bookmarks
     */
    public function exportBookmarks(Request $request)
    {
        $format = $request->get('format', 'json');

        $bookmarks = ThreadBookmark::with(['thread', 'folder'])
            ->where('user_id', $this->user->id)
            ->get()
            ->map(function ($bookmark) {
                return [
                    'thread_title' => $bookmark->thread->title,
                    'thread_url' => route('threads.show', $bookmark->thread),
                    'folder' => $bookmark->folder ? $bookmark->folder->name : 'Default',
                    'notes' => $bookmark->notes,
                    'bookmarked_at' => $bookmark->created_at->toISOString(),
                ];
            });

        $filename = 'bookmarks_' . $this->user->username . '_' . now()->format('Y-m-d');

        if ($format === 'csv') {
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"{$filename}.csv\"",
            ];

            $callback = function () use ($bookmarks) {
                $file = fopen('php://output', 'w');
                fputcsv($file, ['Thread Title', 'Thread URL', 'Folder', 'Notes', 'Bookmarked At']);

                foreach ($bookmarks as $bookmark) {
                    fputcsv($file, [
                        $bookmark['thread_title'],
                        $bookmark['thread_url'],
                        $bookmark['folder'],
                        $bookmark['notes'],
                        $bookmark['bookmarked_at'],
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

        // Default to JSON
        return response()->json($bookmarks)
            ->header('Content-Disposition', "attachment; filename=\"{$filename}.json\"");
    }
}
