<?php

namespace App\View\Helpers;

class UserAgentSummary
{
    /**
     * Resume un user agent en dispositivo y navegador legibles.
     *
     * @return array{device: string, browser: string}
     */
    public static function parse(?string $userAgent): array
    {
        $ua = (string) $userAgent;

        $device = match (true) {
            str_contains($ua, 'iPhone') => 'iPhone',
            str_contains($ua, 'iPad') => 'iPad',
            str_contains($ua, 'Android') => 'Android',
            str_contains($ua, 'Windows') => 'Windows',
            str_contains($ua, 'Macintosh') => 'Mac',
            str_contains($ua, 'Linux') => 'Linux',
            default => 'Desconocido',
        };

        // El orden importa: Edge y Opera también dicen Chrome, y Chrome también dice Safari.
        $browser = match (true) {
            str_contains($ua, 'Edg/') => 'Edge',
            str_contains($ua, 'OPR/') => 'Opera',
            str_contains($ua, 'Firefox/') => 'Firefox',
            str_contains($ua, 'Chrome/'), str_contains($ua, 'CriOS/') => 'Chrome',
            str_contains($ua, 'Safari/') => 'Safari',
            default => 'Desconocido',
        };

        return ['device' => $device, 'browser' => $browser];
    }
}
