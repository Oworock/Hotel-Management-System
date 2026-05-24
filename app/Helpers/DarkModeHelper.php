<?php

namespace App\Helpers;

class DarkModeHelper
{
    public static function isDarkMode()
    {
        return auth()->check() ? auth()->user()->prefer_dark_mode : request()->cookie('dark_mode', false);
    }

    public static function toggleDarkMode($value = null)
    {
        if (auth()->check()) {
            auth()->user()->update(['prefer_dark_mode' => $value ?? !auth()->user()->prefer_dark_mode]);
        }
    }

    public static function getDarkModeClass()
    {
        return self::isDarkMode() ? 'dark' : '';
    }

    public static function getThemeColors($isDark = false)
    {
        $theme = \App\Helpers\ThemeHelper::getActiveTheme();
        
        if (!$isDark) {
            return $theme?->colors ?? [
                'primary' => '#3B82F6',
                'secondary' => '#1F2937',
                'accent' => '#10B981',
                'background' => '#F9FAFB',
                'text' => '#111827',
            ];
        }

        return [
            'primary' => '#60A5FA',
            'secondary' => '#E5E7EB',
            'accent' => '#34D399',
            'background' => '#111827',
            'text' => '#F9FAFB',
        ];
    }
}
