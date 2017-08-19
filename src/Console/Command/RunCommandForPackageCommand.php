<?php

namespace Synga\LaravelDevelopment\Console\Command;

use Illuminate\Console\Command;
use Symfony\Component\Finder\Finder;
use Synga\LaravelDevelopment\RunCommandTrait;

/**
 * Class RunCommandForPackageCommand
 * @package Synga\LaravelDevelopment\Console\Command
 */
class RunCommandForPackageCommand extends Command
{
    use RunCommandTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'development:command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup laravel for development';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        do {
            $result = $this->newCommand();
        } while (false !== $result);
    }

    /**
     * @param $directory
     * @return array
     */
    public function getPackageNames($directory)
    {
        $packageNames = [];

        foreach (Finder::create()->in($directory)->directories()->depth(1) as $dir) {
            $packageNames[str_replace($directory . DIRECTORY_SEPARATOR, '', $dir->getPathname())] = $dir;
        }

        return $packageNames;
    }

    /**
     * @return array
     */
    public function getCommands()
    {
        $commands = [];

        foreach (\Artisan::all() as $command) {
            /* @var $command \Symfony\Component\Console\Command\HelpCommand */
            if (starts_with($command->getName(), 'make:')) {
                $commands[$command->getName()] = $command;
            }
        }

        return $commands;
    }

    /**
     * @return bool
     */
    public function newCommand()
    {
        $packagesDirectory = 'packages';
        $directory = base_path($packagesDirectory);

        $packageNames = $this->getPackageNames($directory);
        $packageNames = array_merge($packageNames, ['exit' => 'exit']);

        $commands = $this->getCommands();
        $commands = array_merge($commands, ['exit' => 'exit']);

        $package = $this->choice('In which package do you want to work?', array_keys($packageNames));
        if ('exit' === $package) {
            $this->info('we wish you all good fortune and happiness for the future!');
            return false;
        };

        $commandName = $this->choice('What is the command you want to execute', array_keys($commands));
        if ('exit' === $commandName) {
            $this->info('we wish you all good fortune and happiness for the future!');
            return false;
        };

        $this->runCommand($commands, $commandName, $package, $packageNames);
    }

    /**
     * @param $commands
     * @param $commandName
     * @param $package
     * @param $packageNames
     */
    public function runCommand($commands, $commandName, $package, $packageNames)
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
}