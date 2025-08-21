<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ShowcaseRating;
use App\Models\ShowcaseRatingReply;
use App\Models\User;
use App\Services\WebSocketNotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

/**
 * ShowcaseRatingReplyController - API cho hệ thống reply đánh giá
 *
 * Endpoints:
 * - POST /api/ratings/{rating}/replies - Tạo reply mới
 * - PUT /api/replies/{reply} - Cập nhật reply
 * - DELETE /api/replies/{reply} - Xóa reply
 * - POST /api/replies/{reply}/like - Toggle like reply
 * - GET /api/ratings/{rating}/replies - Lấy danh sách replies
 */
class ShowcaseRatingReplyController extends Controller
{
    protected WebSocketNotificationService $notificationService;

    public function __construct(WebSocketNotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }
    /**
     * Tạo reply mới cho đánh giá
     */
    public function store(Request $request, ShowcaseRating $rating): JsonResponse
    {
        try {
            if (!Auth::check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn cần đăng nhập để trả lời đánh giá này.',
                ], 401);
            }

            $user = Auth::user();

            // Validate input
            $validated = $request->validate([
                'content' => 'required|string|max:1000',
                'parent_id' => 'nullable|exists:showcase_rating_replies,id',
                'images' => 'nullable|array|max:5',
                'images.*' => 'image|mimes:jpg,jpeg,png,gif,webp|max:5120',
            ]);

