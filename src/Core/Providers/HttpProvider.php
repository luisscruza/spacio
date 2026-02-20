<?php

namespace Spacio\Framework\Core\Providers;

use Spacio\Framework\Container\Container;
use Spacio\Framework\Http\ControllerResolver;

class HttpProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->container->singleton(ControllerResolver::class, function (Container $container) {
            return new ControllerResolver($container);
        });
    }
}
