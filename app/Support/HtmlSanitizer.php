<?php

namespace App\Support;

class HtmlSanitizer
{
    private const ALLOWED_TAGS = '<a><abbr><b><blockquote><br><code><div><em><h1><h2><h3><h4><h5><h6><hr><i><li><ol><p><pre><small><span><strong><table><tbody><td><th><thead><tr><u><ul>';

    public static function clean(?string $html): string
    {
        if ($html === null || $html === '') {
            return '';
        }

        $html = preg_replace('#<(script|style|iframe|object|embed|form|input|button|meta|link)\b[^>]*>.*?</\1>#is', '', $html) ?? '';
        $html = preg_replace('#<(script|style|iframe|object|embed|form|input|button|meta|link)\b[^>]*/?>#is', '', $html) ?? '';
        $html = strip_tags($html, self::ALLOWED_TAGS);

        $html = preg_replace('/\s+on[a-z]+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $html) ?? '';
        $html = preg_replace('/\s+(href|src)\s*=\s*([\'"])\s*(javascript:|data:|vbscript:).*?\2/i', '', $html) ?? '';
        $html = preg_replace('/\s+style\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $html) ?? '';

        return $html;
    }
}
