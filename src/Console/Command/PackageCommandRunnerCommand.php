<?php

namespace Synga\LaravelDevelopment\Console\Command;

use Illuminate\Console\Command;
use Synga\LaravelDevelopment\RunCommandTrait;

/**
 * Class RunCommandForPackageCommand
 * @package Synga\LaravelDevelopment\Console\Command
 */
class PackageCommandRunnerCommand extends Command
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
     * Return all modified commands
     *
     * @return array
     */
    public function getCommands()
    {
        $result = [];

        foreach ($this->getOverriddenCommands() as $commandClassName) {
            $object = app()->make($commandClassName);

            $result[$object->getName()] = $object;
        };

        return $result;
    }

    /**
     * @return bool
     */
    public function newCommand()
    {
        $packageNames = array_merge(
            $this->getPackageNames(\Config::get('development.packages_directory')),
            ['New package' => 'new_package', 'exit' => 'exit']
        );

        $commands = $this->getCommands();
        $commands = array_merge($commands, ['exit' => 'exit']);

        $package = $this->choice('In which package do you want to work?', array_keys($packageNames));

        if ('new_package' === $packageNames[$package]) {
            $this->runLaravelCommand('packager:new');

            return true;
        }

        if ('exit' === $package) {
            $this->info('We wish you all good fortune and happiness for the future!');

            return false;
        };

        $commandName = $this->choice('What is the command you want to execute', array_keys($commands));
        if ('exit' === $commandName) {
            $this->info('We wish you all good fortune and happiness for the future!');

            return false;
        };

        $this->runOverriddenCommand($commands, $commandName, $package, $packageNames);
    }
}