<?php

namespace Synga\LaravelDevelopment;

use Illuminate\Support\ServiceProvider;
use Synga\LaravelDevelopment\Files\DevelopmentFile;
use Synga\LaravelDevelopment\Installer\PackageInstaller;
use Synga\LaravelDevelopment\Installer\Phase\Composer;
use Synga\LaravelDevelopment\Files\ComposerFile;

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
            __DIR__ . '/Config/packages.php' => config_path('packages.php'),
            __DIR__ . '/Config/development.php' => config_path('development.php'),
        ]);

        if ($this->app->runningInConsole()) {
            $developmentFile = new DevelopmentFile(base_path('development.json'));
            $this->commands(array_merge([
                \Synga\LaravelDevelopment\Console\Command\SetupDevelopmentCommand::class,
                \Synga\LaravelDevelopment\Console\Command\DeferComposerArtisanCommandsCommand::class,
                \Synga\LaravelDevelopment\Console\Command\RunCommandForPackageCommand::class,
                \Synga\LaravelDevelopment\Console\Command\CommandClassCommand::class,
                \Synga\LaravelDevelopment\Console\Command\SeedCommand::class
            ], $developmentFile->get('command')));
        }
    }

    /**
     * Register any package services and aliases.
     *
     * @return void
     */
    public function register()
    {
        \Event::listen('Illuminate\Console\Events\CommandStarting', function ($questionMark) {
            /* @var \Illuminate\Console\Events\CommandStarting $questionMark */
            if ('db:seed' === $questionMark->command) {
                \Event::fire('database.seed');
            }
        });

        $packagesConfig = \Config::get('packages');

        $this->app->bind(PackageInstaller::class, function () {
            $packageInstaller = new PackageInstaller();
            $packageInstaller->addPhases([
                new Composer(new ComposerFile('composer.json'))
            ]);

            return $packageInstaller;
        });

        if (!empty($packagesConfig)) {
            $serviceProviders = $aliases = [];;

            $dev = $this->app->environment('local');

            foreach ($packagesConfig as $packageConfig) {
                if (true !== $dev && true === $packageConfig['dev']) {
                    continue;
                }

                if (isset($packageConfig['service_providers']) && is_array($packageConfig['service_providers'])) {
                    $serviceProviders = array_merge($serviceProviders, $packageConfig['service_providers']);
                }

                if (isset($packageConfig['aliases']) && is_array($packageConfig['aliases'])) {
                    $aliases = array_merge($aliases, $packageConfig['aliases']);
                }
            }

            foreach ($serviceProviders as $serviceProvider) {
                if (class_exists($serviceProvider)) {
                    $this->app->register($serviceProvider);
                }
            }

            foreach ($aliases as $aliasName => $aliasClass) {
                if (class_exists($aliasClass)) {
                    $this->app->alias($aliasName, $aliasClass);
                }
            }
        }
    }
}