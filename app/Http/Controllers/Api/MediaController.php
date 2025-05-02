<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Models\Thread;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class MediaController extends Controller
{
    /**
     * Get a list of media for the authenticated user
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $query = Auth::user()->media();
            
            // Filter by type
            if ($request->has('type')) {
                $query->where('file_type', $request->type);
            }
            
            // Sort by
            $sortBy = $request->input('sort_by', 'created_at');
            $sortOrder = $request->input('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);
            
            // Paginate
            $perPage = $request->input('per_page', 15);
            $media = $query->paginate($perPage);
            
            // Add full URL to each media
            $media->getCollection()->transform(function ($item) {
                $item->full_url = url(Storage::url($item->file_path));
                return $item;
            });
            
            return response()->json([
                'success' => true,
                'data' => $media,
                'message' => 'Lấy danh sách media thành công.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi lấy danh sách media.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Upload a new media file
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            // Validate request
            $request->validate([
                'file' => 'required|file|max:10240', // Max 10MB
                'title' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'thread_id' => 'nullable|exists:threads,id',
            ]);
            
            // Check if thread exists and user has permission
            if ($request->has('thread_id')) {
                $thread = Thread::findOrFail($request->thread_id);
                if ($thread->user_id !== Auth::id() && !Auth::user()->hasRole(['admin', 'moderator'])) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Bạn không có quyền thêm media vào chủ đề này.'
                    ], 403);
                }
            }
            
            // Get file
            $file = $request->file('file');
            
            // Generate file name
            $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
            
            // Store file
            $filePath = $file->storeAs('public/uploads/media/' . Auth::id(), $fileName);
            
            // Create media record
            $media = Media::create([
                'user_id' => Auth::id(),
                'file_name' => $fileName,
                'file_path' => $filePath,
                'file_type' => $file->getClientMimeType(),
                'file_size' => $file->getSize(),
                'title' => $request->title ?? $file->getClientOriginalName(),
                'description' => $request->description,
                'mediable_id' => $request->thread_id,
                'mediable_type' => $request->has('thread_id') ? Thread::class : null,
            ]);
            
            // Add full URL
            $media->full_url = url(Storage::url($media->file_path));
            
            return response()->json([
                'success' => true,
                'data' => $media,
                'message' => 'Upload media thành công.'
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
                'message' => 'Đã xảy ra lỗi khi upload media.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get a media by ID
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $media = Media::findOrFail($id);
            
            // Check if user has permission
            if ($media->user_id !== Auth::id() && !Auth::user()->hasRole(['admin', 'moderator'])) {
                // Check if media is public (attached to a thread)
                if (!$media->mediable_id || !$media->mediable_type) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Bạn không có quyền xem media này.'
                    ], 403);
                }
            }
            
            // Add full URL
            $media->full_url = url(Storage::url($media->file_path));
            
            return response()->json([
                'success' => true,
                'data' => $media,
                'message' => 'Lấy thông tin media thành công.'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy media.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi lấy thông tin media.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Update a media
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            // Validate request
            $request->validate([
                'title' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'thread_id' => 'nullable|exists:threads,id',
            ]);
            
            $media = Media::findOrFail($id);
            
            // Check if user has permission
            if ($media->user_id !== Auth::id() && !Auth::user()->hasRole(['admin', 'moderator'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền cập nhật media này.'
                ], 403);
            }
            
            // Check if thread exists and user has permission
            if ($request->has('thread_id')) {
                $thread = Thread::findOrFail($request->thread_id);
                if ($thread->user_id !== Auth::id() && !Auth::user()->hasRole(['admin', 'moderator'])) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Bạn không có quyền thêm media vào chủ đề này.'
                    ], 403);
                }
            }
            
            // Update media
            $media->fill($request->only([
                'title',
                'description',
            ]));
            
            // Update thread association
            if ($request->has('thread_id')) {
                $media->mediable_id = $request->thread_id;
                $media->mediable_type = Thread::class;
            }
            
            $media->save();
            
            // Add full URL
            $media->full_url = url(Storage::url($media->file_path));
            
            return response()->json([
                'success' => true,
                'data' => $media,
                'message' => 'Cập nhật media thành công.'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy media.'
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
                'message' => 'Đã xảy ra lỗi khi cập nhật media.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Delete a media
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $media = Media::findOrFail($id);
            
            // Check if user has permission
            if ($media->user_id !== Auth::id() && !Auth::user()->hasRole(['admin', 'moderator'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền xóa media này.'
                ], 403);
            }
            
            // Delete file
            Storage::delete($media->file_path);
            
            // Delete media record
            $media->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Xóa media thành công.'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy media.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi xóa media.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get media for a thread
     *
     * @param Request $request
     * @param string $slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function getThreadMedia(Request $request, $slug)
    {
        try {
            $thread = Thread::where('slug', $slug)->firstOrFail();
            
            $query = Media::where('mediable_id', $thread->id)
                ->where('mediable_type', Thread::class);
            
            // Filter by type
            if ($request->has('type')) {
                $query->where('file_type', $request->type);
            }
            
            // Sort by
            $sortBy = $request->input('sort_by', 'created_at');
            $sortOrder = $request->input('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);
            
            // Paginate
            $perPage = $request->input('per_page', 15);
            $media = $query->paginate($perPage);
            
            // Add full URL to each media
            $media->getCollection()->transform(function ($item) {
                $item->full_url = url(Storage::url($item->file_path));
                return $item;
            });
            
            return response()->json([
                'success' => true,
                'data' => $media,
                'message' => 'Lấy danh sách media của chủ đề thành công.'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy chủ đề.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi lấy danh sách media của chủ đề.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
