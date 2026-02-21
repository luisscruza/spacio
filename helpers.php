<?php

use Spacio\Framework\Components\ComponentManager;
use Spacio\Framework\Core\Config\ConfigRepository;
use Spacio\Framework\Core\Support\App;
use Spacio\Framework\Http\Response;
use Spacio\Framework\Http\Session;
use Spacio\Framework\View\ViewEngine;

if (! function_exists('view')) {
    function view(string $name, array $data = []): Response
    {
        $engine = app(ViewEngine::class);
        $content = $engine->render($name, $data);

        return new Response($content);
    }
}

if (! function_exists('component')) {
    function component(string $name, array $props = [], array $data = []): string
    {
        $manager = app(ComponentManager::class);

        return $manager->render($name, $props, $data);
    }
}

if (! function_exists('view_render')) {
    function view_render(string $name, array $data = []): string
    {
        $engine = app(ViewEngine::class);

        return $engine->renderPartial($name, $data);
    }
}

if (! function_exists('view_extend')) {
    function view_extend(string $view): void
    {
        ViewEngine::extend($view);
    }
}

if (! function_exists('view_section_start')) {
    function view_section_start(string $name): void
    {
        ViewEngine::sectionStart($name);
    }
}

if (! function_exists('view_section_end')) {
    function view_section_end(): void
    {
        ViewEngine::sectionEnd();
    }
}

if (! function_exists('view_yield')) {
    function view_yield(string $name, string $default = ''): string
    {
        return ViewEngine::yield($name, $default);
    }
}

if (! function_exists('errors')) {
    function errors(?string $key = null): mixed
    {
        static $bag;

        if ($bag === null) {
            $bag = Session::pullFlash('errors', []);
        }

        if ($key === null) {
            return $bag;
        }

        return $bag[$key] ?? null;
    }
}

if (! function_exists('old')) {
    function old(string $key, mixed $default = null): mixed
    {
        static $values;

        if ($values === null) {
            $values = Session::pullFlash('old', []);
        }

        return $values[$key] ?? $default;
    }
}

if (! function_exists('view_errors')) {
    function view_errors(?string $key = null): string
    {
        $bag = errors();

        if ($key !== null) {
            $messages = $bag[$key] ?? [];

            return $messages ? e((string) $messages[0]) : '';
        }

        if (! $bag) {
            return '';
        }

        $items = array_map(function (array $messages): string {
            return '<li>'.e((string) $messages[0]).'</li>';
        }, $bag);

        return '<ul>'.implode('', $items).'</ul>';
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
