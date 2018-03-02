<?php
return [
    'jeroen-g/laravel-packager' => [
        'development' => true,
        'publish' => [
            '--provider="JeroenG\Packager\PackagerServiceProvider"'
        ]
    ],
    'barryvdh/laravel-debugbar' => [
        'development' => true,
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
        'publish' => [
            '--provider="Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider" --tag=config'
        ]
    ],
];