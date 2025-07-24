<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class Localization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Danh sách ngôn ngữ được hỗ trợ
        $supportedLocales = ['vi', 'en'];

        // Lấy ngôn ngữ từ session hoặc từ request
        $locale = $this->getLocale($request, $supportedLocales);

        // Thiết lập ngôn ngữ cho ứng dụng (theo Laravel 11 docs)
        App::setLocale($locale);

        return $next($request);
    }

    /**
     * Xác định ngôn ngữ sử dụng
     *
     * @param Request $request
     * @param array $supportedLocales
     * @return string
     */
    private function getLocale(Request $request, array $supportedLocales): string
    {
        // 1. Kiểm tra parameter 'lang' trong URL
        if ($request->has('lang') && in_array($request->get('lang'), $supportedLocales)) {
            return $request->get('lang');
        }

        // 2. Kiểm tra session
        if (Session::has('locale') && in_array(Session::get('locale'), $supportedLocales)) {
            return Session::get('locale');
        }

        // 3. Kiểm tra header Accept-Language từ browser
        $browserLocale = $this->getBrowserLocale($request, $supportedLocales);
        if ($browserLocale) {
            return $browserLocale;
        }

        // 4. Sử dụng ngôn ngữ mặc định từ config
        return config('app.locale', 'vi');
    }

    /**
     * Lấy ngôn ngữ từ browser header
     *
     * @param Request $request
     * @param array $supportedLocales
     * @return string|null
     */
    private function getBrowserLocale(Request $request, array $supportedLocales): ?string
    {
        $acceptLanguage = $request->header('Accept-Language');

        if (!$acceptLanguage) {
            return null;
        }

        // Parse Accept-Language header
        $languages = [];
        preg_match_all('/([a-z]{2}(?:-[A-Z]{2})?)\s*(?:;\s*q\s*=\s*([0-9.]+))?/',
                      $acceptLanguage, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $lang = substr($match[1], 0, 2); // Lấy 2 ký tự đầu (vi, en)
            $quality = isset($match[2]) ? (float) $match[2] : 1.0;
            $languages[$lang] = $quality;
        }

        // Sắp xếp theo độ ưu tiên
        arsort($languages);

        // Tìm ngôn ngữ được hỗ trợ đầu tiên
        foreach ($languages as $lang => $quality) {
            if (in_array($lang, $supportedLocales)) {
                return $lang;
            }
        }

        return null;
    }
}
