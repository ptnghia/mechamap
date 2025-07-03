<?php

namespace App\Services;

use App\Models\Thread;
use App\Models\Showcase;
use App\Models\Media;
use Illuminate\Support\Str;

/**
 * Unified Image Display Service
 * Đảm bảo tất cả threads và showcases đều có hình ảnh hiển thị
 */
class UnifiedImageDisplayService
{
    /**
     * Placeholder images cho các loại content khác nhau
     */
    private const PLACEHOLDERS = [
        'thread' => '/images/placeholders/300x200.png',
        'showcase' => '/images/placeholders/800x600.png',
        'default' => '/images/placeholders/300x300.png'
    ];

    /**
     * Lấy hình ảnh hiển thị cho thread với fallback logic
     */
    public function getThreadDisplayImage(Thread $thread): string
    {
        // 1. Ưu tiên: Featured image từ media relationships
        if ($thread->relationLoaded('media') && $thread->media->isNotEmpty()) {
            $featuredMedia = $thread->media
                ->filter(function ($media) {
                    return Str::startsWith($media->mime_type, 'image/');
                })
                ->first();

            if ($featuredMedia) {
                return $this->buildImageUrl($featuredMedia->file_path);
            }
        }

        // 2. Fallback: Database featured_image column
        if ($thread->getOriginal('featured_image')) {
            return $this->buildImageUrl($thread->getOriginal('featured_image'));
        }

        // 3. Fallback: Hình ảnh đầu tiên trong content
        if ($thread->content) {
            $imageFromContent = $this->extractImageFromContent($thread->content);
            if ($imageFromContent) {
                return $imageFromContent;
            }
        }

        // 4. Fallback: Category image nếu có
        if ($thread->category && $thread->category->icon) {
            return asset($thread->category->icon);
        }

        // 5. Final fallback: Placeholder
        return asset(self::PLACEHOLDERS['thread']);
    }

    /**
     * Lấy hình ảnh hiển thị cho showcase với fallback logic
     */
    public function getShowcaseDisplayImage(Showcase $showcase): string
    {
        // 1. Ưu tiên: Featured media từ media relationships
        if ($showcase->relationLoaded('media') && $showcase->media->isNotEmpty()) {
            $featuredMedia = $showcase->media
                ->where('file_name', 'like', '%[Featured]%')
                ->first();

            if ($featuredMedia) {
                return $this->buildImageUrl($featuredMedia->file_path);
            }

            // Nếu không có featured, lấy media đầu tiên
            $firstMedia = $showcase->media
                ->filter(function ($media) {
                    return Str::startsWith($media->mime_type, 'image/');
                })
                ->first();

            if ($firstMedia) {
                return $this->buildImageUrl($firstMedia->file_path);
            }
        }

        // 2. Fallback: Legacy cover_image field
        if ($showcase->cover_image) {
            return $this->buildImageUrl($showcase->cover_image);
        }

        // 3. Fallback: Hình ảnh từ image_gallery
        if ($showcase->image_gallery) {
            $gallery = is_string($showcase->image_gallery)
                ? json_decode($showcase->image_gallery, true)
                : $showcase->image_gallery;

            if (is_array($gallery) && !empty($gallery)) {
                return $this->buildImageUrl($gallery[0]);
            }
        }

        // 4. Fallback: Hình ảnh từ description content
        if ($showcase->description) {
            $imageFromContent = $this->extractImageFromContent($showcase->description);
            if ($imageFromContent) {
                return $imageFromContent;
            }
        }

        // 5. Final fallback: Placeholder
        return asset(self::PLACEHOLDERS['showcase']);
    }

    /**
     * Extract image từ HTML content
     */
    public function extractImageFromContent(string $content): ?string
    {
        // Tìm img tags trong content
        preg_match('/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i', $content, $matches);

        if (!empty($matches[1])) {
            $imageSrc = $matches[1];

            // Nếu là relative path, convert thành full URL
            if (!filter_var($imageSrc, FILTER_VALIDATE_URL)) {
                return $this->buildImageUrl($imageSrc);
            }

            return $imageSrc;
        }

        // Tìm markdown images
        preg_match('/!\[.*?\]\(([^)]+)\)/', $content, $markdownMatches);

        if (!empty($markdownMatches[1])) {
            $imageSrc = $markdownMatches[1];

            if (!filter_var($imageSrc, FILTER_VALIDATE_URL)) {
                return $this->buildImageUrl($imageSrc);
            }

            return $imageSrc;
        }

        return null;
    }

    /**
     * Build proper image URL từ file path
     */
    public function buildImageUrl(string $filePath): string
    {
        // Nếu đã là URL đầy đủ, return trực tiếp
        if (filter_var($filePath, FILTER_VALIDATE_URL)) {
            return $filePath;
        }

        // Clean path
        $cleanPath = ltrim($filePath, '/');

        // Tất cả images đều trong public/images/, sử dụng asset()
        if (Str::startsWith($cleanPath, 'images/')) {
            return asset($cleanPath);
        }

        // Default: assume it's in public/images
        return asset('images/' . $cleanPath);
    }

    /**
     * Kiểm tra xem image URL có tồn tại không
     */
    public function imageExists(string $imageUrl): bool
    {
        // Nếu là external URL, skip check để tránh slow down
        if (Str::startsWith($imageUrl, ['http://', 'https://']) &&
            !Str::contains($imageUrl, config('app.url'))) {
            return true;
        }

        // Convert URL thành file path để check
        $relativePath = str_replace(config('app.url'), '', $imageUrl);
        $relativePath = ltrim($relativePath, '/');

        if (Str::startsWith($relativePath, 'storage/')) {
            $filePath = storage_path('app/public/' . Str::after($relativePath, 'storage/'));
        } else {
            $filePath = public_path($relativePath);
        }

        return file_exists($filePath);
    }

    /**
     * Lấy responsive image sizes cho thread
     */
    public function getThreadImageSizes(): array
    {
        return [
            'thumbnail' => '150x100',
            'medium' => '300x200',
            'large' => '600x400'
        ];
    }

    /**
     * Lấy responsive image sizes cho showcase
     */
    public function getShowcaseImageSizes(): array
    {
        return [
            'thumbnail' => '200x150',
            'medium' => '400x300',
            'large' => '800x600',
            'hero' => '1200x800'
        ];
    }

    /**
     * Generate fallback image với text overlay
     */
    public function generateFallbackImage(string $title, string $type = 'default'): string
    {
        // Tạo URL cho placeholder với title
        $encodedTitle = urlencode(Str::limit($title, 50));
        $backgroundColor = $type === 'thread' ? '4F46E5' : '059669'; // Indigo cho thread, Green cho showcase
        $textColor = 'FFFFFF';

        return "https://via.placeholder.com/400x300/{$backgroundColor}/{$textColor}?text=" . $encodedTitle;
    }

    /**
     * Bulk process images cho collection of threads/showcases
     */
    public function bulkProcessImages($items, string $type = 'thread'): void
    {
        foreach ($items as $item) {
            if ($type === 'thread') {
                $item->display_image = $this->getThreadDisplayImage($item);
            } elseif ($type === 'showcase') {
                $item->display_image = $this->getShowcaseDisplayImage($item);
            }
        }
    }
}
