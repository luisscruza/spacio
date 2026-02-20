<?php

namespace Spacio\Framework\Core\Support;

use Spacio\Framework\Container\Container as ContainerInstance;
use Spacio\Framework\Core\Providers\ServiceProvider;

class Container
{
    public static function registerProviders(ContainerInstance $container, array $groups): void
    {
        foreach ($groups as $path => $namespace) {
            if (! is_dir($path)) {
                continue;
            }

            $files = glob($path.'/*.php') ?: [];
            sort($files);

            foreach ($files as $file) {
                $class = $namespace.'\\'.pathinfo($file, PATHINFO_FILENAME);

                if (! class_exists($class) || ! is_subclass_of($class, ServiceProvider::class)) {
                    continue;
                }

                $provider = new $class($container);
                $provider->register();
            }
        }
    }
}
