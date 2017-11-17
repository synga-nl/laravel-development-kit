<?php

namespace Synga\LaravelDevelopment\Console\Command;

use Illuminate\Console\Command;
use Synga\LaravelDevelopment\Console\ApproveExecCommand;

/**
 * Class DeferComposerArtisanCommandsCommand
 * @package Synga\LaravelDevelopment\Console\Command
 */
class DeferComposerCommandsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'development:commands {type} {--skip-approval}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Executes all the commands specified in the development config';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $type = $this->argument('type');

        $packages = \Config::get('packages');

        foreach ($packages as $package) {
            $commands = array_get($package, 'composer.commands.' . $type);
            if (!empty($commands)) {
                if (isset($commands['artisan'])) {
                    $this->executeCommands($commands['artisan'], function ($command) {
                        if (isset(\Artisan::all()[$command])) {
                            return true;
                        }

                        return false;
                    });
                }

                if (isset($commands['shell'])) {
                    foreach ($commands['shell'] as $artisanCommand) {
                        $command = (is_array($artisanCommand)) ? $artisanCommand['command'] : $artisanCommand;

                        ApproveExecCommand::exec($command, $this->option('skip-approval'));
                    }
                }
            }
        }
    }

    /**
     * Executes an artisan command
     *
     * @param $commands
     * @param callable|null $validateCommand
     */
    private function executeCommands($commands, callable $validateCommand = null)
    {
        foreach ($commands as $artisanCommand) {
            $command = (is_array($artisanCommand)) ? $artisanCommand['command'] : $artisanCommand;

            if (null !== $validateCommand) {
                if (false === $validateCommand($command)) {
                    continue;
                }
            }

            $this->info('Executing: ' . $command);

            $this->call($command);
        }
    }
}