<?php

namespace Spacio\Framework\Http;

use FastRoute\RouteCollector;
use Spacio\Framework\Container\Container;

use Throwable;
use function FastRoute\simpleDispatcher;

class Kernel
{
    public function __construct(
        protected Container $container,
        protected ControllerResolver $resolver,
    ) {}

    public function handle(Request $request): Response
    {
        try {
            $this->container->instance(Request::class, $request);

            $dispatcher = simpleDispatcher(function (RouteCollector $collector) {
                $routes = require BASE_PATH.'/routes/web.php';

                foreach ($routes as $route) {
                    $collector->addRoute(
                        ...$route
                    );
                }
            });

            $routeInfo = $dispatcher->dispatch(
                $request->getMethod(),
                $request->getUri()
            );

            [$status, [$controller, $method], $vars] = $routeInfo;

            [$controllerInstance, $method, $arguments] = $this->resolver->resolve(
                $controller,
                $method,
                $vars
            );

            return call_user_func_array([$controllerInstance, $method], $arguments);
        } catch (Throwable $throwable) {
            $renderer = new ExceptionRenderer;
            $content = $renderer->render($throwable, $request);

            return new Response($content, 500, [
                'Content-Type' => 'text/html; charset=UTF-8',
            ]);
        }
    }
}
