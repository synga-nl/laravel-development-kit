<?php

namespace Synga\LaravelDevelopment\Console\Command;

use Illuminate\Console\Command;
use Synga\LaravelDevelopment\Installer\ConfigurationHandler;
use Synga\LaravelDevelopment\Installer\PackageInstaller;

/**
 * Class SetupDevelopmentCommand
 * @package Synga\LaravelDevelopment\Console\Command
 */
class SetupDevelopmentCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'development:setup {--skip-approval}';

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
    public function handle(PackageInstaller $packageInstaller)
    {
        $configuration = $this->getConfiguration();

        $packageInstaller->install($configuration, $this->option('skip-approval'));
    }

    /**
     * @return ConfigurationHandler
     */
    private function getConfiguration()
    {
        $developmentConfigFile = config_path('packages.php');

        if (!file_exists($developmentConfigFile)) {
            $this->call('vendor:publish',
                ['--provider' => 'Synga\LaravelDevelopment\LaravelDevelopmentServiceProvider']);

            $this->confirm('The configuration files are published, you can now edit them before the file is being processed, press enter when you are done.', true);

            $developmentConfig = include_once config_path('packages.php');
        } else {
            $developmentConfig = \Config::get('packages');
        }

        return new ConfigurationHandler($developmentConfig);
    }
}