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
        $commands = $extraData = [];

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

                $qualifiedClass = $command->getQualifiedClass($this->getClassName($arguments));
                $exploded = explode('\\', $qualifiedClass);
                $extraData[$commandName] = [
                    'qualified_class' => $qualifiedClass,
                    'class' => end($exploded)
                ];
            }

            $commands[$commandName] = $command;
        }

        foreach ($commands as $commandName => $command) {
            $command->setData($extraData);

            $this->runCommand($command, $this->getArgumentsString($this->commands[$commandName]));
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
        $name = $this->getClassName($arguments);

        if (isset($arguments['arguments'])) {
            $name = $arguments['arguments'] . ' ' . $name;
        }

        return $name;
    }

    /**
     * Gets the class name for current input key "name".
     *
     * @param $arguments
     * @return array|string
     */
    public function getClassName($arguments)
    {
        $name = $this->argument('name');

        if (isset($arguments['suffix'])) {
            $name = $name . $arguments['suffix'];
        }

        return $name;
    }
}