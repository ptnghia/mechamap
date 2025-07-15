<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

class AvatarController extends Controller
{
    /**
     * Tạo avatar mặc định với chữ cái đầu tiên
     *
     * @param string $initial Chữ cái đầu tiên
     * @param Request $request
     * @return Response
     */
    public function generate(string $initial, Request $request): Response
    {
        // Validate input
        $initial = strtoupper(substr($initial, 0, 1));
        if (!preg_match('/[A-Z0-9]/', $initial)) {
            $initial = 'U'; // Default to 'U' for User
        }

        // Lấy size từ request, mặc định 100px
        $size = (int) $request->get('size', 100);
        $size = max(32, min(400, $size)); // Giới hạn từ 32px đến 400px

        // Tạo cache key
        $cacheKey = "avatar_{$initial}_{$size}";

        // Kiểm tra cache
        if (Cache::has($cacheKey)) {
            $imageData = Cache::get($cacheKey);
            return response($imageData, 200, [
                'Content-Type' => 'image/png',
                'Cache-Control' => 'public, max-age=2592000', // 30 days
            ]);
        }

        // Tạo avatar mới
        $imageData = $this->createAvatar($initial, $size);

        // Lưu vào cache trong 30 ngày
        Cache::put($cacheKey, $imageData, now()->addDays(30));

        return response($imageData, 200, [
            'Content-Type' => 'image/png',
            'Cache-Control' => 'public, max-age=2592000', // 30 days
        ]);
    }

    /**
     * Tạo avatar image với GD library
     *
     * @param string $initial
     * @param int $size
     * @return string
     */
    private function createAvatar(string $initial, int $size): string
    {
        // Tạo màu nền ngẫu nhiên dựa trên chữ cái
        $backgroundColor = $this->generateColorFromString($initial);

        // Màu chữ (trắng hoặc đen tùy thuộc vào độ sáng của màu nền)
        $textColor = $this->getContrastColor($backgroundColor);

        // Tạo image
        $image = imagecreatetruecolor($size, $size);

        // Chuyển đổi màu hex sang RGB
        $bgRgb = $this->hexToRgb($backgroundColor);
        $textRgb = $this->hexToRgb($textColor);

        // Tạo màu
        $bgColor = imagecolorallocate($image, $bgRgb['r'], $bgRgb['g'], $bgRgb['b']);
        $txtColor = imagecolorallocate($image, $textRgb['r'], $textRgb['g'], $textRgb['b']);

        // Fill background
        imagefill($image, 0, 0, $bgColor);

        // Tính toán font size và vị trí - tỷ lệ thuận với kích thước avatar
        $fontSize = max(12, $size * 0.5); // Tối thiểu 12px, tối đa 50% kích thước avatar
        $fontPath = $this->getFontPath();

        if ($fontPath && file_exists($fontPath)) {
            // Sử dụng TTF font nếu có
            $textBox = imagettfbbox($fontSize, 0, $fontPath, $initial);
            $textWidth = $textBox[4] - $textBox[0];
            $textHeight = $textBox[1] - $textBox[7];

            $x = ($size - $textWidth) / 2;
            $y = ($size - $textHeight) / 2 + $textHeight;

            imagettftext($image, $fontSize, 0, $x, $y, $txtColor, $fontPath, $initial);
        } else {
            // Fallback sử dụng built-in font với scaling tỷ lệ thuận
            $font = 5; // Largest built-in font
            $baseTextWidth = imagefontwidth($font) * strlen($initial);
            $baseTextHeight = imagefontheight($font);

            // Scale factor tỷ lệ thuận với kích thước avatar
            $scale = max(1, $size / 32); // Tỷ lệ dựa trên kích thước cơ bản 32px

            // Tạo text với kích thước phù hợp
            $scaledTextWidth = $baseTextWidth * $scale;
            $scaledTextHeight = $baseTextHeight * $scale;

            // Vị trí căn giữa
            $x = ($size - $scaledTextWidth) / 2;
            $y = ($size - $scaledTextHeight) / 2;

            // Vẽ text với hiệu ứng bold bằng cách vẽ nhiều lần
            $thickness = max(1, intval($scale / 2));
            for ($i = 0; $i < $thickness; $i++) {
                for ($j = 0; $j < $thickness; $j++) {
                    imagestring($image, $font, $x + $i, $y + $j, $initial, $txtColor);
                }
            }
        }

        // Tạo output buffer
        ob_start();
        imagepng($image);
        $imageData = ob_get_contents();
        ob_end_clean();

        // Cleanup
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
     * Lấy màu tương phản (trắng hoặc đen)
     *
     * @param string $hexColor
     * @return string
     */
    private function getContrastColor(string $hexColor): string
    {
        $rgb = $this->hexToRgb($hexColor);

        // Tính độ sáng
        $brightness = ($rgb['r'] * 299 + $rgb['g'] * 587 + $rgb['b'] * 114) / 1000;

        return $brightness > 128 ? '#000000' : '#FFFFFF';
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
     * Lấy đường dẫn font TTF
     *
     * @return string|null
     */
    private function getFontPath(): ?string
    {
        $fontPaths = [
            storage_path('fonts/Roboto-Medium.ttf'),
            storage_path('fonts/arial.ttf'),
            public_path('fonts/Roboto-Medium.ttf'),
            public_path('fonts/arial.ttf'),
        ];

        foreach ($fontPaths as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        return null;
    }

    /**
     * Xóa cache avatar
     *
     * @param string $initial
     * @return \Illuminate\Http\JsonResponse
     */
    public function clearCache(string $initial = null)
    {
        if ($initial) {
            $initial = strtoupper(substr($initial, 0, 1));
            $sizes = [32, 40, 50, 80, 100, 150, 200, 400];

            foreach ($sizes as $size) {
                Cache::forget("avatar_{$initial}_{$size}");
            }

            return response()->json(['message' => "Cache cleared for initial: {$initial}"]);
        }

        // Xóa tất cả cache avatar
        $initials = range('A', 'Z');
        $initials = array_merge($initials, range('0', '9'));
        $sizes = [32, 40, 50, 80, 100, 150, 200, 400];

        foreach ($initials as $initial) {
            foreach ($sizes as $size) {
                Cache::forget("avatar_{$initial}_{$size}");
            }
        }

        return response()->json(['message' => 'All avatar cache cleared']);
    }
}
