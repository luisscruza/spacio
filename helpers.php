<?php

use Spacio\Framework\Core\Config\ConfigRepository;
use Spacio\Framework\Core\Support\App;
use Spacio\Framework\Http\Response;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

if (! function_exists('view')) {
    function view(string $name, array $data = []): Response
    {
        $template = BASE_PATH.'/views';

        $loader = new FilesystemLoader($template);
        $twig = new Environment($loader);

        $content = $twig->render("$name.spa", $data);

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
