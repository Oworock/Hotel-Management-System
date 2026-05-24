<?php

namespace App\Helpers;

use App\Models\Theme;

class ThemeHelper
{
    public static function getActiveTheme()
    {
        $activeThemes = Theme::where('is_active', true)->latest('updated_at')->get();

        if ($activeThemes->count() > 1) {
            $themeToKeep = $activeThemes->first();
            Theme::whereKeyNot($themeToKeep->getKey())->update(['is_active' => false]);
            return $themeToKeep;
        }

        return $activeThemes->first() ?? Theme::where('is_default', true)->first();
    }

    public static function getThemeColor($colorKey)
    {
        $theme = self::getActiveTheme();
        return $theme?->colors[$colorKey] ?? '#3B82F6';
    }

    public static function getThemeVariable($key)
    {
        $theme = self::getActiveTheme();
        return $theme ? data_get($theme->settings ?? [], $key) : null;
    }

    public static function getThemeContent(string $key, ?string $default = null): ?string
    {
        return self::getThemeVariable("content.{$key}") ?? $default;
    }

    public static function getThemeView($view)
    {
        $theme = self::getActiveTheme();
        $themeView = $theme ? "themes.{$theme->slug}.{$view}" : null;
        
        if ($themeView && view()->exists($themeView)) {
            return $themeView;
        }
        
        $fallbackView = "themes.modern-minimal.{$view}";

        return view()->exists($fallbackView) ? $fallbackView : $view;
    }

    public static function getThemeCss()
    {
        $theme = self::getActiveTheme();
        if (!$theme) return '';

        $css = ":root {\n";
        foreach ($theme->colors ?? [] as $key => $value) {
            $css .= "  --{$key}: {$value};\n";
        }
        $css .= "}\n";

        return $css;
    }
}
