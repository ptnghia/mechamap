<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Showcase;
use App\Models\ShowcaseRating;
use App\Models\User;
use App\Services\WebSocketNotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

/**
 * ShowcaseRatingController - API cho hệ thống đánh giá showcase
 *
 * Endpoints:
 * - POST /api/showcases/{showcase}/ratings - Tạo đánh giá mới
 * - PUT /api/ratings/{rating} - Cập nhật đánh giá
 * - DELETE /api/ratings/{rating} - Xóa đánh giá
 * - POST /api/ratings/{rating}/like - Toggle like đánh giá
 * - GET /api/showcases/{showcase}/ratings - Lấy danh sách đánh giá
 */
class ShowcaseRatingController extends Controller
{
    protected WebSocketNotificationService $notificationService;

    public function __construct(WebSocketNotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }
    /**
     * Tạo đánh giá mới cho showcase
     */
    public function store(Request $request, Showcase $showcase): JsonResponse
    {
        try {
            // Kiểm tra user đã đăng nhập
            if (!Auth::check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn cần đăng nhập để đánh giá showcase này.',
                ], 401);
            }

            $user = Auth::user();

            // Kiểm tra user đã đánh giá showcase này chưa
            $existingRating = $showcase->ratings()->where('user_id', $user->id)->first();
            if ($existingRating) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn đã đánh giá showcase này rồi. Vui lòng chỉnh sửa đánh giá hiện tại.',
                    'rating_id' => $existingRating->id,
                ], 422);
            }

            // Validate input
            $validated = $request->validate([
                'technical_quality' => 'required|integer|min:1|max:5',
                'innovation' => 'required|integer|min:1|max:5',
                'usefulness' => 'required|integer|min:1|max:5',
                'documentation' => 'required|integer|min:1|max:5',
                'review' => 'nullable|string|max:2000',
                'images' => 'nullable|array|max:10',
                'images.*' => 'image|mimes:jpg,jpeg,png,gif,webp|max:5120', // 5MB max
            ]);

            DB::beginTransaction();

            // Xử lý upload images nếu có
            $imagePaths = [];
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $fileName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                    $path = $image->storeAs('images/ratings', $fileName, 'public');
                    $imagePaths[] = $path;
                }
            }

            // Tạo rating mới
            $rating = $showcase->ratings()->create([
                'user_id' => $user->id,
                'technical_quality' => $validated['technical_quality'],
                'innovation' => $validated['innovation'],
                'usefulness' => $validated['usefulness'],
                'documentation' => $validated['documentation'],
                'review' => $validated['review'],
                'images' => !empty($imagePaths) ? $imagePaths : null,
                'has_media' => !empty($imagePaths),
                'like_count' => 0,
            ]);

            // Load relationships cho response
            $rating->load(['user', 'likes']);

            DB::commit();

            // Gửi notification real-time
            $this->notificationService->sendRatingNotification($showcase, $rating, 'created');

            return response()->json([
                'success' => true,
                'message' => 'Đánh giá của bạn đã được gửi thành công!',
                'data' => [
                    'rating' => $this->formatRatingResponse($rating),
                    'showcase_stats' => $this->getShowcaseStats($showcase),
                ],
            ], 201);

        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ.',
                'errors' => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating showcase rating: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi gửi đánh giá. Vui lòng thử lại.',
            ], 500);
        }
    }

    /**
     * Cập nhật đánh giá
     */
    public function update(Request $request, ShowcaseRating $rating): JsonResponse
    {
        try {
            $user = Auth::user();

            // Kiểm tra quyền sửa đánh giá
            if (!$user || $rating->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền chỉnh sửa đánh giá này.',
                ], 403);
            }

            // Validate input
            $validated = $request->validate([
                'technical_quality' => 'required|integer|min:1|max:5',
                'innovation' => 'required|integer|min:1|max:5',
                'usefulness' => 'required|integer|min:1|max:5',
                'documentation' => 'required|integer|min:1|max:5',
                'review' => 'nullable|string|max:2000',
                'images' => 'nullable|array|max:10',
                'images.*' => 'image|mimes:jpg,jpeg,png,gif,webp|max:5120',
                'remove_images' => 'nullable|array',
                'remove_images.*' => 'string',
            ]);

            DB::beginTransaction();

            // Xử lý xóa images cũ
            $currentImages = $rating->images ?? [];
            if ($request->has('remove_images')) {
                foreach ($request->remove_images as $imageToRemove) {
                    if (($key = array_search($imageToRemove, $currentImages)) !== false) {
                        // Xóa file khỏi storage
                        Storage::disk('public')->delete($imageToRemove);
                        unset($currentImages[$key]);
                    }
                }
                $currentImages = array_values($currentImages); // Re-index array
            }

            // Xử lý upload images mới
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $fileName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                    $path = $image->storeAs('images/ratings', $fileName, 'public');
                    $currentImages[] = $path;
                }
            }

            // Cập nhật rating
            $rating->update([
                'technical_quality' => $validated['technical_quality'],
                'innovation' => $validated['innovation'],
                'usefulness' => $validated['usefulness'],
                'documentation' => $validated['documentation'],
                'review' => $validated['review'],
                'images' => !empty($currentImages) ? $currentImages : null,
                'has_media' => !empty($currentImages),
            ]);

            $rating->load(['user', 'likes']);

            DB::commit();

            // Gửi notification real-time
            $this->notificationService->sendRatingNotification($rating->showcase, $rating, 'updated');

            return response()->json([
                'success' => true,
                'message' => 'Đánh giá đã được cập nhật thành công!',
                'data' => [
                    'rating' => $this->formatRatingResponse($rating),
                    'showcase_stats' => $this->getShowcaseStats($rating->showcase),
                ],
            ]);

        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ.',
                'errors' => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating showcase rating: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi cập nhật đánh giá. Vui lòng thử lại.',
            ], 500);
        }
    }

    /**
     * Xóa đánh giá
     */
    public function destroy(ShowcaseRating $rating): JsonResponse
    {
        try {
            $user = Auth::user();

            // Kiểm tra quyền xóa đánh giá
            if (!$user || ($rating->user_id !== $user->id && !$user->hasRole(['super_admin', 'admin']))) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền xóa đánh giá này.',
                ], 403);
            }

            DB::beginTransaction();

            $showcase = $rating->showcase;

            // Xóa images nếu có
            if ($rating->images) {
                foreach ($rating->images as $imagePath) {
                    Storage::disk('public')->delete($imagePath);
                }
            }

            // Xóa rating (cascade sẽ xóa likes và replies)
            $rating->delete();

            DB::commit();

            // Gửi notification real-time (skip for delete - không cần thiết)
            // $this->notificationService->sendRatingNotification($showcase, null, 'deleted');

            return response()->json([
                'success' => true,
                'message' => 'Đánh giá đã được xóa thành công!',
                'data' => [
                    'showcase_stats' => $this->getShowcaseStats($showcase),
                ],
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error deleting showcase rating: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xóa đánh giá. Vui lòng thử lại.',
            ], 500);
        }
    }

    /**
     * Toggle like/unlike đánh giá
     */
    public function toggleLike(ShowcaseRating $rating): JsonResponse
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn cần đăng nhập để thích đánh giá này.',
                ], 401);
            }

            // Không thể like đánh giá của chính mình
            if ($rating->user_id === $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không thể thích đánh giá của chính mình.',
                ], 422);
            }

            $isLiked = $rating->toggleLike($user);

            // Gửi notification real-time nếu là like (không gửi khi unlike)
            if ($isLiked) {
                $this->notificationService->sendLikeNotification(
                    'rating_liked',
                    $rating->id,
                    $user,
                    [
                        'author_id' => $rating->user_id,
                        'showcase_id' => $rating->showcase_id,
                    ]
                );
            }

            return response()->json([
                'success' => true,
                'message' => $isLiked ? 'Đã thích đánh giá!' : 'Đã bỏ thích đánh giá!',
                'data' => [
                    'is_liked' => $isLiked,
                    'like_count' => $rating->fresh()->like_count,
                ],
            ]);

        } catch (\Exception $e) {
            \Log::error('Error toggling rating like: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra. Vui lòng thử lại.',
            ], 500);
        }
    }

    /**
     * Lấy danh sách đánh giá của showcase
     */
    public function index(Request $request, Showcase $showcase): JsonResponse
    {
        try {
            $perPage = min($request->get('per_page', 10), 50);
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');

            $ratings = $showcase->ratings()
                ->with(['user', 'likes', 'replies.user'])
                ->orderBy($sortBy, $sortOrder)
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => [
                    'ratings' => $ratings->items(),
                    'pagination' => [
                        'current_page' => $ratings->currentPage(),
                        'last_page' => $ratings->lastPage(),
                        'per_page' => $ratings->perPage(),
                        'total' => $ratings->total(),
                        'has_more' => $ratings->hasMorePages(),
                    ],
                    'showcase_stats' => $this->getShowcaseStats($showcase),
                ],
            ]);

        } catch (\Exception $e) {
            \Log::error('Error fetching showcase ratings: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tải đánh giá.',
            ], 500);
        }
    }

    /**
     * Format rating response
     */
    private function formatRatingResponse(ShowcaseRating $rating): array
    {
        return [
            'id' => $rating->id,
            'user' => [
                'id' => $rating->user->id,
                'name' => $rating->user->display_name,
                'username' => $rating->user->username,
                'avatar_url' => $rating->user->getAvatarUrl(),
            ],
            'technical_quality' => $rating->technical_quality,
            'innovation' => $rating->innovation,
            'usefulness' => $rating->usefulness,
            'documentation' => $rating->documentation,
            'overall_rating' => $rating->overall_rating,
            'review' => $rating->review,
            'has_media' => $rating->has_media,
            'images' => $rating->image_urls,
            'like_count' => $rating->like_count,
            'is_liked' => Auth::check() ? $rating->isLikedBy(Auth::user()) : false,
            'created_at' => $rating->created_at->toISOString(),
            'created_at_human' => $rating->created_at->diffForHumans(),
        ];
    }

    /**
     * Get showcase statistics
     */
    private function getShowcaseStats(Showcase $showcase): array
    {
        $ratings = $showcase->ratings;

        return [
            'total_ratings' => $ratings->count(),
            'average_rating' => $ratings->avg('overall_rating'),
            'ratings_breakdown' => [
                'technical_quality' => $ratings->avg('technical_quality'),
                'innovation' => $ratings->avg('innovation'),
                'usefulness' => $ratings->avg('usefulness'),
                'documentation' => $ratings->avg('documentation'),
            ],
        ];
    }


}
