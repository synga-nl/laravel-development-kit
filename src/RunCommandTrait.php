<?php

namespace Synga\LaravelDevelopment;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Finder\Finder;

/**
 * Trait RunCommandTrait
 * @package Synga\LaravelDevelopment
 */
trait RunCommandTrait
{
    /**
     * @var array
     */
    protected $overriddenCommands = [];

    /**
     * Returns all overridden commands
     */
    protected function getOverriddenCommands()
    {
        if (empty($this->overriddenCommands)) {
            $this->overriddenCommands = \Config::get('development.commands', []);
        }

        return $this->overriddenCommands;
    }

    /**
     * @param $commandName
     *
     * @return Command
     */
    public function createCommand($commandName)
    {
        $overriddenCommands = $this->getOverriddenCommands();

        if (isset($overriddenCommands[$commandName])) {
            return \App::make($overriddenCommands[$commandName]);
        }

        foreach (\Artisan::all() as $command) {
            if ($commandName === $command->getName()) {
                return $command;
            }
        }
    }

    /**
     * Run a overridden/modified Laravel command.
     *
     * @param $commands
     * @param $commandName
     * @param $package
     * @param $packageNames
     */
    public function runOverriddenCommand($commands, $commandName, $package, $packageNames)
    {
        $command = $this->createCommand($commands[$commandName]->getName());
        if (!empty($command)) {
            if (method_exists($command, 'setData')) {
                $command->setData([
                    'root_namespace' => str_replace('/', '\\', $package),
                    'path' => $packageNames[$package]->getPathname() . DIRECTORY_SEPARATOR . 'src',
                ]);
            }

            $this->info($command->getSynopsis());
            $parameters = $this->ask('Please fill in the command parameters');

            $this->runCommand($command, $parameters);
        }
    }

    /**
     * Run a default Laravel command.
     *
     * @param $commandName
     */
    public function runLaravelCommand($commandName)
    {
        foreach (\Artisan::all() as $name => $command) {
            if ($commandName === $name) {
                $laravelCommand = $command;
            }
        }

        if (isset($laravelCommand)) {
            $this->info($laravelCommand->getSynopsis());
            $parameters = $commandName . ' ' . $this->ask('Please fill in the command parameters');

            $this->runCommand($laravelCommand, $parameters);
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
     * @param $directory
     * @return array
     */
    public function getPackageNames($directory)
    {
        $packageNames = [];

        if (file_exists($directory)) {
            foreach (Finder::create()->in($directory)->directories()->depth(1) as $dir) {
                $packageNames[str_replace($directory . DIRECTORY_SEPARATOR, '', $dir->getPathname())] = $dir;
            }
        }

        return $packageNames;
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