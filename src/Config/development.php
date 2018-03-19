<?php
return [
    'stubs' => [
        ''
    ],
    'file' => base_path('development.json'),
    'packages_directory' => base_path('packages'),
    'commands' => [
        'make:model' => \Synga\LaravelDevelopment\Console\Command\Modified\ModelMakeCommand::class,
        'make:migration' => \Synga\LaravelDevelopment\Console\Command\Modified\MigrateMakeCommand::class,
        'make:command' => \Synga\LaravelDevelopment\Console\Command\Modified\ConsoleMakeCommand::class,
        'make:controller' => \Synga\LaravelDevelopment\Console\Command\Modified\ControllerMakeCommand::class,
        'make:resource' => \Synga\LaravelDevelopment\Console\Command\Modified\ResourceMakeCommand::class,
        'make:seeder' => \Synga\LaravelDevelopment\Console\Command\Modified\SeederMakeCommand::class,
        'make:route' => \Synga\LaravelDevelopment\Console\Command\Modified\RouteMakeCommand::class,
        'development:api-model-controller-resource' => \Synga\LaravelDevelopment\Console\Command\Combined\ApiControllerResourceModelCommand::class,
    ]
];