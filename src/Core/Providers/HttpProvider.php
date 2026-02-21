<?php

namespace Spacio\Framework\Core\Providers;

use Spacio\Framework\Container\Container;
use Spacio\Framework\Http\ControllerResolver;
use Spacio\Framework\Http\ExceptionRenderer;
use Spacio\Framework\Http\RouteHandler;
use Spacio\Framework\Http\RouteRegistrar;

class HttpProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->container->singleton(ControllerResolver::class, function (Container $container) {
            return new ControllerResolver($container);
        });

        $this->container->singleton(ExceptionRenderer::class, function () {
            return new ExceptionRenderer;
        });

        $this->container->singleton(RouteHandler::class, function (Container $container) {
            return new RouteHandler(
                $container->get(ControllerResolver::class),
                $container->get(ExceptionRenderer::class)
            );
        });

        $this->container->singleton(RouteRegistrar::class, function (Container $container) {
            return new RouteRegistrar(
                $container->get(\Spacio\Framework\Core\Config\ConfigRepository::class)
            );
        });
    }
}
