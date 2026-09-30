<?php

namespace App\Services;

class UserAgentParser
{
    public static function parse(?string $userAgent): array
    {
        if (empty($userAgent)) {
            return [
                'device_type' => 'Desktop',
                'browser' => 'Unknown',
            ];
        }

        $deviceType = 'Desktop';
        if (preg_match('/(mobile|iphone|ipod|android|blackberry|opera mini|iemobile)/i', $userAgent)) {
            $deviceType = 'Mobile';
        } elseif (preg_match('/(tablet|ipad|playbook|silk)/i', $userAgent)) {
            $deviceType = 'Tablet';
        }

        $browser = 'Chrome';
        if (preg_match('/Brave/i', $userAgent)) {
            $browser = 'Brave';
        } elseif (preg_match('/Edg/i', $userAgent)) {
            $browser = 'Edge';
        } elseif (preg_match('/Firefox/i', $userAgent)) {
            $browser = 'Firefox';
        } elseif (preg_match('/Safari/i', $userAgent) && !preg_match('/Chrome/i', $userAgent)) {
            $browser = 'Safari';
        } elseif (preg_match('/Chrome/i', $userAgent)) {
            $browser = 'Chrome';
        } elseif (preg_match('/MSIE|Trident/i', $userAgent)) {
            $browser = 'Internet Explorer';
        }

        return [
            'device_type' => $deviceType,
            'browser' => $browser,
        ];
    }
}
