<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Forum;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ForumController extends Controller
{
    /**
     * Get a list of forums
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $query = Forum::query();
            
            // Filter by parent_id
            if ($request->has('parent_id')) {
                $query->where('parent_id', $request->parent_id);
            } else {
                // Get only top-level forums by default
                $query->whereNull('parent_id');
            }
            
            // Include sub-forums if requested
            $withSubForums = $request->boolean('with_sub_forums', false);
            
            // Sort by order
            $query->orderBy('order');
            
            // Get forums
            $forums = $query->get();
            
            // Include sub-forums if requested
            if ($withSubForums) {
                $forums->load('subForums');
            }
            
            // Include thread counts
            $forums->each(function ($forum) {
                $forum->threads_count = $forum->threads()->count();
                $forum->latest_thread = $forum->threads()->latest()->first();
            });
            
            return response()->json([
                'success' => true,
                'data' => $forums,
                'message' => 'Lấy danh sách diễn đàn thành công.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi lấy danh sách diễn đàn.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get a forum by slug
     *
     * @param string $slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($slug)
    {
        try {
            $forum = Forum::where('slug', $slug)->firstOrFail();
            
            // Load sub-forums
            $forum->load('subForums');
            
            // Include thread counts
            $forum->threads_count = $forum->threads()->count();
            
            // Include parent forum if exists
            if ($forum->parent_id) {
                $forum->load('parent');
            }
            
            return response()->json([
                'success' => true,
                'data' => $forum,
                'message' => 'Lấy thông tin diễn đàn thành công.'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy diễn đàn.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi lấy thông tin diễn đàn.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get threads in a forum
     *
     * @param Request $request
     * @param string $slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function getThreads(Request $request, $slug)
    {
        try {
            $forum = Forum::where('slug', $slug)->firstOrFail();
            
            $query = $forum->threads();
            
            // Filter by status
            if ($request->has('status')) {
                $query->where('status', $request->status);
            } else {
                // Only show approved threads by default
                $query->where('status', 'approved');
            }
            
            // Filter by sticky
            if ($request->has('is_sticky')) {
                $query->where('is_sticky', $request->boolean('is_sticky'));
            }
            
            // Filter by locked
            if ($request->has('is_locked')) {
                $query->where('is_locked', $request->boolean('is_locked'));
            }
            
            // Sort by
            $sortBy = $request->input('sort_by', 'created_at');
            $sortOrder = $request->input('sort_order', 'desc');
            
            // Special case for "activity" sort
            if ($sortBy === 'activity') {
                $query->orderByRaw('COALESCE(last_comment_at, created_at) ' . $sortOrder);
            } else {
                $query->orderBy($sortBy, $sortOrder);
            }
            
            // Include sticky threads at the top if sorting by activity or created_at
            if (in_array($sortBy, ['activity', 'created_at']) && $sortOrder === 'desc') {
                $query->orderBy('is_sticky', 'desc');
            }
            
            // Paginate
            $perPage = $request->input('per_page', 15);
            $threads = $query->with(['user', 'forum'])->paginate($perPage);
            
            // Include additional information
            $threads->getCollection()->transform(function ($thread) {
                // Add user avatar URL
                if ($thread->user) {
                    $thread->user->avatar_url = $thread->user->getAvatarUrl();
                }
                
                return $thread;
            });
            
            return response()->json([
                'success' => true,
                'data' => $threads,
                'message' => 'Lấy danh sách chủ đề trong diễn đàn thành công.'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy diễn đàn.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi lấy danh sách chủ đề trong diễn đàn.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get a list of categories
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCategories(Request $request)
    {
        try {
            $query = Category::query();
            
            // Filter by parent_id
            if ($request->has('parent_id')) {
                $query->where('parent_id', $request->parent_id);
            } else {
                // Get only top-level categories by default
                $query->whereNull('parent_id');
            }
            
            // Include children if requested
            $withChildren = $request->boolean('with_children', false);
            
            // Sort by order
            $query->orderBy('order');
            
            // Get categories
            $categories = $query->get();
            
            // Include children if requested
            if ($withChildren) {
                $categories->load('children');
            }
            
            return response()->json([
                'success' => true,
                'data' => $categories,
                'message' => 'Lấy danh sách danh mục thành công.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi lấy danh sách danh mục.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get a category by slug
     *
     * @param string $slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCategory($slug)
    {
        try {
            $category = Category::where('slug', $slug)->firstOrFail();
            
            // Load children
            $category->load('children');
            
            // Include parent category if exists
            if ($category->parent_id) {
                $category->load('parent');
            }
            
            return response()->json([
                'success' => true,
                'data' => $category,
                'message' => 'Lấy thông tin danh mục thành công.'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy danh mục.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi lấy thông tin danh mục.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get forums in a category
     *
     * @param Request $request
     * @param string $slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCategoryForums(Request $request, $slug)
    {
        try {
            $category = Category::where('slug', $slug)->firstOrFail();
            
            // Get forums in this category
            $forums = Forum::where('category_id', $category->id)
                ->orderBy('order')
                ->get();
            
            // Include thread counts
            $forums->each(function ($forum) {
                $forum->threads_count = $forum->threads()->count();
                $forum->latest_thread = $forum->threads()->latest()->first();
            });
            
            return response()->json([
                'success' => true,
                'data' => $forums,
                'message' => 'Lấy danh sách diễn đàn trong danh mục thành công.'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy danh mục.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi lấy danh sách diễn đàn trong danh mục.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
