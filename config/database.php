<?php

use Spacio\Framework\Database\Drivers\SQLiteDriver;

return [
    'default' => 'sqlite',

    'drivers' => [
        'sqlite' => SQLiteDriver::class,
    ],

    'connections' => [
        'sqlite' => [
            'database' => env('DB_DATABASE', 'database/database.sqlite'),
        ],
    ],
];
