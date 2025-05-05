<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Showcase;
use App\Models\Thread;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ShowcaseController extends Controller
{
    /**
     * Add a thread to showcase
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addToShowcase(Request $request)
    {
        try {
            // Validate request
            $request->validate([
                'showcaseable_id' => 'required|integer',
                'showcaseable_type' => 'required|string|in:thread,post',
                'description' => 'nullable|string',
                'order' => 'nullable|integer',
            ]);

            // Check if item exists
            $showcaseableType = $request->showcaseable_type === 'thread' 
                ? Thread::class 
                : Post::class;
            
            $showcaseableId = $request->showcaseable_id;
            
            $item = $showcaseableType::find($showcaseableId);
            
            if (!$item) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy nội dung cần thêm vào showcase.'
                ], 404);
            }

            // Check if already in showcase
            $existingShowcase = Showcase::where('showcaseable_id', $showcaseableId)
                ->where('showcaseable_type', $showcaseableType)
                ->first();

            if ($existingShowcase) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nội dung này đã được thêm vào showcase.'
                ], 422);
            }

            // Create showcase
            $showcase = new Showcase();
            $showcase->user_id = Auth::id();
            $showcase->showcaseable_id = $showcaseableId;
            $showcase->showcaseable_type = $showcaseableType;
            $showcase->description = $request->description;
            $showcase->order = $request->order ?? 0;
            $showcase->save();

            // Load relationships
            $showcase->load(['user', 'showcaseable']);

            // Add user avatar URL
            if ($showcase->user) {
                $showcase->user->avatar_url = $showcase->user->getAvatarUrl();
            }

            return response()->json([
                'success' => true,
                'data' => $showcase,
                'message' => 'Thêm vào showcase thành công.'
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi thêm vào showcase.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove from showcase
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeFromShowcase($id)
    {
        try {
            $showcase = Showcase::findOrFail($id);

            // Check if user is authorized
            if (Auth::id() !== $showcase->user_id && !Auth::user()->hasRole(['admin', 'moderator'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền xóa khỏi showcase.'
                ], 403);
            }

            // Delete showcase
            $showcase->delete();

            return response()->json([
                'success' => true,
                'message' => 'Xóa khỏi showcase thành công.'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy showcase.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi xóa khỏi showcase.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * List all showcases
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $query = Showcase::query();

            // Filter by user
            if ($request->has('user_id')) {
                $query->where('user_id', $request->user_id);
            }

            // Filter by type
            if ($request->has('type')) {
                $type = $request->type;
                if ($type === 'thread') {
                    $query->where('showcaseable_type', Thread::class);
                } elseif ($type === 'post') {
                    $query->where('showcaseable_type', Post::class);
                }
            }

            // Sort by
            $sortBy = $request->input('sort_by', 'created_at');
            $sortOrder = $request->input('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Paginate
            $perPage = $request->input('per_page', 15);
            $showcases = $query->with(['user', 'showcaseable'])->paginate($perPage);

            // Add additional information
            $showcases->getCollection()->transform(function ($showcase) {
                // Add user avatar URL
                if ($showcase->user) {
                    $showcase->user->avatar_url = $showcase->user->getAvatarUrl();
                }

                // Add showcaseable information
                if ($showcase->showcaseable) {
                    if ($showcase->showcaseable_type === Thread::class) {
                        $showcase->title = $showcase->showcaseable->title;
                        $showcase->slug = $showcase->showcaseable->slug;
                    } elseif ($showcase->showcaseable_type === Post::class) {
                        $showcase->title = $showcase->showcaseable->thread->title;
                        $showcase->slug = $showcase->showcaseable->thread->slug;
                    }
                }

                return $showcase;
            });

            return response()->json([
                'success' => true,
                'data' => $showcases,
                'message' => 'Lấy danh sách showcase thành công.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi lấy danh sách showcase.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
