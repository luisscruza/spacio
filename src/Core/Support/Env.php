<?php

namespace Spacio\Framework\Core\Support;

class Env
{
    public static function load(string $path): void
    {
        if (! is_file($path)) {
            return;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];

        foreach ($lines as $line) {
            $line = trim($line);

            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            [$key, $value] = array_pad(explode('=', $line, 2), 2, '');
            $key = trim($key);
            $value = trim($value);

            if ($key === '') {
                continue;
            }

            $value = self::stripQuotes($value);

            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;

            if (function_exists('putenv')) {
                putenv("{$key}={$value}");
            }
        }
    }

    protected static function stripQuotes(string $value): string
    {
        $first = $value[0] ?? '';
        $last = $value[strlen($value) - 1] ?? '';

        if (($first === '"' && $last === '"') || ($first === "'" && $last === "'")) {
            return substr($value, 1, -1);
        }

        return $value;
    }
}
