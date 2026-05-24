<?php

namespace App\Helpers;

use App\Models\Theme;

class ThemeHelper
{
    public static function getActiveTheme()
    {
        return cache()->remember('active_theme', 3600, function () {
            return Theme::where('is_active', true)->first() ?? Theme::where('is_default', true)->first();
        });
    }

    public static function getThemeColor($colorKey)
    {
        $theme = self::getActiveTheme();
        return $theme?->colors[$colorKey] ?? '#3B82F6';
    }

    public static function getThemeVariable($key)
    {
        $theme = self::getActiveTheme();
        return $theme?->settings[$key] ?? null;
    }

    public static function getThemeView($view)
    {
        $theme = self::getActiveTheme();
        $themeView = "themes.{$theme?->slug}.{$view}";
        
        if (view()->exists($themeView)) {
            return $themeView;
        }
        
        return $view;
    }

    public static function getThemeCss()
    {
        $theme = self::getActiveTheme();
        if (!$theme) return '';

        $css = ":root {\n";
        foreach ($theme->colors ?? [] as $key => $value) {
            $css .= "  --color-{$key}: {$value};\n";
        }
        $css .= "}\n";

        return $css;
    }
}
