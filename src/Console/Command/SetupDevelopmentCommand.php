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
    protected $signature = 'development:setup';

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

        $packageInstaller->install($configuration);
    }

    /**
     * @return ConfigurationHandler
     */
    private function getConfiguration()
    {
        $developmentConfigFile = config_path('development.php');

        if (!file_exists($developmentConfigFile)) {
            $this->call('vendor:publish',
                ['--provider' => 'Synga\LaravelDevelopment\LaravelDevelopmentServiceProvider']);
            $developmentConfig = include_once config_path('development.php');
        } else {
            $developmentConfig = \Config::get('development');
        }

        return new ConfigurationHandler($developmentConfig);
    }
}