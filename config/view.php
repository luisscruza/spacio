<?php

return [
    'path' => BASE_PATH.'/views',
    'extension' => '.spacio.php',
    'directives' => [
        'paths' => [
            BASE_PATH.'/src/View/Directives',
            BASE_PATH.'/app/View/Directives',
        ],
        'namespaces' => [
            'Spacio\\Framework\\View\\Directives',
            'App\\View\\Directives',
        ],
    ],
];
