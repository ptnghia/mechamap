<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Thread;
use App\Models\Forum;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    /**
     * Search for threads, forums, and users
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        try {
            // Validate request
            $request->validate([
                'query' => 'required|string|min:2',
                'type' => 'nullable|string|in:all,threads,forums,users',
                'per_page' => 'nullable|integer|min:1|max:50',
            ]);
            
            $query = $request->query('query');
            $type = $request->query('type', 'all');
            $perPage = $request->query('per_page', 15);
            
            $results = [];
            
            // Search threads
            if ($type === 'all' || $type === 'threads') {
                $threads = Thread::where(function ($q) use ($query) {
                    $q->where('title', 'like', "%{$query}%")
                        ->orWhere('content', 'like', "%{$query}%");
                })
                ->where('status', 'approved')
                ->with(['user', 'forum'])
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);
                
                // Add user avatar URL
                $threads->getCollection()->transform(function ($thread) {
                    if ($thread->user) {
                        $thread->user->avatar_url = $thread->user->getAvatarUrl();
                    }
                    return $thread;
                });
                
                $results['threads'] = $threads;
            }
            
            // Search forums
            if ($type === 'all' || $type === 'forums') {
                $forums = Forum::where(function ($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%")
                        ->orWhere('description', 'like', "%{$query}%");
                })
                ->orderBy('name', 'asc')
                ->paginate($perPage);
                
                $results['forums'] = $forums;
            }
            
            // Search users
            if ($type === 'all' || $type === 'users') {
                $users = User::where(function ($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%")
                        ->orWhere('username', 'like', "%{$query}%");
                })
                ->where('status', 'active')
                ->orderBy('name', 'asc')
                ->paginate($perPage);
                
                // Add avatar URL
                $users->getCollection()->transform(function ($user) {
                    $user->avatar_url = $user->getAvatarUrl();
                    return $user;
                });
                
                $results['users'] = $users;
            }
            
            return response()->json([
                'success' => true,
                'data' => $results,
                'message' => 'Tìm kiếm thành công.'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi tìm kiếm.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get search suggestions
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function suggestions(Request $request)
    {
        try {
            // Validate request
            $request->validate([
                'query' => 'required|string|min:2',
                'limit' => 'nullable|integer|min:1|max:20',
            ]);
            
            $query = $request->query('query');
            $limit = $request->query('limit', 10);
            
            $suggestions = [];
            
            // Thread title suggestions
            $threadSuggestions = Thread::where('title', 'like', "%{$query}%")
                ->where('status', 'approved')
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->pluck('title')
                ->toArray();
            
            // Forum name suggestions
            $forumSuggestions = Forum::where('name', 'like', "%{$query}%")
                ->orderBy('name', 'asc')
                ->limit($limit)
                ->pluck('name')
                ->toArray();
            
            // User name suggestions
            $userSuggestions = User::where('name', 'like', "%{$query}%")
                ->orWhere('username', 'like', "%{$query}%")
                ->where('status', 'active')
                ->orderBy('name', 'asc')
                ->limit($limit)
                ->get(['name', 'username'])
                ->map(function ($user) {
                    return $user->name . ' (@' . $user->username . ')';
                })
                ->toArray();
            
            // Merge suggestions
            $suggestions = array_merge($threadSuggestions, $forumSuggestions, $userSuggestions);
            
            // Remove duplicates and limit
            $suggestions = array_unique($suggestions);
            $suggestions = array_slice($suggestions, 0, $limit);
            
            return response()->json([
                'success' => true,
                'data' => $suggestions,
                'message' => 'Lấy gợi ý tìm kiếm thành công.'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi lấy gợi ý tìm kiếm.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
