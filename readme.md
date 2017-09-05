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
            'aliasses' => [
                // no aliasses for this package
            ]
        ],
    ],
];
```

The basic anatomy of the array is as follows:

- Composer: This is explained below
- Dev: is the package only needed for the development environment
- Service provider: specify all service providers needed for this package
- Aliasses: specify all aliasses needed for this package

With the composer key you can specify the following keys:
 
- Name: the name of the package. If you want a version constraint you can add a colon (:). For instance 
barryvdh/laravel-ide-helper:^3.0.
- Commands: You can add commands to certain events in composer. A list of events can be found here: 
https://getcomposer.org/doc/articles/scripts.md. Each event can have two keys: artisan and shell. 
    - Artisan: an array with command (do not add php artisan) and a key after, which indicates after which command it
     should be executed.
    - Shell: an array with the command and after which command it should execute

### Execute commands for a package

This package provides the ```bash php artisan development:command``` command. 

When you execute this command, you get the
question for which package you want to execute a command. All the packages in the packages directory (made with the package Jeroen-G/packager) are listed. After 
the selection of the package you get the question which command you want to execute. After the selection of the command 
you can specify some arguments. After you executed the command the whole process starts again.


