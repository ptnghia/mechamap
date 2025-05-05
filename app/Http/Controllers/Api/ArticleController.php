<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Thread;
use App\Models\Forum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ArticleController extends Controller
{
    /**
     * Get recent news and articles
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRecent(Request $request)
    {
        try {
            // Get pagination parameters
            $perPage = $request->input('per_page', 10);
            
            // Get news forum IDs (forums with names containing 'news' or 'article')
            $newsForumIds = Forum::where('name', 'like', '%news%')
                ->orWhere('name', 'like', '%article%')
                ->orWhere('name', 'like', '%tin tức%')
                ->orWhere('description', 'like', '%news%')
                ->pluck('id')
                ->toArray();
            
            // Get recent news and articles
            $articles = Thread::whereHas('forum', function ($query) use ($newsForumIds) {
                    $query->whereIn('id', $newsForumIds);
                })
                ->with(['user', 'forum'])
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);
            
            // Add user avatar URL
            $articles->getCollection()->transform(function ($article) {
                if ($article->user) {
                    $article->user->avatar_url = $article->user->getAvatarUrl();
                }
                return $article;
            });
            
            return response()->json([
                'success' => true,
                'data' => $articles,
                'message' => 'Lấy danh sách tin tức và bài viết mới nhất thành công.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi lấy danh sách tin tức và bài viết mới nhất.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
