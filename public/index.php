<?php

use Spacio\Framework\Http\Kernel;
use Spacio\Framework\Http\Request;
use Spacio\Framework\Http\Session;

define('BASE_PATH', dirname(__DIR__));

require_once BASE_PATH.'/vendor/autoload.php';

Session::start();

$container = require BASE_PATH.'/bootstrap/app.php';

$request = Request::create();

$kernel = $container->get(Kernel::class);

$response = $kernel->handle($request);

$response->send();
