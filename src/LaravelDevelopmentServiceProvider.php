<?php

namespace Synga\LaravelDevelopment;

use Illuminate\Support\ServiceProvider;
use Synga\LaravelDevelopment\Installer\PackageInstaller;
use Synga\LaravelDevelopment\Installer\Phase\Composer;

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
     * Register any package services and aliases.
     *
     * @return void
     */
    public function register()
    {
        $packagesConfig = \Config::get('development.packages');

        $this->app->bind(PackageInstaller::class, function () {
            $packageInstaller = new PackageInstaller();
            $packageInstaller->addPhases([
                new Composer(new ComposerFile('composer.json'))
            ]);

            return $packageInstaller;
        });

        if (!empty($packagesConfig)) {
            $productionServiceProviders = $developmentServiceProviders = $productionAliases = $developmentAliases = [];;

            // @todo This can be done better, lets improve this
            foreach ($packagesConfig as $packageConfig) {
                if (true === $packageConfig['dev']) {
                    $developmentServiceProviders = array_merge(
                        $developmentServiceProviders,
                        $packageConfig['service_providers']
                    );
                    $developmentAliases = array_merge(
                        $developmentAliases,
                        $packageConfig['aliasses']
                    );
                } else {
                    $productionServiceProviders = array_merge(
                        $productionServiceProviders,
                        $packageConfig['service_providers']
                    );
                    $productionAliases = array_merge(
                        $productionAliases,
                        $packageConfig['aliasses']
                    );
                }
            }

            if ($this->app->environment('production')) {
                foreach ($productionServiceProviders as $productionServiceProvider) {
                    $this->app->register($productionServiceProvider);
                }

                foreach ($productionAliases as $productionAliasName => $productionAlias) {
                    $this->app->alias($productionAliasName, $productionAlias);
                }
            } else {
                foreach ($developmentServiceProviders as $developmentServiceProvider) {
                    $this->app->register($developmentServiceProvider);
                }

                foreach ($developmentAliases as $developmentAliasName => $developmentAlias) {
                    $this->app->alias($developmentAliasName, $developmentAlias);
                }
            }
        }
    }
}