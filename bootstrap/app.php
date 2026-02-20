<?php

use Spacio\Framework\Container\Container;
use Spacio\Framework\Core\Support\App;
use Spacio\Framework\Core\Support\Container as ContainerSupport;
use Spacio\Framework\Core\Support\Env;

$container = new Container;
$container->instance(Container::class, $container);
App::setContainer($container);

Env::load(BASE_PATH.'/.env');

ContainerSupport::registerProviders($container, [
    BASE_PATH.'/app/Providers' => 'App\\Providers',
    BASE_PATH.'/src/Core/Providers' => 'Spacio\\Framework\\Core\\Providers',
]);

return $container;
