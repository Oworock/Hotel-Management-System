<?php

namespace App\Helpers;

use App\Models\Language;
use App\Models\Translation;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class TranslationHelper
{
    public static function translate(string $key, ?string $default = null, ?string $locale = null): string
    {
        $locale = $locale ?: App::getLocale();
        $default = $default ?? Str::headline(str_replace(['.', '_'], ' ', $key));

        try {
            if (!Schema::hasTable('translations')) {
                return $default;
            }

            $value = Cache::remember("translation.{$locale}.{$key}", 3600, function () use ($locale, $key) {
                return Translation::where('locale', $locale)->where('key', $key)->value('value');
            });

            return filled($value) ? $value : $default;
        } catch (\Throwable $e) {
            return $default;
        }
    }

    public static function activeLocale(): string
    {
        try {
            if (session()->has('locale')) {
                return session('locale');
            }

            if (Schema::hasTable('languages')) {
                return Language::where('is_default', true)->value('code') ?: config('app.locale', 'en');
            }
        } catch (\Throwable $e) {
            //
        }

        return config('app.locale', 'en');
    }

    public static function renderHtml(string $html, ?string $locale = null): string
    {
        $locale = $locale ?: App::getLocale();

        try {
            if (!Schema::hasTable('translations')) {
                return $html;
            }

            $rows = Translation::where('locale', $locale)
                ->whereNotNull('source_text')
                ->whereNotNull('value')
                ->get(['source_text', 'value'])
                ->filter(fn ($row) => filled($row->source_text) && $row->source_text !== $row->value)
                ->sortByDesc(fn ($row) => mb_strlen($row->source_text));

            foreach ($rows as $row) {
                $html = str_replace($row->source_text, $row->value, $html);
            }
        } catch (\Throwable $e) {
            return $html;
        }

        return $html;
    }

    public static function ensureEnglishCatalog(): int
    {
        if (!Schema::hasTable('languages') || !Schema::hasTable('translations')) {
            return 0;
        }

        Language::firstOrCreate(
            ['code' => 'en'],
            ['name' => 'English', 'flag_emoji' => 'EN', 'is_active' => true, 'is_default' => true, 'order' => 0]
        );

        $phrases = self::extractProjectPhrases();
        $created = 0;

        foreach ($phrases as $phrase) {
            $key = 'ui.' . Str::slug(Str::limit($phrase, 70, ''), '_');
            if (!$key || $key === 'ui.') {
                continue;
            }

            $translation = Translation::firstOrCreate(
                ['locale' => 'en', 'key' => $key],
                ['group' => 'ui', 'source_text' => $phrase, 'value' => $phrase]
            );

            if ($translation->wasRecentlyCreated) {
                $created++;
            } elseif (!$translation->source_text) {
                $translation->update(['source_text' => $phrase]);
            }
        }

        return $created;
    }

    public static function copyEnglishToLocale(string $locale): int
    {
        if ($locale === 'en' || !Schema::hasTable('translations')) {
            return 0;
        }

        self::ensureEnglishCatalog();

        $created = 0;
        Translation::where('locale', 'en')->orderBy('id')->chunk(200, function ($englishRows) use ($locale, &$created) {
            foreach ($englishRows as $row) {
                $translation = Translation::firstOrCreate(
                    ['locale' => $locale, 'key' => $row->key],
                    ['group' => $row->group, 'source_text' => $row->source_text ?: $row->value, 'value' => $row->value]
                );

                if ($translation->wasRecentlyCreated) {
                    $created++;
                }
            }
        });

        return $created;
    }

    protected static function extractProjectPhrases(): array
    {
        $phrases = [];
        $paths = [
            resource_path('views'),
            base_path('plugins'),
        ];

        foreach ($paths as $path) {
            if (!is_dir($path)) {
                continue;
            }

            foreach (File::allFiles($path) as $file) {
                if (!str_ends_with($file->getFilename(), '.blade.php')) {
                    continue;
                }

                $content = file_get_contents($file->getPathname());
                preg_match_all('/>([^<>@{}$]{2,180})</u', $content, $matches);
                foreach ($matches[1] ?? [] as $match) {
                    $text = trim(html_entity_decode(preg_replace('/\s+/', ' ', $match)));
                    if (self::isTranslatablePhrase($text)) {
                        $phrases[$text] = $text;
                    }
                }

                preg_match_all('/(?:placeholder|title|aria-label|alt)=["\']([^"\']{2,180})["\']/u', $content, $attrMatches);
                foreach ($attrMatches[1] ?? [] as $match) {
                    $text = trim(html_entity_decode(preg_replace('/\s+/', ' ', $match)));
                    if (self::isTranslatablePhrase($text)) {
                        $phrases[$text] = $text;
                    }
                }
            }
        }

        ksort($phrases);

        return array_values($phrases);
    }

    protected static function isTranslatablePhrase(string $text): bool
    {
        if (mb_strlen($text) < 2 || mb_strlen($text) > 180) {
            return false;
        }

        if (preg_match('/^[\d\s\W_]+$/u', $text)) {
            return false;
        }

        foreach (['{{', '}}', '$', '=>', '::', 'function', 'var(', 'route(', 'asset('] as $needle) {
            if (str_contains($text, $needle)) {
                return false;
            }
        }

        return true;
    }
}
