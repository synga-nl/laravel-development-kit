<?php
return [
    'packages' => [
        [
            'composer' => [
                'name' => 'jeroen-g/laravel-packager',
            ],
            'dev' => true,
            'service_providers' => [
                \JeroenG\Packager\PackagerServiceProvider::class,
            ],
        ],
        [
            'composer' => [
                'name' => 'barryvdh/laravel-debugbar:2.*',
            ],
            'dev' => true,
            'service_providers' => [
                \Barryvdh\Debugbar\ServiceProvider::class,
            ],
        ],
        [
            'composer' => [
                'name' => 'barryvdh/laravel-ide-helper',
                'commands' => [
                    'post-update-cmd' => [
                        'artisan' => [
                            ['command' => 'ide-helper:generate', 'after' => 'Illuminate\\Foundation\\ComposerScripts::postInstall'],
                            ['command' => 'ide-helper:meta', 'after' => 'Illuminate\\Foundation\\ComposerScripts::postInstall']
                        ]
                    ],
                    'post-install-cmd' => [
                        'artisan' => [
                            ['command' => 'ide-helper:generate', 'after' => 'Illuminate\\Foundation\\ComposerScripts::postInstall'],
                            ['command' => 'ide-helper:meta', 'after' => 'Illuminate\\Foundation\\ComposerScripts::postInstall']
                        ]
                    ]
                ],
            ],
            'dev' => true,
            'service_providers' => [
                \Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class,
            ],
        ],
    ],
];