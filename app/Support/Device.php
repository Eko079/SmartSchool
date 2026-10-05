<?php

namespace App\Support;

use Illuminate\Http\Request;

class Device
{
    /**
     * True bila request berasal dari HP (mobile phone).
     * Tablet (iPad, Android tablet) dan laptop/desktop => false (tampil desktop).
     */
    public static function isPhone(?Request $request = null): bool
    {
        $ua = strtolower($request?->userAgent() ?? request()->userAgent() ?? '');

        if ($ua === '') {
            return false;
        }

        // Tablet / e-reader => desktop.
        if (preg_match('/tablet|ipad|playbook|silk|kindle|nexus\s*[79]|xoom|sch-i800|sm-t\d|gt-p\d/', $ua)) {
            return false;
        }

        // Android tanpa token "mobile" umumnya tablet.
        if (str_contains($ua, 'android') && ! str_contains($ua, 'mobile')) {
            return false;
        }

        return (bool) preg_match('/iphone|ipod|windows\s*phone|blackberry|iemobile|opera\s*mini|mobile/', $ua);
    }
}
