<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageUploadController extends Controller
{
    /**
     * Upload images for rich text editor
     */
    public function upload(Request $request): JsonResponse
    {
        $request->validate([
            'images' => 'required|array|max:5',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:5120' // 5MB max
        ]);

        try {
            $uploadedImages = [];

            foreach ($request->file('images') as $image) {
                $uploadedImages[] = $this->processAndStoreImage($image);
            }

            return response()->json([
                'success' => true,
                'images' => $uploadedImages,
                'message' => 'Hình ảnh đã được tải lên thành công.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tải lên hình ảnh: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process and store image
     */
    private function processAndStoreImage($image): string
    {
        // Generate unique filename
        $filename = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
        $path = 'images/comments/' . $filename;

        // Create directory if it doesn't exist
        $fullPath = public_path('images/comments');
        if (!file_exists($fullPath)) {
            mkdir($fullPath, 0755, true);
        }

        // Move file to public directory
        $image->move(public_path('images/comments'), $filename);

        return asset($path);
    }

    /**
     * Delete uploaded image
     */
    public function delete(Request $request): JsonResponse
    {
        $request->validate([
            'image_url' => 'required|string'
        ]);

        try {
            $imageUrl = $request->input('image_url');
            $imagePath = str_replace(asset(''), '', $imageUrl);

            if (file_exists(public_path($imagePath))) {
                unlink(public_path($imagePath));
            }

            return response()->json([
                'success' => true,
                'message' => 'Hình ảnh đã được xóa.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xóa hình ảnh.'
            ], 500);
        }
    }
}
