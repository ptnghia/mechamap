<?php

namespace App\Services;

use App\Models\Showcase;
use Illuminate\Support\Collection;

/**
 * Service để xử lý hình ảnh showcase một cách thống nhất
 * Đồng bộ logic hiển thị hình ảnh giữa các routes khác nhau
 */
class ShowcaseImageService
{
    /**
     * Xử lý featured images cho collection of showcases
     *
     * @param Collection $showcases
     * @return Collection
     */
    public static function processFeaturedImages(Collection $showcases): Collection
    {
        return $showcases->map(function ($showcase) {
            $showcase->featured_image = self::getFeaturedImage($showcase);
            return $showcase;
        });
    }

    /**
     * Lấy featured image cho một showcase
     * Thứ tự ưu tiên:
     * 1. Media có [Featured] trong file_name
     * 2. Media image đầu tiên
     * 3. Thread featured_image (nếu là Thread type)
     * 4. Null (sẽ hiển thị placeholder)
     *
     * @param Showcase $showcase
     * @return object|null
     */
    public static function getFeaturedImage(Showcase $showcase): ?object
    {
        // 1. Ưu tiên: Media relations với [Featured] tag
        if ($showcase->media && $showcase->media->count() > 0) {
            // Tìm media có [Featured] trong tên file
            $featuredMedia = $showcase->media->filter(function ($media) {
                return strpos($media->file_name, '[Featured]') !== false;
            })->first();

            if ($featuredMedia) {
                return $featuredMedia;
            }

            // Fallback: Media image đầu tiên
            $firstImage = $showcase->media->filter(function ($media) {
                return strpos($media->file_type ?? '', 'image/') !== false;
            })->first();

            if ($firstImage) {
                return $firstImage;
            }
        }

        // 2. Fallback: Thread featured_image (legacy support)
        if (
            $showcase->showcaseable_type === 'App\\Models\\Thread' &&
            $showcase->showcaseable &&
            $showcase->showcaseable->featured_image
        ) {

            return (object)[
                'url' => $showcase->showcaseable->featured_image,
                'alt' => $showcase->showcaseable->title ?? 'Featured Image',
                'file_type' => 'image/jpeg', // Assume image type
                'is_legacy' => true // Flag để biết đây là legacy image
            ];
        }

        // 3. Fallback: Post content images (nếu là Post type)
        if (
            $showcase->showcaseable_type === 'App\\Models\\Post' &&
            $showcase->showcaseable &&
            $showcase->showcaseable->content
        ) {

            $imageUrl = self::extractFirstImageFromContent($showcase->showcaseable->content);
            if ($imageUrl) {
                return (object)[
                    'url' => $imageUrl,
                    'alt' => 'Post Image',
                    'file_type' => 'image/jpeg',
                    'is_extracted' => true // Flag để biết đây là extracted image
                ];
            }
        }

        // 4. Không có image nào
        return null;
    }

    /**
     * Trích xuất URL hình ảnh đầu tiên từ content HTML
     *
     * @param string $content
     * @return string|null
     */
    private static function extractFirstImageFromContent(string $content): ?string
    {
        // Pattern để tìm img tags trong HTML
        $pattern = '/<img[^>]+src="([^"]+)"[^>]*>/i';

        if (preg_match($pattern, $content, $matches)) {
            return $matches[1] ?? null;
        }

        // Pattern để tìm markdown images
        $markdownPattern = '/!\[([^\]]*)\]\(([^)]+)\)/';

        if (preg_match($markdownPattern, $content, $matches)) {
            return $matches[2] ?? null;
        }

        return null;
    }

    /**
     * Lấy URL của featured image với fallback
     *
     * @param Showcase $showcase
     * @return string
     */
    public static function getFeaturedImageUrl(Showcase $showcase): string
    {
        $featuredImage = self::getFeaturedImage($showcase);

        if ($featuredImage) {
            return $featuredImage->url ?? asset('images/placeholder.svg');
        }

        return asset('images/placeholder.svg');
    }

    /**
     * Kiểm tra showcase có featured image không
     *
     * @param Showcase $showcase
     * @return bool
     */
    public static function hasFeaturedImage(Showcase $showcase): bool
    {
        return self::getFeaturedImage($showcase) !== null;
    }

    /**
     * Lấy metadata của featured image
     *
     * @param Showcase $showcase
     * @return array
     */
    public static function getFeaturedImageMeta(Showcase $showcase): array
    {
        $featuredImage = self::getFeaturedImage($showcase);

        if (!$featuredImage) {
            return [
                'hasImage' => false,
                'url' => asset('images/placeholder.svg'),
                'alt' => 'No image available',
                'type' => 'placeholder'
            ];
        }

        return [
            'hasImage' => true,
            'url' => $featuredImage->url ?? asset('images/placeholder.svg'),
            'alt' => $featuredImage->alt ?? $showcase->title ?? 'Showcase Image',
            'type' => $featuredImage->is_legacy ?? false ? 'legacy' : ($featuredImage->is_extracted ?? false ? 'extracted' : 'media'),
            'fileType' => $featuredImage->file_type ?? 'unknown'
        ];
    }
}
