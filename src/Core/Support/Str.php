<?php

namespace Spacio\Framework\Core\Support;

class Str
{
    public static function uppercase(string $value): string
    {
        return strtoupper($value);
    }

    public static function titleize(string $value): string
    {
        $value = str_replace(['_', '-'], ' ', $value);
        $value = trim(preg_replace('/\s+/', ' ', $value) ?? $value);

        return ucwords(strtolower($value));
    }

    public static function studly(string $value): string
    {
        $value = str_replace(['-', '_'], ' ', $value);
        $value = ucwords(strtolower($value));

        return str_replace(' ', '', $value);
    }
}
