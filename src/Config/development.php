<?php
return [
    'packages' => [
        [
            'composer'          => [
                'name'             => 'jeroen-g/laravel-packager',
                'update_commands'  => [
                    'artisan' => [
                        'ide-helper:generate',
                    ],
                    'shell'   => [],
                ],
                'install_commands' => [],
            ],
            'dev'               => true,
            'service_providers' => [
                \JeroenG\Packager\PackagerServiceProvider::class,
            ],
        ],
        [
            'composer'          => [
                'name'             => 'barryvdh/laravel-debugbar',
                'update_commands'  => [

                ],
                'install_commands' => [

                ],
            ],
            'dev'               => true,
            'service_providers' => [
                \Barryvdh\Debugbar\ServiceProvider::class,
            ],
        ],
        [
            'composer'          => [
                'name'             => 'barryvdh/laravel-ide-helper',
                'update_commands'  => [
                    'artisan' => [
                        'ide-helper:generate',
                        'ide-helper:meta'
                    ],
                ],
                'install_commands' => [
                    'artisan' => [
                        'ide-helper:generate',
                        'ide-helper:meta'
                    ],
                ],
            ],
            'dev'               => true,
            'service_providers' => [
                \Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class,
            ],
        ],
    ],
];