<?php

namespace Spacio\Framework\Http;

use FastRoute\RouteCollector;
use Spacio\Framework\Container\Container;
use Spacio\Framework\Http\Exceptions\HttpException;
use Throwable;

use function FastRoute\simpleDispatcher;

class Kernel
{
    public function __construct(
        protected Container $container,
    ) {}

    public function handle(Request $request): Response
    {
        try {
            $this->container->instance(Request::class, $request);
            $routeHandler = $this->container->get(RouteHandler::class);
            $registrar = $this->container->get(RouteRegistrar::class);

            $dispatcher = simpleDispatcher(function (RouteCollector $collector) {
                $routes = $this->container->get(RouteRegistrar::class)->routes();

                foreach ($routes as $route) {
                    $collector->addRoute(...$route);
                }
            });

            $routeInfo = $dispatcher->dispatch(
                $request->getMethod(),
                $request->getUri()
            );

            return $routeHandler->handle($routeInfo, $request);
        } catch (Throwable $throwable) {
            $renderer = new ExceptionRenderer;

            if ($throwable instanceof HttpException) {
                $content = $renderer->renderStatus(
                    $throwable->getStatusCode(),
                    $throwable->getMessage(),
                    $request
                );

                return new Response($content, $throwable->getStatusCode(), [
                    'Content-Type' => 'text/html; charset=UTF-8',
                ]);
            }

            $content = $renderer->render($throwable, $request);

            return new Response($content, 500, [
                'Content-Type' => 'text/html; charset=UTF-8',
            ]);
        }
    }
}
