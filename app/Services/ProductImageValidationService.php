<?php

namespace App\Services;

use App\Models\MarketplaceProduct;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\UploadedFile;

class ProductImageValidationService
{
    private const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    private const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5MB

    // Thư mục images ở root project để lấy hình ảnh ngẫu nhiên
    private const ROOT_IMAGES_DIRECTORY = 'images';

    // Các thư mục con ưu tiên cho product images
    private const PREFERRED_SUBDIRS = [
        'showcase',
        'threads',
        'category-forum'
    ];

    /**
     * Validate và fix tất cả product images
     */
    public function validateAndFixAllProducts(bool $dryRun = false): array
    {
        $stats = [
            'total_processed' => 0,
            'missing_featured_fixed' => 0,
            'missing_images_fixed' => 0,
            'broken_images_fixed' => 0,
            'errors' => 0
        ];

        MarketplaceProduct::chunk(50, function ($products) use (&$stats, $dryRun) {
            foreach ($products as $product) {
                try {
                    $changes = $this->validateAndFixProductImages($product, $dryRun);

                    if (!empty($changes)) {
                        if (!$dryRun) {
                            $product->update($changes);
                        }

                        if (isset($changes['featured_image'])) {
                            $stats['missing_featured_fixed']++;
                        }
                        if (isset($changes['images'])) {
                            $stats['missing_images_fixed']++;
                        }
                    }

                    $stats['total_processed']++;

                } catch (\Exception $e) {
                    Log::error("Error validating product #{$product->id} images: " . $e->getMessage());
                    $stats['errors']++;
                }
            }
        });

        return $stats;
    }

    /**
     * Validate và fix images cho một product
     */
    public function validateAndFixProductImages(MarketplaceProduct $product, bool $dryRun = false): array
    {
        $changes = [];

        // Fix missing featured image
        if (empty($product->featured_image) || !$this->imageExists($product->featured_image)) {
            $newFeaturedImage = $this->getRandomImageFromRoot();
            if ($newFeaturedImage) {
                $changes['featured_image'] = $newFeaturedImage;
                Log::info("Product #{$product->id}: Assigned new featured image: {$newFeaturedImage}");
            }
        }

        // Fix missing hoặc broken images array
        $validImages = [];
        if (!empty($product->images) && is_array($product->images)) {
            foreach ($product->images as $imagePath) {
                if ($this->imageExists($imagePath)) {
                    $validImages[] = $imagePath;
                }
            }
        }

        // Nếu không có valid images, assign random images từ root
        if (empty($validImages)) {
            $randomImages = $this->getMultipleRandomImagesFromRoot(3);
            if (!empty($randomImages)) {
                $changes['images'] = $randomImages;
                Log::info("Product #{$product->id}: Assigned " . count($randomImages) . " new images from root directory");
            }
        } elseif (count($validImages) !== count($product->images ?? [])) {
            // Một số images invalid, update với chỉ valid ones
            $changes['images'] = $validImages;
            Log::info("Product #{$product->id}: Removed invalid images, kept " . count($validImages) . " valid images");
        }

        return $changes;
    }

    /**
     * Kiểm tra xem image file có tồn tại không
     */
    private function imageExists(string $imagePath): bool
    {
        if (empty($imagePath)) {
            return false;
        }

        // Xử lý các format path khác nhau
        $fullPath = $this->getFullImagePath($imagePath);
        return File::exists($fullPath);
    }

    /**
     * Get full path cho image
     */
    private function getFullImagePath(string $imagePath): string
    {
        // Remove leading slash nếu có
        $imagePath = ltrim($imagePath, '/');

        // Nếu path đã bắt đầu với public/, sử dụng như vậy
        if (str_starts_with($imagePath, 'public/')) {
            return base_path($imagePath);
        }

        // Nếu path bắt đầu với images/, prepend public/
        if (str_starts_with($imagePath, 'images/')) {
            return public_path($imagePath);
        }

        // Ngược lại assume nó relative to public/images/
        return public_path('images/' . $imagePath);
    }

