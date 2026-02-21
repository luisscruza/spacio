<?php

namespace Spacio\Framework\Http;

use FastRoute\Dispatcher;

class RouteHandler
{
    public function __construct(
        protected ControllerResolver $resolver,
        protected ExceptionRenderer $renderer,
    ) {}

    public function handle(array $routeInfo, Request $request): Response
    {
        $status = $routeInfo[0] ?? null;

        if ($status === Dispatcher::NOT_FOUND) {
            $content = $this->renderer->renderStatus(404, 'The requested page was not found.', $request);

            return new Response($content, 404, [
                'Content-Type' => 'text/html; charset=UTF-8',
            ]);
        }

        if ($status === Dispatcher::METHOD_NOT_ALLOWED) {
            $allowed = $routeInfo[1] ?? [];
            $message = 'Allowed methods: '.implode(', ', $allowed);
            $content = $this->renderer->renderStatus(405, $message, $request);

            return new Response($content, 405, [
                'Content-Type' => 'text/html; charset=UTF-8',
            ]);
        }

        if ($status !== Dispatcher::FOUND) {
            throw new \RuntimeException('Invalid route dispatch result.');
        }

        [$controller, $method] = $routeInfo[1];
        $vars = $routeInfo[2] ?? [];

        [$controllerInstance, $method, $arguments] = $this->resolver->resolve(
            $controller,
            $method,
            $vars
        );

        return call_user_func_array([$controllerInstance, $method], $arguments);
    }
}
