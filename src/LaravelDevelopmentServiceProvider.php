<?php

namespace Synga\LaravelDevelopment;

use Illuminate\Console\Events\CommandStarting;
use Illuminate\Support\ServiceProvider;
use Synga\LaravelDevelopment\Console\ConfirmShellCommand;
use Synga\LaravelDevelopment\Files\ComposerLockFile;
use Synga\LaravelDevelopment\Files\DevelopmentFile;
use Synga\LaravelDevelopment\Installer\PackageInstaller;
use Synga\LaravelDevelopment\Installer\Phase\Composer;
use Synga\LaravelDevelopment\Files\ComposerFile;
use Synga\LaravelDevelopment\Installer\Phase\PublishResources;
use Synga\LaravelDevelopment\Packages\Configuration\Merge\MergeArray;
use Synga\LaravelDevelopment\Packages\Configuration\Merge\MergeComposer;
use Synga\LaravelDevelopment\Packages\Configuration\Merge\MergeScalar;
use Synga\LaravelDevelopment\Packages\Configuration\MergeConfiguration;

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
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/Config/packages.php' => config_path('packages.php'),
            __DIR__ . '/Config/development.php' => config_path('development.php'),
        ]);

        if ($this->app->runningInConsole()) {
            $developmentFile = $this->app->get(DevelopmentFile::class);

            $commands = [];

            foreach ($developmentFile->get('command') as $command) {
                if (class_exists($command)) {
                    $commands[] = $command;

                    continue;
                }

                $developmentFile->deleteValue('command', $command);
            }

            $this->commands(array_merge([
                \Synga\LaravelDevelopment\Console\Command\SetupDevelopmentCommand::class,
                \Synga\LaravelDevelopment\Console\Command\ComposerScriptRunnerCommand::class,
                \Synga\LaravelDevelopment\Console\Command\PackageCommandRunnerCommand::class,
                \Synga\LaravelDevelopment\Console\Command\ConsoleClassFinderCommand::class,
                \Synga\LaravelDevelopment\Console\Command\SeedCommand::class
            ], $commands));

            \Event::listen(CommandStarting::class, function (CommandStarting $event) {
                ConfirmShellCommand::setInputOutput($event->input, $event->output);

                /* @var CommandStarting $event */
                if ('db:seed' === $event->command) {
                    \Event::fire('database.seed');
                }
            });
        }
    }

    /**
     * Register any package services and aliases.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(PackageInstaller::class, function () {
            $packageInstaller = new PackageInstaller();
            $packageInstaller->addPhases([
                new Composer(
                    new ComposerFile('composer.json'),
                    new ComposerLockFile('composer.lock')
                ),
                new PublishResources(),
            ]);

            return $packageInstaller;
        });

        $this->app->bind(MergeConfiguration::class, function () {
            $mergeConfiguration = new MergeConfiguration();
            $mergeConfiguration
                ->addMergeHandler(new MergeComposer())
                ->addMergeHandler(new MergeArray(['service_providers', 'aliases']))
                ->addMergeHandler(new MergeScalar(['development']));

            return $mergeConfiguration;
        });

        $this->app->bind(DevelopmentFile::class, function () {
            return new DevelopmentFile(base_path('development.json'));
        });

        $this->registerPackages();
    }

    /**
     * Registers all packages registered with LDK
     */
    protected function registerPackages()
    {
        $packagesConfig = \Config::get('packages');

        if (!empty($packagesConfig)) {
            $serviceProviders = $aliases = [];;

            $dev = $this->app->environment('local');

            foreach ($packagesConfig as $packageConfig) {
                if (true !== $dev && (isset($packageConfig['development']) && true === $packageConfig['development'])) {
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