<?php

namespace Synga\LaravelDevelopment\Console\Command;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\StringInput;
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

        if (file_exists($directory)) {
            foreach (Finder::create()->in($directory)->directories()->depth(1) as $dir) {
                $packageNames[str_replace($directory . DIRECTORY_SEPARATOR, '', $dir->getPathname())] = $dir;
            }
        }

        return $packageNames;
    }

    /**
     * Return all modified commands
     *
     * @return array
     */
    public function getCommands()
    {
        $result = [];

        foreach ($this->overriddenCommands as $commandClassName) {
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
        $packagesDirectory = 'packages';
        $directory = base_path($packagesDirectory);

        $packageNames = $this->getPackageNames($directory);
        if (empty($packageNames)) {
            $this->info('No packages are found');
            return false;
        }

        $packageNames = array_merge($packageNames, ['New package' => 'new_package', 'exit' => 'exit']);

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

        $this->runCommandOverriddenCommand($commands, $commandName, $package, $packageNames);
    }

    /**
     * Run a overridden/modified Laravel command.
     *
     * @param $commands
     * @param $commandName
     * @param $package
     * @param $packageNames
     */
    public function runCommandOverriddenCommand($commands, $commandName, $package, $packageNames)
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
     * Run the actual command with the given parameters.
     *
     * @param Command $command
     * @param string $parameters
     */
    public function runCommand(Command $command, string $parameters)
    {
        $command->setLaravel($this->laravel);
        $command->run(new StringInput($parameters), $this->output);
    }
}