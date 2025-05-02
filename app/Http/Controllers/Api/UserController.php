<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Thread;
use App\Models\Comment;
use App\Models\UserActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    /**
     * Get a list of users
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $query = User::query();

            // Filter by role
            if ($request->has('role')) {
                $query->where('role', $request->role);
            }

            // Filter by status
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            // Search by name, username or email
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('username', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            }

            // Sort by
            $sortBy = $request->input('sort_by', 'created_at');
            $sortOrder = $request->input('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Paginate
            $perPage = $request->input('per_page', 15);
            $users = $query->paginate($perPage);

            // Transform each user to include avatar URL
            $users->getCollection()->transform(function ($user) {
                $user->avatar_url = $user->getAvatarUrl();
                return $user;
            });

            return response()->json([
                'success' => true,
                'data' => $users,
                'message' => 'Lấy danh sách người dùng thành công.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi lấy danh sách người dùng.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a user by username
     *
     * @param string $username
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($username)
    {
        try {
            $user = User::where('username', $username)->firstOrFail();
            
            // Add avatar URL
            $user->avatar_url = $user->getAvatarUrl();
            
            // Add counts
            $user->threads_count = $user->threads()->count();
            $user->comments_count = $user->comments()->count();
            $user->followers_count = $user->followers()->count();
            $user->following_count = $user->following()->count();
            
            // Check if current user is following this user
            if (Auth::check()) {
                $user->is_following = Auth::user()->following()->where('following_id', $user->id)->exists();
            } else {
                $user->is_following = false;
            }

            return response()->json([
                'success' => true,
                'data' => $user,
                'message' => 'Lấy thông tin người dùng thành công.'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy người dùng.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi lấy thông tin người dùng.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update a user
     *
     * @param Request $request
     * @param string $username
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $username)
    {
        try {
            $user = User::where('username', $username)->firstOrFail();
            
            // Check if the authenticated user is the owner or an admin
            if (Auth::id() !== $user->id && !Auth::user()->isAdmin()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền cập nhật thông tin người dùng này.'
                ], 403);
            }
            
            // Validate request
            $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'username' => [
                    'sometimes',
                    'required',
                    'string',
                    'max:255',
                    'alpha_dash',
                    Rule::unique('users')->ignore($user->id),
                ],
                'email' => [
                    'sometimes',
                    'required',
                    'string',
                    'email',
                    'max:255',
                    Rule::unique('users')->ignore($user->id),
                ],
                'about_me' => 'nullable|string|max:1000',
                'website' => 'nullable|url|max:255',
                'location' => 'nullable|string|max:255',
                'signature' => 'nullable|string|max:500',
            ]);
            
            // Update user
            $user->fill($request->only([
                'name',
                'username',
                'email',
                'about_me',
                'website',
                'location',
                'signature',
            ]));
            
            $user->save();
            
            // Add avatar URL
            $user->avatar_url = $user->getAvatarUrl();

            return response()->json([
                'success' => true,
                'data' => $user,
                'message' => 'Cập nhật thông tin người dùng thành công.'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy người dùng.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi cập nhật thông tin người dùng.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a user
     *
     * @param string $username
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($username)
    {
        try {
            $user = User::where('username', $username)->firstOrFail();
            
            // Check if the authenticated user is the owner or an admin
            if (Auth::id() !== $user->id && !Auth::user()->isAdmin()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền xóa người dùng này.'
                ], 403);
            }
            
            // Delete avatar if exists and not a URL
            if ($user->avatar && strpos($user->avatar, 'http') !== 0) {
                Storage::disk('public')->delete($user->avatar);
            }
            
            // Delete user
            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'Xóa người dùng thành công.'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy người dùng.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi xóa người dùng.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get threads by a user
     *
     * @param Request $request
     * @param string $username
     * @return \Illuminate\Http\JsonResponse
     */
    public function getThreads(Request $request, $username)
    {
        try {
            $user = User::where('username', $username)->firstOrFail();
            
            $query = $user->threads();
            
            // Filter by forum
            if ($request->has('forum_id')) {
                $query->where('forum_id', $request->forum_id);
            }
            
            // Filter by status
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }
            
            // Sort by
            $sortBy = $request->input('sort_by', 'created_at');
            $sortOrder = $request->input('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);
            
            // Paginate
            $perPage = $request->input('per_page', 15);
            $threads = $query->with(['forum', 'user'])->paginate($perPage);
            
            return response()->json([
                'success' => true,
                'data' => $threads,
                'message' => 'Lấy danh sách bài viết của người dùng thành công.'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy người dùng.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi lấy danh sách bài viết của người dùng.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get comments by a user
     *
     * @param Request $request
     * @param string $username
     * @return \Illuminate\Http\JsonResponse
     */
    public function getComments(Request $request, $username)
    {
        try {
            $user = User::where('username', $username)->firstOrFail();
            
            $query = $user->comments();
            
            // Filter by thread
            if ($request->has('thread_id')) {
                $query->where('thread_id', $request->thread_id);
            }
            
            // Sort by
            $sortBy = $request->input('sort_by', 'created_at');
            $sortOrder = $request->input('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);
            
            // Paginate
            $perPage = $request->input('per_page', 15);
            $comments = $query->with(['thread', 'user'])->paginate($perPage);
            
            return response()->json([
                'success' => true,
                'data' => $comments,
                'message' => 'Lấy danh sách bình luận của người dùng thành công.'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy người dùng.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi lấy danh sách bình luận của người dùng.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get activities by a user
     *
     * @param Request $request
     * @param string $username
     * @return \Illuminate\Http\JsonResponse
     */
    public function getActivities(Request $request, $username)
    {
        try {
            $user = User::where('username', $username)->firstOrFail();
            
            $query = $user->activities();
            
            // Filter by type
            if ($request->has('type')) {
                $query->where('type', $request->type);
            }
            
            // Sort by
            $sortBy = $request->input('sort_by', 'created_at');
            $sortOrder = $request->input('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);
            
            // Paginate
            $perPage = $request->input('per_page', 15);
            $activities = $query->paginate($perPage);
            
            return response()->json([
                'success' => true,
                'data' => $activities,
                'message' => 'Lấy danh sách hoạt động của người dùng thành công.'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy người dùng.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi lấy danh sách hoạt động của người dùng.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Follow a user
     *
     * @param string $username
     * @return \Illuminate\Http\JsonResponse
     */
    public function follow($username)
    {
        try {
            $user = User::where('username', $username)->firstOrFail();
            
            // Check if user is trying to follow themselves
            if (Auth::id() === $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không thể theo dõi chính mình.'
                ], 400);
            }
            
            // Check if already following
            if (Auth::user()->following()->where('following_id', $user->id)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn đã theo dõi người dùng này rồi.'
                ], 400);
            }
            
            // Follow user
            Auth::user()->following()->attach($user->id);
            
            // Create activity
            UserActivity::create([
                'user_id' => Auth::id(),
                'type' => 'follow',
                'subject_id' => $user->id,
                'subject_type' => User::class,
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Theo dõi người dùng thành công.'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy người dùng.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi theo dõi người dùng.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Unfollow a user
     *
     * @param string $username
     * @return \Illuminate\Http\JsonResponse
     */
    public function unfollow($username)
    {
        try {
            $user = User::where('username', $username)->firstOrFail();
            
            // Check if not following
            if (!Auth::user()->following()->where('following_id', $user->id)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn chưa theo dõi người dùng này.'
                ], 400);
            }
            
            // Unfollow user
            Auth::user()->following()->detach($user->id);
            
            return response()->json([
                'success' => true,
                'message' => 'Hủy theo dõi người dùng thành công.'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy người dùng.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi hủy theo dõi người dùng.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get followers of a user
     *
     * @param Request $request
     * @param string $username
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFollowers(Request $request, $username)
    {
        try {
            $user = User::where('username', $username)->firstOrFail();
            
            // Paginate
            $perPage = $request->input('per_page', 15);
            $followers = $user->followers()->paginate($perPage);
            
            // Transform each user to include avatar URL
            $followers->getCollection()->transform(function ($follower) {
                $follower->avatar_url = $follower->getAvatarUrl();
                return $follower;
            });
            
            return response()->json([
                'success' => true,
                'data' => $followers,
                'message' => 'Lấy danh sách người theo dõi thành công.'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy người dùng.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi lấy danh sách người theo dõi.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get users that a user is following
     *
     * @param Request $request
     * @param string $username
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFollowing(Request $request, $username)
    {
        try {
            $user = User::where('username', $username)->firstOrFail();
            
            // Paginate
            $perPage = $request->input('per_page', 15);
            $following = $user->following()->paginate($perPage);
            
            // Transform each user to include avatar URL
            $following->getCollection()->transform(function ($followed) {
                $followed->avatar_url = $followed->getAvatarUrl();
                return $followed;
            });
            
            return response()->json([
                'success' => true,
                'data' => $following,
                'message' => 'Lấy danh sách người đang theo dõi thành công.'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy người dùng.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi lấy danh sách người đang theo dõi.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update user avatar
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateAvatar(Request $request)
    {
        try {
            $request->validate([
                'avatar' => 'required|image|max:2048', // Max 2MB
            ]);
            
            $user = Auth::user();
            
            // Update avatar
            $user->updateAvatar($request->file('avatar'));
            
            return response()->json([
                'success' => true,
                'data' => [
                    'avatar_url' => $user->getAvatarUrl(),
                ],
                'message' => 'Cập nhật avatar thành công.'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi cập nhật avatar.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
