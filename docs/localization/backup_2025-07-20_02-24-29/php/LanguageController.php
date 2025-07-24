<?php

namespace App\Http\Controllers;

use App\Services\LanguageService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

class LanguageController extends Controller
{
    /**
     * Chuyển đổi ngôn ngữ
     *
     * @param Request $request
     * @param string $locale
     * @return RedirectResponse|JsonResponse
     */
    public function switch(Request $request, string $locale)
    {
        // Kiểm tra ngôn ngữ có được hỗ trợ không
        if (!LanguageService::isSupported($locale)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('messages.language.not_supported')
                ], 400);
            }

            return redirect()->back()->with('error', __('messages.language.not_supported'));
        }

        // Thiết lập ngôn ngữ
        $success = LanguageService::setLocale($locale);

        if ($request->expectsJson()) {
            // Đảm bảo locale được áp dụng ngay cho response này
            app()->setLocale($locale);

            return response()->json([
                'success' => $success,
                'message' => $success
                    ? __('messages.language.switched_successfully')
                    : __('messages.language.switch_failed'),
                'locale' => $locale,
                'language_info' => LanguageService::getCurrentLanguageInfo()
            ]);
        }

        if ($success) {
            return redirect()->back()->with('success', __('messages.language.switched_successfully'));
        }

        return redirect()->back()->with('error', __('messages.language.switch_failed'));
    }

    /**
     * Lấy thông tin ngôn ngữ hiện tại
     *
     * @return JsonResponse
     */
    public function current(): JsonResponse
    {
        return response()->json([
            'current_locale' => LanguageService::getCurrentLocale(),
            'current_language' => LanguageService::getCurrentLanguageInfo(),
            'supported_languages' => LanguageService::getSupportedLocales(),
            'other_languages' => LanguageService::getOtherLanguages()
        ]);
    }

    /**
     * Lấy danh sách tất cả ngôn ngữ được hỗ trợ
     *
     * @return JsonResponse
     */
    public function supported(): JsonResponse
    {
        return response()->json([
            'supported_languages' => LanguageService::getSupportedLocales()
        ]);
    }

    /**
     * Tự động phát hiện ngôn ngữ từ browser
     *
     * @param Request $request
     * @return RedirectResponse|JsonResponse
     */
    public function autoDetect(Request $request)
    {
        $acceptLanguage = $request->header('Accept-Language');
        $detectedLocale = $this->detectLocaleFromHeader($acceptLanguage);

        if ($detectedLocale && LanguageService::isSupported($detectedLocale)) {
            LanguageService::setLocale($detectedLocale);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'detected_locale' => $detectedLocale,
                    'message' => __('messages.language.auto_detected')
                ]);
            }

            return redirect()->back()->with('success', __('messages.language.auto_detected'));
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => __('messages.language.auto_detect_failed')
            ]);
        }

        return redirect()->back()->with('info', __('messages.language.auto_detect_failed'));
    }

    /**
     * Phát hiện ngôn ngữ từ Accept-Language header
     *
     * @param string|null $acceptLanguage
     * @return string|null
     */
    private function detectLocaleFromHeader(?string $acceptLanguage): ?string
    {
        if (!$acceptLanguage) {
            return null;
        }

        $supportedLocales = array_keys(LanguageService::getSupportedLocales());

        // Parse Accept-Language header
        $languages = [];
        preg_match_all('/([a-z]{2}(?:-[A-Z]{2})?)\s*(?:;\s*q\s*=\s*([0-9.]+))?/',
                      $acceptLanguage, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $lang = substr($match[1], 0, 2);
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