            // Kiểm tra parent_id thuộc về cùng rating
            if ($validated['parent_id']) {
                $parentReply = ShowcaseRatingReply::find($validated['parent_id']);
                if (!$parentReply || $parentReply->rating_id !== $rating->id) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Reply cha không hợp lệ.',
                    ], 422);
                }
            }

            DB::beginTransaction();

            // Xử lý upload images nếu có
            $imagePaths = [];
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $fileName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                    $path = $image->storeAs('images/rating-replies', $fileName, 'public');
                    $imagePaths[] = $path;
                }
            }

            // Tạo reply mới
            $reply = $rating->replies()->create([
                'user_id' => $user->id,
                'parent_id' => $validated['parent_id'],
                'content' => $validated['content'],
                'images' => !empty($imagePaths) ? $imagePaths : null,
                'has_media' => !empty($imagePaths),
                'like_count' => 0,
            ]);

            $reply->load(['user', 'likes']);

            DB::commit();

            // Gửi notification real-time
            $this->notificationService->sendReplyNotification($rating, $reply, 'created');

            return response()->json([
                'success' => true,
                'message' => 'Trả lời của bạn đã được gửi thành công!',
                'data' => [
                    'reply' => $this->formatReplyResponse($reply),
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
            \Log::error('Error creating rating reply: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi gửi trả lời. Vui lòng thử lại.',
            ], 500);
        }
    }

    /**
     * Cập nhật reply
     */
    public function update(Request $request, ShowcaseRatingReply $reply): JsonResponse
    {
        try {
            $user = Auth::user();

            if (!$user || $reply->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền chỉnh sửa trả lời này.',
                ], 403);
            }

            // Validate input
            $validated = $request->validate([
                'content' => 'required|string|max:1000',
                'images' => 'nullable|array|max:5',
                'images.*' => 'image|mimes:jpg,jpeg,png,gif,webp|max:5120',
                'remove_images' => 'nullable|array',
                'remove_images.*' => 'string',
            ]);

            DB::beginTransaction();

            // Xử lý xóa images cũ
            $currentImages = $reply->images ?? [];
            if ($request->has('remove_images')) {
                foreach ($request->remove_images as $imageToRemove) {
                    if (($key = array_search($imageToRemove, $currentImages)) !== false) {
                        Storage::disk('public')->delete($imageToRemove);
                        unset($currentImages[$key]);
                    }
                }
                $currentImages = array_values($currentImages);
            }

            // Xử lý upload images mới
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $fileName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                    $path = $image->storeAs('images/rating-replies', $fileName, 'public');
                    $currentImages[] = $path;
                }
            }

            // Cập nhật reply
            $reply->update([
                'content' => $validated['content'],
                'images' => !empty($currentImages) ? $currentImages : null,
                'has_media' => !empty($currentImages),
            ]);

            $reply->load(['user', 'likes']);

            DB::commit();

            // Gửi notification real-time
            $this->notificationService->sendReplyNotification($reply->rating, $reply, 'updated');

            return response()->json([
                'success' => true,
                'message' => 'Trả lời đã được cập nhật thành công!',
                'data' => [
                    'reply' => $this->formatReplyResponse($reply),
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
            \Log::error('Error updating rating reply: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi cập nhật trả lời. Vui lòng thử lại.',
            ], 500);
        }
    }

    /**
     * Xóa reply
     */
    public function destroy(ShowcaseRatingReply $reply): JsonResponse
    {
        try {
            $user = Auth::user();

            if (!$user || ($reply->user_id !== $user->id && !$user->hasRole(['super_admin', 'admin']))) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền xóa trả lời này.',
                ], 403);
            }

            DB::beginTransaction();

            $rating = $reply->rating;

            // Xóa images nếu có
            if ($reply->images) {
                foreach ($reply->images as $imagePath) {
                    Storage::disk('public')->delete($imagePath);
                }
            }

            // Xóa reply (cascade sẽ xóa likes và nested replies)
            $reply->delete();

            DB::commit();

            // Gửi notification real-time (skip for delete - không cần thiết)
            // $this->notificationService->sendReplyNotification($rating, null, 'deleted');

            return response()->json([
                'success' => true,
                'message' => 'Trả lời đã được xóa thành công!',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error deleting rating reply: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xóa trả lời. Vui lòng thử lại.',
            ], 500);
        }
    }

    /**
     * Toggle like/unlike reply
     */
    public function toggleLike(ShowcaseRatingReply $reply): JsonResponse
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn cần đăng nhập để thích trả lời này.',
                ], 401);
            }

            if ($reply->user_id === $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không thể thích trả lời của chính mình.',
                ], 422);
            }

            $isLiked = $reply->toggleLike($user);

            // Gửi notification real-time nếu là like
            if ($isLiked) {
                $this->notificationService->sendLikeNotification(
                    'reply_liked',
                    $reply->id,
                    $user,
                    [
                        'author_id' => $reply->user_id,
                        'showcase_id' => $reply->rating->showcase_id,
                        'rating_id' => $reply->rating_id,
                    ]
                );
            }

            return response()->json([
                'success' => true,
                'message' => $isLiked ? 'Đã thích trả lời!' : 'Đã bỏ thích trả lời!',
                'data' => [
                    'is_liked' => $isLiked,
                    'like_count' => $reply->fresh()->like_count,
                ],
            ]);

        } catch (\Exception $e) {
            \Log::error('Error toggling reply like: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra. Vui lòng thử lại.',
            ], 500);
        }
    }

    /**
     * Lấy danh sách replies của rating
     */
    public function index(Request $request, ShowcaseRating $rating): JsonResponse
    {
        try {
            $perPage = min($request->get('per_page', 20), 50);

            $replies = $rating->replies()
                ->with(['user', 'likes', 'replies.user'])
                ->whereNull('parent_id') // Chỉ lấy top-level replies
                ->orderBy('created_at', 'asc')
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => [
                    'replies' => $replies->items(),
                    'pagination' => [
                        'current_page' => $replies->currentPage(),
                        'last_page' => $replies->lastPage(),
                        'per_page' => $replies->perPage(),
                        'total' => $replies->total(),
                        'has_more' => $replies->hasMorePages(),
                    ],
                ],
            ]);

        } catch (\Exception $e) {
            \Log::error('Error fetching rating replies: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tải trả lời.',
            ], 500);
        }
    }

    /**
     * Format reply response
     */
    private function formatReplyResponse(ShowcaseRatingReply $reply): array
    {
        return [
            'id' => $reply->id,
            'rating_id' => $reply->rating_id,
            'parent_id' => $reply->parent_id,
            'user' => [
                'id' => $reply->user->id,
                'name' => $reply->user->display_name,
                'username' => $reply->user->username,
                'avatar_url' => $reply->user->getAvatarUrl(),
            ],
            'content' => $reply->content,
            'has_media' => $reply->has_media,
            'images' => $reply->image_urls,
            'like_count' => $reply->like_count,
            'is_liked' => Auth::check() ? $reply->isLikedBy(Auth::user()) : false,
            'depth_level' => $reply->getDepthLevel(),
            'replies' => $reply->replies->map(function ($nestedReply) {
                return $this->formatReplyResponse($nestedReply);
            }),
            'created_at' => $reply->created_at->toISOString(),
            'created_at_human' => $reply->created_at->diffForHumans(),
        ];
    }


}
