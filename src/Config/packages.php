<?php
return [
    'jeroen-g/laravel-packager' => [
        'development' => true,
        'service_providers' => [
            \JeroenG\Packager\PackagerServiceProvider::class,
        ],
        'publish' => [
            '--provider="JeroenG\Packager\PackagerServiceProvider"'
        ]
    ],
    'barryvdh/laravel-debugbar' => [
        'development' => true,
        'service_providers' => [
            \Barryvdh\Debugbar\ServiceProvider::class,
        ],
        'publish' => [
            '--provider="Barryvdh\Debugbar\ServiceProvider"'
        ]
    ],
    'barryvdh/laravel-ide-helper' => [
        'composer' => [
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
        'development' => true,
        'service_providers' => [
            \Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class,
        ],
        'publish' => [
            '--provider="Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider" --tag=config'
        ]
    ],
];