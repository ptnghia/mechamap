<?php

/**
 * Tạo màu ngẫu nhiên dựa trên chỉ số
 * 
 * @param int $index Chỉ số để tạo màu
 * @return string Mã màu HEX
 */
if (!function_exists('randomColor')) {
    function randomColor($index = null) {
        // Danh sách màu đẹp và dễ phân biệt
        $colors = [
            '#4e73df', // primary
            '#1cc88a', // success
            '#36b9cc', // info
            '#f6c23e', // warning
            '#e74a3b', // danger
            '#6f42c1', // purple
            '#fd7e14', // orange
            '#20c997', // teal
            '#6c757d', // gray
            '#f8f9fa', // light gray
            '#343a40', // dark
            '#007bff', // blue
            '#28a745', // green
            '#17a2b8', // cyan
            '#ffc107', // yellow
            '#dc3545', // red
            '#6610f2', // indigo
            '#6f42c1', // purple
            '#e83e8c', // pink
            '#fd7e14', // orange
        ];
        
        if ($index !== null) {
            // Nếu có chỉ số, lấy màu tương ứng hoặc lặp lại danh sách
            return $colors[$index % count($colors)];
        }
        
        // Nếu không có chỉ số, trả về màu ngẫu nhiên
        return $colors[array_rand($colors)];
    }
}

/**
 * Format số lượng lớn thành dạng dễ đọc (1K, 1M, ...)
 * 
 * @param int $number Số cần format
 * @param int $precision Số chữ số thập phân
 * @return string Số đã được format
 */
if (!function_exists('formatNumber')) {
    function formatNumber($number, $precision = 1) {
        if ($number < 1000) {
            return $number;
        } else if ($number < 1000000) {
            return round($number / 1000, $precision) . 'K';
        } else if ($number < 1000000000) {
            return round($number / 1000000, $precision) . 'M';
        } else {
            return round($number / 1000000000, $precision) . 'B';
        }
    }
}

/**
 * Tạo slug từ chuỗi
 * 
 * @param string $string Chuỗi cần tạo slug
 * @return string Slug
 */
if (!function_exists('makeSlug')) {
    function makeSlug($string) {
        $string = trim($string);
        $string = strtolower($string);
        $string = preg_replace('/[^a-z0-9\s-]/', '', $string);
        $string = preg_replace('/[\s-]+/', ' ', $string);
        $string = preg_replace('/\s/', '-', $string);
        return $string;
    }
}

/**
 * Lấy tên viết tắt từ tên đầy đủ
 * 
 * @param string $name Tên đầy đủ
 * @return string Tên viết tắt
 */
if (!function_exists('getInitials')) {
    function getInitials($name) {
        $words = explode(' ', $name);
        $initials = '';
        
        foreach ($words as $word) {
            $initials .= strtoupper(substr($word, 0, 1));
        }
        
        return $initials;
    }
}

/**
 * Tạo màu từ chuỗi (để tạo màu avatar)
 * 
 * @param string $string Chuỗi cần tạo màu
 * @return string Mã màu HEX
 */
if (!function_exists('stringToColor')) {
    function stringToColor($string) {
        // Tạo hash từ chuỗi
        $hash = 0;
        for ($i = 0; $i < strlen($string); $i++) {
            $hash = ord($string[$i]) + (($hash << 5) - $hash);
        }
        
        // Chuyển hash thành màu
        $color = '#';
        for ($i = 0; $i < 3; $i++) {
            $value = ($hash >> ($i * 8)) & 0xFF;
            $color .= str_pad(dechex($value), 2, '0', STR_PAD_LEFT);
        }
        
        return $color;
    }
}
