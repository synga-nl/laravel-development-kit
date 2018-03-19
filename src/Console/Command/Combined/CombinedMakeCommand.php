<?php

namespace Synga\LaravelDevelopment\Console\Command\Combined;

use Illuminate\Console\Command;
use Synga\LaravelDevelopment\Console\Command\Modified\ModifyCommandTrait;
use Synga\LaravelDevelopment\RunCommandTrait;

/**
 * Class ApiModelControllerResourceCommand
 * @package Synga\LaravelDevelopment\Console\Command\Combined
 */
abstract class CombinedMakeCommand extends Command
{
    use RunCommandTrait, ModifyCommandTrait;

    /**
     * The commands available in the current command.
     *
     * @var array
     */
    protected $commands = [];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = '';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        foreach ($this->commands as $commandName => $arguments) {
            if (!is_array($arguments)) {
                $commandName = $arguments;
            }

            $command = $this->createCommand($commandName);

            if (method_exists($command, 'setData')) {
                $command->setData([
                    'root_namespace' => $this->mandatoryData['root_namespace'],
                    'path' => $this->mandatoryData['path'],
                ]);
            }

            $this->runCommand($command, $this->getArgumentsString($arguments));
        }
    }

    /**
     * Creates a command argument string based on the array $arguments.
     *
     * @param $arguments
     * @return array|string
     */
    protected function getArgumentsString($arguments)
    {
        $name = $this->argument('name');

        if (isset($arguments['suffix'])) {
            $name = $name . $arguments['suffix'];
        }

        if (isset($arguments['arguments'])) {
            $name = $arguments['arguments'] . ' ' . $name;
        }

        return $name;
    }
}