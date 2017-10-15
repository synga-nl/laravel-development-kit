<?php
return [
    [
        'composer' => [
            'name' => 'jeroen-g/laravel-packager',
        ],
        'dev' => true,
        'service_providers' => [
            \JeroenG\Packager\PackagerServiceProvider::class,
        ],
        'publish' => [
            '--provider="JeroenG\Packager\PackagerServiceProvider"'
        ]
    ],
    [
        'composer' => [
            'name' => 'barryvdh/laravel-debugbar',
        ],
        'dev' => true,
        'service_providers' => [
            \Barryvdh\Debugbar\ServiceProvider::class,
        ],
        'publish' => [
            '--provider="Barryvdh\Debugbar\ServiceProvider"'
        ]
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
        'publish' => [
            '--provider="Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider" --tag=config'
        ]
    ],
];