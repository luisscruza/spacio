<?php

use Spacio\Framework\Container\Container;
use Spacio\Framework\Core\Support\Container as ContainerSupport;

$container = new Container;
$container->instance(Container::class, $container);

ContainerSupport::registerProviders($container, [
    BASE_PATH.'/app/Providers' => 'App\\Providers',
    BASE_PATH.'/src/Core/Providers' => 'Spacio\\Framework\\Core\\Providers',
]);

return $container;
