<?php

namespace Spacio\Framework\Core\Providers;

use Spacio\Framework\Container\Container;

abstract class ServiceProvider
{
    public function __construct(
        protected Container $container,
    ) {}

    abstract public function register(): void;
}
