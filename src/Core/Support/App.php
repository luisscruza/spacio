<?php

namespace Spacio\Framework\Core\Support;

use RuntimeException;
use Spacio\Framework\Container\Container;

class App
{
    protected static ?Container $container = null;

    public static function setContainer(Container $container): void
    {
        self::$container = $container;
    }

    public static function container(): Container
    {
        if (! self::$container) {
            throw new RuntimeException('Application container is not set.');
        }

        return self::$container;
    }
}
