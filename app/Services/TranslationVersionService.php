<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use App\Models\Translation;

class TranslationVersionService
{
    public static function bump(string $locale, array $groups = []): void
    {
        Cache::forget(self::manifestCacheKey($locale));
        Cache::forget(self::versionCacheKey($locale));
        foreach (array_filter(array_unique($groups)) as $group) {
            Cache::forget(self::groupHashCacheKey($locale, $group));
        }
    }

    public static function getManifest(string $locale): array
    {
        $cacheKey = self::manifestCacheKey($locale);
        return Cache::remember($cacheKey, config('translation.version_cache_ttl'), function () use ($locale) {
            $groups = Translation::query()
                ->select('group_name')
                ->where('locale', $locale)
                ->active()
                ->distinct()
                ->pluck('group_name')
                ->filter()
                ->values();

            $groupHashes = [];
            foreach ($groups as $g) {
                $groupHashes[$g] = self::getGroupHash($locale, $g);
            }

            ksort($groupHashes);
            $version = self::computeVersion($groupHashes);

            return [
                'locale' => $locale,
                'version' => $version,
                'groups' => $groupHashes,
                'critical_groups' => config('translation.critical_groups', []),
            ];
        });
    }

    public static function getGroupHash(string $locale, string $group): string
    {
        $cacheKey = self::groupHashCacheKey($locale, $group);
        return Cache::remember($cacheKey, config('translation.version_cache_ttl'), function () use ($locale, $group) {
            $payload = Translation::active()
                ->forLocale($locale)
                ->forGroup($group)
                ->orderBy('key')
                ->pluck('content', 'key')
                ->toArray();
            return substr(md5(json_encode($payload)), 0, 12);
        });
    }

    public static function computeVersion(array $groupHashes): string
    {
        $base = implode('|', array_map(fn($g, $h) => $g.':'.$h, array_keys($groupHashes), $groupHashes));
        return substr(md5($base), 0, 16);
    }

    public static function version(string $locale): string
    {
        return Cache::remember(self::versionCacheKey($locale), config('translation.version_cache_ttl'), function () use ($locale) {
            $manifest = self::getManifest($locale);
            return $manifest['version'];
        });
    }

    private static function manifestCacheKey(string $locale): string { return "trans_manifest:{$locale}"; }
    private static function groupHashCacheKey(string $locale, string $group): string { return "trans_group_hash:{$locale}:{$group}"; }
    private static function versionCacheKey(string $locale): string { return "trans_version:{$locale}"; }
}