    /**
     * Lấy một random image từ thư mục images ở root
     */
    private function getRandomImageFromRoot(): ?string
    {
        $availableImages = $this->getAllImagesFromRoot();

        if (empty($availableImages)) {
            return null;
        }

        $randomImage = $availableImages[array_rand($availableImages)];
        return $this->normalizeImagePath($randomImage);
    }

    /**
     * Lấy nhiều random images từ thư mục root
     */
    private function getMultipleRandomImagesFromRoot(int $count = 3): array
    {
        $availableImages = $this->getAllImagesFromRoot();

        if (empty($availableImages)) {
            return [];
        }

        // Shuffle và lấy số lượng yêu cầu
        shuffle($availableImages);
        $selectedImages = array_slice($availableImages, 0, min($count, count($availableImages)));

        return array_map([$this, 'normalizeImagePath'], $selectedImages);
    }

    /**
     * Lấy tất cả images từ thư mục images ở root
     */
    private function getAllImagesFromRoot(): array
    {
        $images = [];
        $rootImagesPath = base_path(self::ROOT_IMAGES_DIRECTORY);

        if (!File::isDirectory($rootImagesPath)) {
            Log::warning("Root images directory not found: {$rootImagesPath}");
            return [];
        }

        // Ưu tiên các thư mục con cụ thể
        foreach (self::PREFERRED_SUBDIRS as $subdir) {
            $subdirPath = $rootImagesPath . DIRECTORY_SEPARATOR . $subdir;
            if (File::isDirectory($subdirPath)) {
                $files = File::allFiles($subdirPath);

                foreach ($files as $file) {
                    $extension = strtolower($file->getExtension());
                    if (in_array($extension, self::ALLOWED_EXTENSIONS)) {
                        $images[] = $file->getPathname();
                    }
                }
            }
        }

        // Nếu không tìm thấy đủ images, scan toàn bộ thư mục root
        if (count($images) < 10) {
            $allFiles = File::allFiles($rootImagesPath);

            foreach ($allFiles as $file) {
                $extension = strtolower($file->getExtension());
                if (in_array($extension, self::ALLOWED_EXTENSIONS)) {
                    $fullPath = $file->getPathname();
                    if (!in_array($fullPath, $images)) {
                        $images[] = $fullPath;
                    }
                }
            }
        }

        Log::info("Found " . count($images) . " images in root directory for replacement");
        return $images;
    }

    /**
     * Normalize image path cho database storage
     */
    private function normalizeImagePath(string $fullPath): string
    {
        // Convert full path thành relative path từ public directory
        $publicPath = public_path();
        $rootPath = base_path();

        // Nếu image ở trong public directory
        if (str_starts_with($fullPath, $publicPath)) {
            $relativePath = str_replace($publicPath . DIRECTORY_SEPARATOR, '', $fullPath);
            return str_replace('\\', '/', $relativePath);
        }

        // Nếu image ở root/images, copy vào public/images
        if (str_starts_with($fullPath, $rootPath . DIRECTORY_SEPARATOR . 'images')) {
            return $this->copyImageToPublic($fullPath);
        }

        return $fullPath;
    }

    /**
     * Copy image từ root/images sang public/images
     */
    private function copyImageToPublic(string $sourcePath): string
    {
        $rootImagesPath = base_path('images');
        $relativePath = str_replace($rootImagesPath . DIRECTORY_SEPARATOR, '', $sourcePath);
        $relativePath = str_replace('\\', '/', $relativePath);

        $destinationPath = public_path('images/' . $relativePath);
        $destinationDir = dirname($destinationPath);

        // Tạo directory nếu chưa tồn tại
        if (!File::isDirectory($destinationDir)) {
            File::makeDirectory($destinationDir, 0755, true);
        }

        // Copy file nếu chưa tồn tại
        if (!File::exists($destinationPath)) {
            try {
                File::copy($sourcePath, $destinationPath);
                Log::info("Copied image from root to public: images/{$relativePath}");
            } catch (\Exception $e) {
                Log::error("Failed to copy image: " . $e->getMessage());
                return $sourcePath; // Return original path if copy fails
            }
        }

        return 'images/' . $relativePath;
    }

