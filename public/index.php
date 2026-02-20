<?php

use Spacio\Framework\Http\Kernel;
use Spacio\Framework\Http\Request;

define('BASE_PATH', dirname(__DIR__));

require_once BASE_PATH.'/vendor/autoload.php';

$request = Request::create();

$kernel = new Kernel;

$response = $kernel->handle($request);

$response->send();
