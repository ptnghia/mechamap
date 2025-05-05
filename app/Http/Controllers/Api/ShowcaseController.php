<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Showcase;
use App\Models\Media;
use App\Models\UserActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ShowcaseController extends Controller
{
    /**
     * Get a list of showcases
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

            // Filter by status
            if ($request->has('status')) {
                $query->where('status', $request->status);
            } else {
                // Only show approved showcases by default
                $query->where('status', 'approved');
            }

            // Filter by featured
            if ($request->has('is_featured')) {
                $query->where('is_featured', $request->boolean('is_featured'));
            }

            // Search by title or description
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            }

            // Sort by
            $sortBy = $request->input('sort_by', 'created_at');
            $sortOrder = $request->input('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Paginate
            $perPage = $request->input('per_page', 15);
            $showcases = $query->with(['user', 'media'])->paginate($perPage);

            // Add additional information
            $showcases->getCollection()->transform(function ($showcase) {
                // Add user avatar URL
                if ($showcase->user) {
                    $showcase->user->avatar_url = $showcase->user->getAvatarUrl();
                }

                // Add full URL to each media
                if ($showcase->media) {
                    $showcase->media->transform(function ($media) {
                        $media->full_url = url(Storage::url($media->file_path));
                        return $media;
                    });
                }

                // Add cover image URL
                if ($showcase->cover_image) {
                    $showcase->cover_image_url = url(Storage::url($showcase->cover_image));
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

    /**
     * Get a showcase by slug
     *
     * @param string $slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($slug)
    {
        try {
            $showcase = Showcase::where('slug', $slug)->firstOrFail();

            // Check if showcase is approved or user is owner or admin
            if (
                $showcase->status !== 'approved' &&
                (!Auth::check() || (Auth::id() !== $showcase->user_id && !Auth::user()->hasRole(['admin', 'moderator'])))
            ) {
                return response()->json([
                    'success' => false,
                    'message' => 'Showcase này chưa được phê duyệt.'
                ], 403);
            }

            // Load relationships
            $showcase->load(['user', 'media']);

            // Add user avatar URL
            if ($showcase->user) {
                $showcase->user->avatar_url = $showcase->user->getAvatarUrl();
            }

            // Add full URL to each media
            if ($showcase->media) {
                $showcase->media->transform(function ($media) {
                    $media->full_url = url(Storage::url($media->file_path));
                    return $media;
                });
            }

            // Add cover image URL
            if ($showcase->cover_image) {
                $showcase->cover_image_url = url(Storage::url($showcase->cover_image));
            }

            // Increment view count
            $showcase->increment('view_count');

            return response()->json([
                'success' => true,
                'data' => $showcase,
                'message' => 'Lấy thông tin showcase thành công.'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy showcase.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi lấy thông tin showcase.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create a new showcase
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            // Validate request
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'location' => 'nullable|string|max:255',
                'usage' => 'nullable|string|max:255',
                'floors' => 'nullable|integer|min:1',
                'cover_image' => 'required|file|image|max:5120', // Max 5MB
                'media_ids' => 'nullable|array',
                'media_ids.*' => 'exists:media,id',
            ]);

            // Upload cover image
            $coverImage = $request->file('cover_image');
            $coverImageName = Str::uuid() . '.' . $coverImage->getClientOriginalExtension();
            $coverImagePath = $coverImage->storeAs('public/uploads/showcases/' . Auth::id(), $coverImageName);

            // Create slug from title
            $slug = Str::slug($request->title);

            // Check if slug already exists
            $count = Showcase::where('slug', $slug)->count();
            if ($count > 0) {
                $slug = $slug . '-' . ($count + 1);
            }

            // Create showcase
            $showcase = Showcase::create([
                'user_id' => Auth::id(),
                'title' => $request->title,
                'slug' => $slug,
                'description' => $request->description,
                'location' => $request->location,
                'usage' => $request->usage,
                'floors' => $request->floors,
                'cover_image' => $coverImagePath,
                'status' => 'pending', // Pending approval
            ]);

            // Attach media
            if ($request->has('media_ids')) {
                $mediaIds = $request->media_ids;

                // Check if media belongs to user
                $media = Media::whereIn('id', $mediaIds)->get();
                foreach ($media as $item) {
                    if ($item->user_id !== Auth::id()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Bạn không có quyền sử dụng media này.'
                        ], 403);
                    }
                }

                // Attach media to showcase
                $showcase->media()->attach($mediaIds);
            }

            // Create user activity
            UserActivity::create([
                'user_id' => Auth::id(),
                'type' => 'showcase_create',
                'subject_id' => $showcase->id,
                'subject_type' => Showcase::class,
            ]);

            // Load relationships
            $showcase->load(['user', 'media']);

            // Add user avatar URL
            if ($showcase->user) {
                $showcase->user->avatar_url = $showcase->user->getAvatarUrl();
            }

            // Add full URL to each media
            if ($showcase->media) {
                $showcase->media->transform(function ($media) {
                    $media->full_url = url(Storage::url($media->file_path));
                    return $media;
                });
            }

            // Add cover image URL
            if ($showcase->cover_image) {
                $showcase->cover_image_url = url(Storage::url($showcase->cover_image));
            }

            return response()->json([
                'success' => true,
                'data' => $showcase,
                'message' => 'Tạo showcase mới thành công. Showcase sẽ được hiển thị sau khi được phê duyệt.'
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
                'message' => 'Đã xảy ra lỗi khi tạo showcase mới.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update a showcase
     *
     * @param Request $request
     * @param string $slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $slug)
    {
        try {
            $showcase = Showcase::where('slug', $slug)->firstOrFail();

            // Check if user is authorized to update this showcase
            if (Auth::id() !== $showcase->user_id && !Auth::user()->hasRole(['admin', 'moderator'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền cập nhật showcase này.'
                ], 403);
            }

            // Validate request
            $request->validate([
                'title' => 'sometimes|required|string|max:255',
                'description' => 'sometimes|required|string',
                'location' => 'nullable|string|max:255',
                'usage' => 'nullable|string|max:255',
                'floors' => 'nullable|integer|min:1',
                'cover_image' => 'nullable|file|image|max:5120', // Max 5MB
                'media_ids' => 'nullable|array',
                'media_ids.*' => 'exists:media,id',
            ]);

            // Update cover image if provided
            if ($request->hasFile('cover_image')) {
                // Delete old cover image
                if ($showcase->cover_image) {
                    Storage::delete($showcase->cover_image);
                }

                // Upload new cover image
                $coverImage = $request->file('cover_image');
                $coverImageName = Str::uuid() . '.' . $coverImage->getClientOriginalExtension();
                $coverImagePath = $coverImage->storeAs('public/uploads/showcases/' . Auth::id(), $coverImageName);

                $showcase->cover_image = $coverImagePath;
            }

            // Update slug if title changed
            if ($request->has('title') && $request->title !== $showcase->title) {
                $slug = Str::slug($request->title);

                // Check if slug already exists
                $count = Showcase::where('slug', $slug)->where('id', '!=', $showcase->id)->count();
                if ($count > 0) {
                    $slug = $slug . '-' . ($count + 1);
                }

                $showcase->slug = $slug;
            }

            // Update showcase
            $showcase->fill($request->only([
                'title',
                'description',
                'location',
                'usage',
                'floors',
            ]));

            // Reset status to pending if not admin
            if (!Auth::user()->hasRole(['admin', 'moderator'])) {
                $showcase->status = 'pending';
            }

            $showcase->save();

            // Update media if provided
            if ($request->has('media_ids')) {
                $mediaIds = $request->media_ids;

                // Check if media belongs to user
                $media = Media::whereIn('id', $mediaIds)->get();
                foreach ($media as $item) {
                    if ($item->user_id !== Auth::id() && !Auth::user()->hasRole(['admin', 'moderator'])) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Bạn không có quyền sử dụng media này.'
                        ], 403);
                    }
                }

                // Sync media
                $showcase->media()->sync($mediaIds);
            }

            // Create user activity
            UserActivity::create([
                'user_id' => Auth::id(),
                'type' => 'showcase_update',
                'subject_id' => $showcase->id,
                'subject_type' => Showcase::class,
            ]);

            // Load relationships
            $showcase->load(['user', 'media']);

            // Add user avatar URL
            if ($showcase->user) {
                $showcase->user->avatar_url = $showcase->user->getAvatarUrl();
            }

            // Add full URL to each media
            if ($showcase->media) {
                $showcase->media->transform(function ($media) {
                    $media->full_url = url(Storage::url($media->file_path));
                    return $media;
                });
            }

            // Add cover image URL
            if ($showcase->cover_image) {
                $showcase->cover_image_url = url(Storage::url($showcase->cover_image));
            }

            return response()->json([
                'success' => true,
                'data' => $showcase,
                'message' => 'Cập nhật showcase thành công. Showcase sẽ được hiển thị sau khi được phê duyệt.'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy showcase.'
            ], 404);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi cập nhật showcase.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a showcase
     *
     * @param string $slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($slug)
    {
        try {
            $showcase = Showcase::where('slug', $slug)->firstOrFail();

            // Check if user is authorized to delete this showcase
            if (Auth::id() !== $showcase->user_id && !Auth::user()->hasRole(['admin', 'moderator'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền xóa showcase này.'
                ], 403);
            }

            // Delete cover image
            if ($showcase->cover_image) {
                Storage::delete($showcase->cover_image);
            }

            // Detach media
            $showcase->media()->detach();

            // Delete showcase
            $showcase->delete();

            return response()->json([
                'success' => true,
                'message' => 'Xóa showcase thành công.'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy showcase.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi xóa showcase.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get recent showcases
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRecent(Request $request)
    {
        try {
            // Get pagination parameters
            $page = $request->input('page', 1);
            $perPage = $request->input('per_page', 10);

            // Get recent showcases
            $showcases = Showcase::where('status', 'approved')
                ->with(['user', 'media'])
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);

            // Add additional information
            $showcases->getCollection()->transform(function ($showcase) {
                // Add user avatar URL
                if ($showcase->user) {
                    $showcase->user->avatar_url = $showcase->user->getAvatarUrl();
                }

                // Add full URL to each media
                if ($showcase->media) {
                    $showcase->media->transform(function ($media) {
                        $media->full_url = url(Storage::url($media->file_path));
                        return $media;
                    });
                }

                // Add cover image URL
                if ($showcase->cover_image) {
                    $showcase->cover_image_url = url(Storage::url($showcase->cover_image));
                }

                return $showcase;
            });

            return response()->json([
                'success' => true,
                'data' => $showcases,
                'message' => 'Lấy danh sách showcase mới nhất thành công.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi lấy danh sách showcase mới nhất.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