    /**
     * Validate uploaded image file
     */
    public function validateUploadedImage(UploadedFile $file): array
    {
        $errors = [];

        // Check file extension
        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, self::ALLOWED_EXTENSIONS)) {
            $errors[] = "Invalid file extension. Allowed: " . implode(', ', self::ALLOWED_EXTENSIONS);
        }

        // Check file size
        if ($file->getSize() > self::MAX_FILE_SIZE) {
            $errors[] = "File size too large. Maximum: " . (self::MAX_FILE_SIZE / 1024 / 1024) . "MB";
        }

        // Check if it's actually an image
        try {
            $imageInfo = getimagesize($file->getPathname());
            if (!$imageInfo) {
                $errors[] = "File is not a valid image";
            } else {
                // Check dimensions (optional)
                [$width, $height] = $imageInfo;
                if ($width < 100 || $height < 100) {
                    $errors[] = "Image dimensions too small. Minimum: 100x100px";
                }
                if ($width > 4000 || $height > 4000) {
                    $errors[] = "Image dimensions too large. Maximum: 4000x4000px";
                }
            }
        } catch (\Exception $e) {
            $errors[] = "Could not read image file: " . $e->getMessage();
        }

        return $errors;
    }

    /**
     * Process và optimize uploaded image
     */
    public function processUploadedImage(UploadedFile $file, string $category = 'products'): string
    {
        // Validate first
        $errors = $this->validateUploadedImage($file);
        if (!empty($errors)) {
            throw new \InvalidArgumentException("Image validation failed: " . implode(', ', $errors));
        }

        // Generate unique filename
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $relativePath = "images/{$category}/{$filename}";
        $fullPath = public_path($relativePath);

        // Ensure directory exists
        $directory = dirname($fullPath);
        if (!File::isDirectory($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        // Move uploaded file
        $file->move($directory, $filename);

        Log::info("Image uploaded and saved: {$relativePath}");
        return $relativePath;
    }

    /**
     * Get image validation statistics
     */
    public function getValidationStats(): array
    {
        $totalProducts = MarketplaceProduct::count();
        $productsWithoutFeatured = MarketplaceProduct::whereNull('featured_image')
            ->orWhere('featured_image', '')->count();

        $productsWithBrokenFeatured = 0;
        $productsWithoutImages = 0;
        $productsWithBrokenImages = 0;

        MarketplaceProduct::chunk(100, function ($products) use (&$productsWithBrokenFeatured, &$productsWithoutImages, &$productsWithBrokenImages) {
            foreach ($products as $product) {
                // Check featured image
                if (!empty($product->featured_image) && !$this->imageExists($product->featured_image)) {
                    $productsWithBrokenFeatured++;
                }

                // Check images array
                if (empty($product->images) || !is_array($product->images) || count($product->images) === 0) {
                    $productsWithoutImages++;
                } else {
                    $hasValidImage = false;
                    foreach ($product->images as $imagePath) {
                        if ($this->imageExists($imagePath)) {
                            $hasValidImage = true;
                            break;
                        }
                    }
                    if (!$hasValidImage) {
                        $productsWithBrokenImages++;
                    }
                }
            }
        });

        return [
            'total_products' => $totalProducts,
            'products_without_featured' => $productsWithoutFeatured,
            'products_with_broken_featured' => $productsWithBrokenFeatured,
            'products_without_images' => $productsWithoutImages,
            'products_with_broken_images' => $productsWithBrokenImages,
            'available_replacement_images' => count($this->getAllImagesFromRoot()),
        ];
    }

}
