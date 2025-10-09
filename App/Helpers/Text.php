<?php

namespace App\Helpers;

class Text
{
    public static function excerpt(string $text, int $max = 60): string
    {
        if (mb_strlen($text) <= $max) {
            return $text;
        }
        $excerpt = mb_substr($text, 0, $max);
        $lastSpace = mb_strrpos($excerpt, ' ');
        if ($lastSpace !== false) {
            $excerpt = mb_substr($excerpt, 0, $lastSpace);
        }
        return $excerpt . '...';
    }

    public static function e(?string $content): string
    {
        return htmlentities($content ?? '', ENT_QUOTES, 'UTF-8');
    }
}
