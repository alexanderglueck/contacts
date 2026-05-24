<?php

namespace App\Support;

/**
 * Tiny browser/platform extractor used by the active-sessions page.
 *
 * Not a full UA parser — we never need to make security decisions from
 * the result, just render a human label like "Chrome on macOS" next to
 * each session row. Falls back to the raw UA string when no pattern
 * matches, so an unrecognised device still gets *something* in the UI.
 */
class UserAgentInfo
{
    public static function fromUserAgent(?string $userAgent): array
    {
        $userAgent ??= '';

        return [
            'browser' => self::browser($userAgent),
            'platform' => self::platform($userAgent),
            'is_desktop' => self::isDesktop($userAgent),
        ];
    }

    private static function browser(string $ua): string
    {
        return match (true) {
            str_contains($ua, 'Edg/') || str_contains($ua, 'Edge/') => 'Edge',
            str_contains($ua, 'OPR/') || str_contains($ua, 'Opera') => 'Opera',
            str_contains($ua, 'Firefox') => 'Firefox',
            str_contains($ua, 'Chrome') => 'Chrome',
            str_contains($ua, 'Safari') => 'Safari',
            default => 'Browser',
        };
    }

    private static function platform(string $ua): string
    {
        return match (true) {
            str_contains($ua, 'iPhone') => 'iPhone',
            str_contains($ua, 'iPad') => 'iPad',
            str_contains($ua, 'Android') => 'Android',
            str_contains($ua, 'Mac OS X') || str_contains($ua, 'Macintosh') => 'macOS',
            str_contains($ua, 'Windows') => 'Windows',
            str_contains($ua, 'CrOS') => 'ChromeOS',
            str_contains($ua, 'Linux') => 'Linux',
            default => 'Unknown device',
        };
    }

    private static function isDesktop(string $ua): bool
    {
        $mobileSignals = ['iPhone', 'iPad', 'Android', 'Mobile'];

        foreach ($mobileSignals as $signal) {
            if (str_contains($ua, $signal)) {
                return false;
            }
        }

        return true;
    }
}
