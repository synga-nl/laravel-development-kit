<?php

namespace Synga\LaravelDevelopment;

use Illuminate\Support\ServiceProvider;

/**
 * Class LaravelDevelopmentServiceProvider
 * @package Synga\LaravelDevelopment
 */
class LaravelDevelopmentServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/Config/development.php' => config_path('development.php'),
        ]);

        if ($this->app->runningInConsole()) {
            $this->commands([
                \Synga\LaravelDevelopment\Console\Command\SetupDevelopmentCommand::class,
                \Synga\LaravelDevelopment\Console\Command\DeferComposerArtisanCommandsCommand::class,
                \Synga\LaravelDevelopment\Console\Command\RunCommandForPackageCommand::class,
            ]);
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $packagesConfig = \Config::get('development.packages');

        if (!empty($packagesConfig)) {
            $production = [];
            $development = [];

            foreach ($packagesConfig as $packageConfig) {
                if (true === $packageConfig['dev']) {
                    $development = array_merge($development, $packageConfig['service_providers']);
                } else {
                    $production = array_merge($production, $packageConfig['service_providers']);
                }
            }

            if ($this->app->environment('production')) {
                foreach ($production as $productionServiceProvider) {
                    $this->app->register($productionServiceProvider);
                }
            } else {
                foreach ($development as $developmentServiceProvider) {
                    $this->app->register($developmentServiceProvider);
                }
            }
        }
    }
}