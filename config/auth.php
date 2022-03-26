<?php

return [
    'defaults' => [
        'guard' => 'admin',
        'passwords' => 'admins',
    ],

    'guards' => [
        'admin' => [
            'driver' => 'jwt',
            'provider' => 'admins',
        ],
        'user' => [
            'driver' => 'jwt',
            'provider' => 'users'
        ]
    ],

    'providers' => [
        'admins' => [
            'driver' => 'eloquent',
            'model' => \App\Models\Admin::class
        ],
        'users' => [
            'driver' => 'eloquent',
            'model' => \App\Models\User::class
        ]
    ]
];
