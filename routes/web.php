<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;

return [
    ['GET', '/', [HomeController::class, 'index']],
    ['GET', '/users/{user}', [UserController::class, 'show']],
];
