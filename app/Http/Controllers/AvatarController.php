<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AvatarController extends Controller
{
    /**
     * Tạo avatar mặc định với chữ cái đầu tiên
     *
     * @param string $initial Chữ cái đầu tiên
     * @param Request $request
     * @return Response
     */
    public function generate(string $initial): BinaryFileResponse|Response
    {
        // Validate input
        $initial = strtoupper(substr($initial, 0, 2)); // Support up to 2 characters
        if (!preg_match('/[A-Z0-9]+/', $initial)) {
            $initial = 'U'; // Default to 'U' for User
        }

        // Tạo filename và path (sử dụng lowercase cho filename)
        $filename = strtolower($initial) . '.svg';
        $avatarDir = public_path('images/avatars');
        $filepath = $avatarDir . '/' . $filename;

        // Tạo thư mục nếu chưa tồn tại
        if (!is_dir($avatarDir)) {
            mkdir($avatarDir, 0755, true);
        }

        // Kiểm tra file đã tồn tại
        if (file_exists($filepath)) {
            return response()->file($filepath, [
                'Content-Type' => 'image/svg+xml',
                'Cache-Control' => 'public, max-age=2592000', // 30 days
            ]);
        }

        // Tải avatar từ DiceBear API
        try {
            $svgData = $this->downloadFromDiceBear($initial);

            // Lưu file vào disk
            file_put_contents($filepath, $svgData);

            return response()->file($filepath, [
                'Content-Type' => 'image/svg+xml',
                'Cache-Control' => 'public, max-age=2592000', // 30 days
            ]);
        } catch (\Exception $e) {
            // Fallback về method tạo avatar cũ nếu DiceBear API fail
            $pngFilename = strtolower($initial) . '.png';
            $pngFilepath = $avatarDir . '/' . $pngFilename;

            if (!file_exists($pngFilepath)) {
                $imageData = $this->createModernAvatar($initial, 150);
                file_put_contents($pngFilepath, $imageData);
            }

            return response()->file($pngFilepath, [
                'Content-Type' => 'image/png',
                'Cache-Control' => 'public, max-age=2592000', // 30 days
            ]);
        }
    }

    /**
     * Tải avatar từ DiceBear API
     *
     * @param string $initial
     * @return string
     * @throws \Exception
     */
    private function downloadFromDiceBear(string $initial): string
    {
        // Tạo URL cho DiceBear API
        $apiUrl = "https://api.dicebear.com/9.x/initials/svg";
        $params = [
            'seed' => $initial,
            'backgroundColor' => $this->getRandomBackgroundColor(),
            'fontSize' => 50,
            'fontWeight' => 600,
        ];

        // Gọi API với timeout
        $response = Http::timeout(10)->get($apiUrl, $params);

        if (!$response->successful()) {
            throw new \Exception('Failed to download avatar from DiceBear API');
        }

        $svgContent = $response->body();

        // Validate SVG content
        if (empty($svgContent) || strpos($svgContent, '<svg') === false) {
            throw new \Exception('Invalid SVG content received from DiceBear API');
        }

        return $svgContent;
    }

    /**
     * Lấy màu nền ngẫu nhiên cho DiceBear
     *
     * @return string
     */
    private function getRandomBackgroundColor(): string
    {
        $colors = [
            '7cb342', // Green
            '2196f3', // Blue
            'ff9800', // Orange
            '9c27b0', // Purple
            'f44336', // Red
            '607d8b', // Blue Grey
            '795548', // Brown
            '009688', // Teal
            '3f51b5', // Indigo
            'e91e63', // Pink
        ];

        return $colors[array_rand($colors)];
    }

    /**
     * Tạo avatar fallback đơn giản khi DiceBear API fail
     *
     * @param string $initial
     * @param int $size
     * @return string
     */
    private function createModernAvatar(string $initial, int $size): string
    {
        // Tạo simple avatar với màu nền và chữ
        $image = imagecreatetruecolor($size, $size);

        // Tạo màu nền từ chữ cái
        $baseColor = $this->generateColorFromString($initial);
        $bgRgb = $this->hexToRgb($baseColor);
        $bgColor = imagecolorallocate($image, $bgRgb['r'], $bgRgb['g'], $bgRgb['b']);

        // Fill background
        imagefill($image, 0, 0, $bgColor);

        // Màu chữ tương phản
        $textColor = $this->getOptimalTextColor($baseColor);
        $textRgb = $this->hexToRgb($textColor);
        $txtColor = imagecolorallocate($image, $textRgb['r'], $textRgb['g'], $textRgb['b']);

        // Font size đơn giản
        $fontSize = $size * 0.4; // 40% kích thước avatar

        // Vẽ text (fallback với built-in font)
        $font = 5;
        $textWidth = imagefontwidth($font) * strlen($initial);
        $textHeight = imagefontheight($font);

        $x = ($size - $textWidth) / 2;
        $y = ($size - $textHeight) / 2;

        imagestring($image, $font, $x, $y, $initial, $txtColor);

        // Convert to PNG
        ob_start();
        imagepng($image, null, 9);
        $imageData = ob_get_contents();
        ob_end_clean();

        imagedestroy($image);

        return $imageData;
    }



    /**
     * Tạo màu từ chuỗi
     *
     * @param string $string
     * @return string
     */
    private function generateColorFromString(string $string): string
    {
        // Danh sách màu đẹp cho avatar
        $colors = [
            '#FF6B6B', '#4ECDC4', '#45B7D1', '#96CEB4', '#FFEAA7',
            '#DDA0DD', '#98D8C8', '#F7DC6F', '#BB8FCE', '#85C1E9',
            '#F8C471', '#82E0AA', '#F1948A', '#85C1E9', '#D7BDE2',
            '#A3E4D7', '#F9E79F', '#FADBD8', '#D5DBDB', '#AED6F1'
        ];

        // Tạo hash từ chuỗi để chọn màu
        $hash = 0;
        for ($i = 0; $i < strlen($string); $i++) {
            $hash = ord($string[$i]) + (($hash << 5) - $hash);
        }

        $index = abs($hash) % count($colors);
        return $colors[$index];
    }



    /**
     * Chuyển đổi hex sang RGB
     *
     * @param string $hex
     * @return array
     */
    private function hexToRgb(string $hex): array
    {
        $hex = ltrim($hex, '#');

        if (strlen($hex) == 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }

        return [
            'r' => hexdec(substr($hex, 0, 2)),
            'g' => hexdec(substr($hex, 2, 2)),
            'b' => hexdec(substr($hex, 4, 2))
        ];
    }



    /**
     * Clear avatar files
     *
     * @param string|null $initial
     * @return \Illuminate\Http\JsonResponse
     */
    public function clearCache(string $initial = null)
    {
        $avatarDir = public_path('images/avatars');

        if (!is_dir($avatarDir)) {
            return response()->json(['message' => 'Avatar directory does not exist']);
        }

        if ($initial) {
            $initial = strtolower(substr($initial, 0, 2));
            $svgPattern = $avatarDir . '/' . $initial . '.svg';
            $pngPattern = $avatarDir . '/' . $initial . '.png';

            $files = [];
            if (file_exists($svgPattern)) $files[] = $svgPattern;
            if (file_exists($pngPattern)) $files[] = $pngPattern;

            foreach ($files as $file) {
                unlink($file);
            }

            return response()->json([
                'message' => "Avatar files cleared for '{$initial}'",
                'files_deleted' => count($files)
            ]);
        }

        // Clear all avatar files (SVG and PNG)
        $svgFiles = glob($avatarDir . '/*.svg');
        $pngFiles = glob($avatarDir . '/*.png');
        $allFiles = array_merge($svgFiles, $pngFiles);
        $deletedCount = 0;

        foreach ($allFiles as $file) {
            if (file_exists($file)) {
                unlink($file);
                $deletedCount++;
            }
        }

        return response()->json([
            'message' => 'All avatar files cleared',
            'files_deleted' => $deletedCount
        ]);
    }

    /**
     * Get avatar storage info
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStorageInfo()
    {
        $avatarDir = public_path('images/avatars');

        if (!is_dir($avatarDir)) {
            return response()->json([
                'directory_exists' => false,
                'total_files' => 0,
                'total_size' => 0
            ]);
        }

        $svgFiles = glob($avatarDir . '/*.svg');
        $pngFiles = glob($avatarDir . '/*.png');
        $allFiles = array_merge($svgFiles, $pngFiles);
        $totalSize = 0;

        foreach ($allFiles as $file) {
            $totalSize += filesize($file);
        }

        return response()->json([
            'directory_exists' => true,
            'directory_path' => $avatarDir,
            'total_files' => count($allFiles),
            'svg_files' => count($svgFiles),
            'png_files' => count($pngFiles),
            'total_size' => $totalSize,
            'total_size_human' => $this->formatBytes($totalSize),
            'files' => array_map('basename', $allFiles)
        ]);
    }

    /**
     * Format bytes to human readable format
     *
     * @param int $bytes
     * @return string
     */
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= (1 << (10 * $pow));

        return round($bytes, 2) . ' ' . $units[$pow];
    }

    /**
     * Điều chỉnh độ sáng của màu
     *
     * @param string $hex
     * @param int $percent
     * @return string
     */
    private function adjustBrightness(string $hex, int $percent): string
    {
        $hex = ltrim($hex, '#');

        if (strlen($hex) == 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }

        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        $r = max(0, min(255, $r + ($r * $percent / 100)));
        $g = max(0, min(255, $g + ($g * $percent / 100)));
        $b = max(0, min(255, $b + ($b * $percent / 100)));

        return sprintf('#%02x%02x%02x', $r, $g, $b);
    }

    /**
     * Lấy màu chữ tối ưu với độ tương phản cao
     *
     * @param string $backgroundColor
     * @return string
     */
    private function getOptimalTextColor(string $backgroundColor): string
    {
        $rgb = $this->hexToRgb($backgroundColor);

        // Tính luminance
        $luminance = (0.299 * $rgb['r'] + 0.587 * $rgb['g'] + 0.114 * $rgb['b']) / 255;

        // Trả về màu có độ tương phản cao nhất
        return $luminance > 0.5 ? '#000000' : '#FFFFFF';
    }
}
