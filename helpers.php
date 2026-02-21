<?php

use Spacio\Framework\Core\Config\ConfigRepository;
use Spacio\Framework\Core\Support\App;
use Spacio\Framework\Http\Response;
use Spacio\Framework\View\ViewEngine;

if (! function_exists('view')) {
    function view(string $name, array $data = []): Response
    {
        $engine = new ViewEngine(BASE_PATH.'/views');
        $content = $engine->render($name, $data);

        return new Response($content);
    }
}

if (! function_exists('app')) {
    function app(?string $id = null): mixed
    {
        $container = App::container();

        return $id ? $container->get($id) : $container;
    }
}

if (! function_exists('config')) {
    function config(?string $key = null, mixed $default = null): mixed
    {
        $repository = app(ConfigRepository::class);

        if ($key === null) {
            return $repository->all();
        }

        return $repository->get($key, $default);
    }
}

if (! function_exists('env')) {
    function env(string $key, mixed $default = null): mixed
    {
        $value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);

        return $value !== false && $value !== null ? $value : $default;
    }
}

if (! function_exists('e')) {
    function e(mixed $value): string
    {
        if (is_object($value)) {
            if (method_exists($value, '__toString')) {
                $value = (string) $value;
            } elseif (method_exists($value, 'toArray')) {
                $value = json_encode($value->toArray(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            } else {
                $value = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }
        }

        if (is_array($value)) {
            $value = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

if (! function_exists('view_get')) {
    function view_get(string $key, array $scope): mixed
    {
        if (preg_match('/^([A-Za-z_][A-Za-z0-9_]*)\[(.+)\]$/', $key, $matches)) {
            $rootKey = $matches[1];
            $value = $scope[$rootKey] ?? null;
            $value = view_access($value, trim($matches[2], "'\""));

            return $value;
        }

        $rootKey = strtok($key, '.');
        $value = $scope[$rootKey] ?? null;

        $rest = substr($key, strlen($rootKey));
        if ($rest === false || $rest === '') {
            return $value;
        }

        $segments = array_filter(explode('.', ltrim($rest, '.')));

        foreach ($segments as $segment) {
            if (preg_match('/^(\w+)\[(.+)\]$/', $segment, $matches)) {
                $value = view_access($value, $matches[1]);
                $value = view_access($value, trim($matches[2], "'\""));

                continue;
            }

            $value = view_access($value, $segment);
        }

        return $value;
    }
}

if (! function_exists('view_access')) {
    function view_access(mixed $value, string $key): mixed
    {
        if (is_array($value) && array_key_exists($key, $value)) {
            return $value[$key];
        }

        if (is_object($value)) {
            if (method_exists($value, 'getAttribute')) {
                return $value->getAttribute($key);
            }

            if (property_exists($value, $key)) {
                return $value->{$key};
            }
        }

        return null;
    }
}
