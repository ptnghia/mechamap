<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserDevice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Jenssegers\Agent\Agent;

class DeviceDetectionService
{
    /**
     * Detect and track user device
     */
    public static function detectAndTrackDevice(User $user, Request $request): UserDevice
    {
        $agent = new Agent();
        $agent->setUserAgent($request->userAgent());

        // Generate device fingerprint
        $fingerprint = self::generateDeviceFingerprint($request, $agent);

        // Check if device already exists
        $device = UserDevice::where('user_id', $user->id)
            ->where('device_fingerprint', $fingerprint)
            ->first();

        if ($device) {
            // Update last seen
            $device->update([
                'last_seen_at' => now(),
                'ip_address' => $request->ip(),
            ]);

            return $device;
        }

        // Create new device record
        $locationData = self::getLocationFromIP($request->ip());

        $device = UserDevice::create([
            'user_id' => $user->id,
            'device_fingerprint' => $fingerprint,
            'device_type' => self::getDeviceType($agent),
            'browser' => $agent->browser(),
            'browser_version' => $agent->version($agent->browser()),
            'platform' => $agent->platform(),
            'platform_version' => $agent->version($agent->platform()),
            'user_agent' => $request->userAgent(),
            'ip_address' => $request->ip(),
            'country' => $locationData['country'] ?? null,
            'city' => $locationData['city'] ?? null,
            'is_trusted' => false,
            'first_seen_at' => now(),
            'last_seen_at' => now(),
        ]);

        // Send new device notification
        NotificationService::sendNewDeviceNotification($user, $device);

        return $device;
    }

    /**
     * Generate unique device fingerprint
     */
    private static function generateDeviceFingerprint(Request $request, Agent $agent): string
    {
        $components = [
            $agent->browser(),
            $agent->version($agent->browser()),
            $agent->platform(),
            $agent->version($agent->platform()),
            self::getDeviceType($agent),
            $request->header('Accept-Language'),
            $request->header('Accept-Encoding'),
        ];

        // Add screen resolution if available (from JavaScript)
        if ($request->has('screen_resolution')) {
            $components[] = $request->input('screen_resolution');
        }

        // Add timezone if available
        if ($request->has('timezone')) {
            $components[] = $request->input('timezone');
        }

        $fingerprint = implode('|', array_filter($components));
        return hash('sha256', $fingerprint);
    }

    /**
     * Get device type
     */
    private static function getDeviceType(Agent $agent): string
    {
        if ($agent->isMobile()) {
            return 'mobile';
        } elseif ($agent->isTablet()) {
            return 'tablet';
        } elseif ($agent->isDesktop()) {
            return 'desktop';
        }

        return 'unknown';
    }

    /**
     * Get location from IP address
     */
    private static function getLocationFromIP(string $ip): array
    {
        try {
            // For development, return default values
            if ($ip === '127.0.0.1' || $ip === '::1' || str_starts_with($ip, '192.168.')) {
                return [
                    'country' => 'Vietnam',
                    'city' => 'Ho Chi Minh City',
                ];
            }

            // In production, you would use a service like:
            // - MaxMind GeoIP2
            // - ipapi.co
            // - ip-api.com
            // For now, return basic info
            return [
                'country' => 'Unknown',
                'city' => 'Unknown',
            ];

        } catch (\Exception $e) {
            Log::error('Failed to get location from IP: ' . $e->getMessage());
            return [
                'country' => null,
                'city' => null,
            ];
        }
    }

    /**
     * Check if device is trusted
     */
    public static function isDeviceTrusted(User $user, Request $request): bool
    {
        $agent = new Agent();
        $agent->setUserAgent($request->userAgent());
        $fingerprint = self::generateDeviceFingerprint($request, $agent);

        return UserDevice::where('user_id', $user->id)
            ->where('device_fingerprint', $fingerprint)
            ->where('is_trusted', true)
            ->exists();
    }

    /**
     * Get user's devices
     */
    public static function getUserDevices(User $user): \Illuminate\Database\Eloquent\Collection
    {
        return UserDevice::where('user_id', $user->id)
            ->orderBy('last_seen_at', 'desc')
            ->get();
    }

    /**
     * Trust a device
     */
    public static function trustDevice(User $user, string $deviceFingerprint): bool
    {
        $device = UserDevice::where('user_id', $user->id)
            ->where('device_fingerprint', $deviceFingerprint)
            ->first();

        if ($device) {
            $device->markAsTrusted();
            return true;
        }

        return false;
    }

    /**
     * Remove a device
     */
    public static function removeDevice(User $user, string $deviceFingerprint): bool
    {
        return UserDevice::where('user_id', $user->id)
            ->where('device_fingerprint', $deviceFingerprint)
            ->delete() > 0;
    }

    /**
     * Clean old devices (older than 90 days and not trusted)
     */
    public static function cleanOldDevices(): int
    {
        return UserDevice::where('is_trusted', false)
            ->where('last_seen_at', '<', now()->subDays(90))
            ->delete();
    }
}
