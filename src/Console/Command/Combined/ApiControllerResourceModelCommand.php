<?php
/**
 * Created by PhpStorm.
 * User: roypouls
 * Date: 19/03/2018
 * Time: 10:57
 */

namespace Synga\LaravelDevelopment\Console\Command\Combined;


class ApiControllerResourceModelCommand extends CombinedMakeCommand
{
    /**
     * @var array
     */
    protected $commands = [
        'make:controller' => [
            'suffix' => 'Controller',
            'arguments' => '--api',
            'option' => '-c'
        ],
        'make:model' => [
            'option' => '-c'
        ],
        'make:resource' => [
            'suffix' => 'Resource'
        ]
    ];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'development:api-model-controller-resource {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Executes all the commands specified in the development config';
}