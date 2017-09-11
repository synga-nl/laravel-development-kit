<?php

namespace Synga\LaravelDevelopment;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\StringInput;

/**
 * Trait RunCommandTrait
 * @package Synga\LaravelDevelopment
 */
trait RunCommandTrait
{
    /**
     * @var array
     */
    protected $overriddenCommands = [
        'make:model' => \Synga\LaravelDevelopment\Console\Command\Modified\ModelMakeCommand::class,
        'make:migration' => \Synga\LaravelDevelopment\Console\Command\Modified\MigrateMakeCommand::class,
        'make:command' => \Synga\LaravelDevelopment\Console\Command\Modified\ConsoleMakeCommand::class,
        'make:controller' => \Synga\LaravelDevelopment\Console\Command\Modified\ControllerMakeCommand::class,
        'make:resource' => \Synga\LaravelDevelopment\Console\Command\Modified\ResourceMakeCommand::class,
        'make:seeder' => \Synga\LaravelDevelopment\Console\Command\Modified\SeederMakeCommand::class
    ];

    /**
     * @param $commandName
     *
     * @return Command
     */
    public function createCommand($commandName)
    {
        if (isset($this->overriddenCommands[$commandName])) {
            return \App::make($this->overriddenCommands[$commandName]);
        }

        foreach (\Artisan::all() as $command) {
            if ($commandName === $command->getName()) {
                return $command;
            }
        }
    }

    /**
     * @param $command
     * @param $input
     */
    protected function runCommand(Command $command, $input)
    {
        $command->setLaravel($this->getLaravel());

        $command->run($this->getInput($input), $this->output);
    }

    /**
     * @param $input
     *
     * @return \Symfony\Component\Console\Input\ArrayInput|\Symfony\Component\Console\Input\StringInput
     */
    protected function getInput($input)
    {
        if (is_string($input)) {
            return new StringInput($input);
        }

        return new ArrayInput((is_array($input) ? $input : []));
    }
}