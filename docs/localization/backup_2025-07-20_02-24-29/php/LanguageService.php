<?php

namespace App\Services;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LanguageService
{
    /**
     * Danh sách ngôn ngữ được hỗ trợ
     */
    const SUPPORTED_LOCALES = [
        'vi' => [
            'code' => 'vi',
            'name' => 'Tiếng Việt',
            'flag' => 'vn',
            'direction' => 'ltr'
        ],
        'en' => [
            'code' => 'en',
            'name' => 'English',
            'flag' => 'us',
            'direction' => 'ltr'
        ]
    ];

    /**
     * Lấy ngôn ngữ hiện tại
     *
     * @return string
     */
    public static function getCurrentLocale(): string
    {
        return App::getLocale();
    }

    /**
     * Lấy tất cả ngôn ngữ được hỗ trợ
     *
     * @return array
     */
    public static function getSupportedLocales(): array
    {
        return self::SUPPORTED_LOCALES;
    }

    /**
     * Kiểm tra ngôn ngữ có được hỗ trợ không
     *
     * @param string $locale
     * @return bool
     */
    public static function isSupported(string $locale): bool
    {
        return array_key_exists($locale, self::SUPPORTED_LOCALES);
    }

    /**
     * Thiết lập ngôn ngữ
     *
     * @param string $locale
     * @return bool
     */
    public static function setLocale(string $locale): bool
    {
        if (!self::isSupported($locale)) {
            return false;
        }

        // Set locale cho request hiện tại (theo Laravel 11 docs)
        App::setLocale($locale);

        // Lưu vào session để persist qua các request
        Session::put('locale', $locale);

        return true;
    }

    /**
     * Lấy thông tin ngôn ngữ hiện tại
     *
     * @return array
     */
    public static function getCurrentLanguageInfo(): array
    {
        $currentLocale = self::getCurrentLocale();
        return self::SUPPORTED_LOCALES[$currentLocale] ?? self::SUPPORTED_LOCALES['vi'];
    }

    /**
     * Lấy tên ngôn ngữ hiện tại
     *
     * @return string
     */
    public static function getCurrentLanguageName(): string
    {
        $info = self::getCurrentLanguageInfo();
        return $info['name'];
    }

    /**
     * Lấy mã cờ của ngôn ngữ hiện tại
     *
     * @return string
     */
    public static function getCurrentLanguageFlag(): string
    {
        $info = self::getCurrentLanguageInfo();
        return $info['flag'];
    }

    /**
     * Lấy hướng văn bản của ngôn ngữ hiện tại
     *
     * @return string
     */
    public static function getCurrentDirection(): string
    {
        $info = self::getCurrentLanguageInfo();
        return $info['direction'];
    }

    /**
     * Tạo URL với ngôn ngữ khác
     *
     * @param string $locale
     * @param string|null $url
     * @return string
     */
    public static function getLocalizedUrl(string $locale, ?string $url = null): string
    {
        if (!self::isSupported($locale)) {
            return $url ?? request()->url();
        }

        $baseUrl = $url ?? request()->url();
        $queryParams = request()->query();
        $queryParams['lang'] = $locale;

        return $baseUrl . '?' . http_build_query($queryParams);
    }

    /**
     * Lấy danh sách ngôn ngữ khác (không bao gồm ngôn ngữ hiện tại)
     *
     * @return array
     */
    public static function getOtherLanguages(): array
    {
        $currentLocale = self::getCurrentLocale();
        return array_filter(self::SUPPORTED_LOCALES, function($key) use ($currentLocale) {
            return $key !== $currentLocale;
        }, ARRAY_FILTER_USE_KEY);
    }

    /**
     * Format số theo ngôn ngữ hiện tại
     *
     * @param float $number
     * @param int $decimals
     * @return string
     */
    public static function formatNumber(float $number, int $decimals = 0): string
    {
        $locale = self::getCurrentLocale();

        if ($locale === 'vi') {
            return number_format($number, $decimals, ',', '.');
        }

        return number_format($number, $decimals, '.', ',');
    }

    /**
     * Format tiền tệ theo ngôn ngữ hiện tại
     *
     * @param float $amount
     * @param string $currency
     * @return string
     */
    public static function formatCurrency(float $amount, string $currency = 'VND'): string
    {
        $locale = self::getCurrentLocale();
        $formattedAmount = self::formatNumber($amount, 0);

        if ($locale === 'vi') {
            return $formattedAmount . ' ' . $currency;
        }

        return $currency . ' ' . $formattedAmount;
    }

    /**
     * Format ngày tháng theo ngôn ngữ hiện tại
     *
     * @param \DateTime|string $date
     * @param string $format
     * @return string
     */
    public static function formatDate($date, string $format = null): string
    {
        if (is_string($date)) {
            $date = new \DateTime($date);
        }

        $locale = self::getCurrentLocale();

        if (!$format) {
            $format = $locale === 'vi' ? 'd/m/Y' : 'Y-m-d';
        }

        return $date->format($format);
    }
}
