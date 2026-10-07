<?php

namespace App\Support;

class Icon
{
    private const PATHS = [
        'user'  => '<circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/>',
        'eye'   => '<path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/>',
        'off'   => '<path d="M3 3l18 18M10.6 6.1A9.8 9.8 0 0112 5c6 0 10 7 10 7a17 17 0 01-3.2 3.9M6.6 6.6A17 17 0 002 12s4 7 10 7c1.8 0 3.4-.5 4.8-1.3"/>',
        'dash'  => '<rect x="3" y="3" width="7" height="9" rx="1"/><rect x="14" y="3" width="7" height="5" rx="1"/><rect x="14" y="12" width="7" height="9" rx="1"/><rect x="3" y="16" width="7" height="5" rx="1"/>',
        'box'   => '<path d="M21 8l-9-5-9 5v8l9 5 9-5z"/><path d="M3 8l9 5 9-5M12 13v8"/>',
        'leaf'  => '<path d="M5 19C5 9 11 4 20 4c0 9-5 15-15 15zM5 19l8-8"/>',
        'fact'  => '<path d="M3 21V10l6 4v-4l6 4V6h6v15z"/>',
        'stack' => '<circle cx="12" cy="7" r="3"/><circle cx="7" cy="16" r="3"/><circle cx="17" cy="16" r="3"/>',
        'truck' => '<path d="M2 6h12v10H2zM14 10h4l3 3v3h-7"/><circle cx="7" cy="18" r="2"/><circle cx="17" cy="18" r="2"/>',
        'bag'   => '<path d="M5 8h14l-1 12H6zM9 8a3 3 0 016 0"/>',
        'shop'  => '<path d="M3 9l2-5h14l2 5M4 9v11h16V9M9 20v-6h6v6"/>',
        'chart' => '<path d="M3 20h18M6 16l4-5 4 3 5-7"/>',
        'users' => '<circle cx="9" cy="8" r="3.5"/><path d="M2 20c0-4 3-6 7-6s7 2 7 6M17 5a3.5 3.5 0 010 7M22 20c0-3-2-5-4-5.5"/>',
        'prof'  => '<circle cx="12" cy="12" r="9"/><circle cx="12" cy="10" r="3"/><path d="M6 19c1-3 3-4 6-4s5 1 6 4"/>',
        'out'   => '<path d="M9 21H4V3h5M16 17l5-5-5-5M21 12H9"/>',
        'wal'   => '<path d="M3 7h16a2 2 0 012 2v10H5a2 2 0 01-2-2zM3 7l12-3v3M17 14h2"/>',
        'bell'  => '<path d="M6 16V11a6 6 0 0112 0v5l2 2H4zM10 21a2 2 0 004 0"/>',
        'chev'  => '<path d="M6 9l6 6 6-6"/>',
        'warn'  => '<path d="M12 3l10 18H2zM12 10v5M12 18v.5"/>',
        'alert' => '<circle cx="12" cy="12" r="9"/><path d="M12 7v6M12 16.5v.5"/>',
    ];

    public static function svg(string $name, int $size = 20): string
    {
        $p = self::PATHS[$name] ?? '';

        return '<svg width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" '
            . 'stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $p . '</svg>';
    }
}
