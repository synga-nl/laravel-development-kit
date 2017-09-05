# Laravel Development Kit (LDK)

This package provides some easy ways to develop with laravel. It is mainly focusses on setting up laravel and add 
easy ways to kickstart your development.

## Installation

### Install with composer

```bash
$ composer require synga/laravel-development-kit:dev-master
```

### Publish the package

```bash
$ php artisan vendor:publish --provider="Synga\LaravelDevelopment\LaravelDevelopmentServiceProvider"
```

## Functionalities

This packages has currently two main functionalities: Install packages and executing "make" commands for a certain 
package.

### Install packages

In the development.php config file you can specify which packages should be installed. You can do this by providing 
an array.

```php 
return [
    'packages' => [
        [
            'composer' => [
                'name' => 'barryvdh/laravel-ide-helper',
                'commands' => [
                    'post-update-cmd' => [
                        'artisan' => [
                            ['command' => 'ide-helper:generate', 'after' => 'Illuminate\\Foundation\\ComposerScripts::postInstall'],
                            ['command' => 'ide-helper:meta', 'after' => 'Illuminate\\Foundation\\ComposerScripts::postInstall']
                        ],
                        'shell' => [
                            // No shell commands for this package
                        ]
                    ],
                    'post-install-cmd' => [
                        'artisan' => [
                            ['command' => 'ide-helper:generate', 'after' => 'Illuminate\\Foundation\\ComposerScripts::postInstall'],
                            ['command' => 'ide-helper:meta', 'after' => 'Illuminate\\Foundation\\ComposerScripts::postInstall']
                        ],
                    ]
                ],
            ],
            'dev' => true,
            'service_providers' => [
                \Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class,
            ],
            'aliases' => [
                // no aliases for this package
            ]
        ],
    ],
];
```

With the composer key you can specify the following keys:
 
- Name: the name of the package. If you want a version constraint you can add a colon (:). For instance 
barryvdh/laravel-ide-helper:^3.0.
- Commands: You can add commands 
