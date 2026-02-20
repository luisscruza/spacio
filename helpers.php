<?php

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
