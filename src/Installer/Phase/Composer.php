<?php

namespace Synga\LaravelDevelopment\Installer\Phase;

use Illuminate\Support\Collection;
use Synga\LaravelDevelopment\Console\ConfirmShellCommand;
use Synga\LaravelDevelopment\Files\ComposerFile;
use Synga\LaravelDevelopment\Files\ComposerLockFile;
use Synga\LaravelDevelopment\Installer\ConfigurationHandler;
use Synga\LaravelDevelopment\Installer\PackagesFinder;

/**
 * Class Composer
 * @package Synga\LaravelDevelopment\Installer\Phase
 */
class Composer implements Phase
{
    /** @var bool */
    private $skipApproval = false;

    /** @var ComposerFile */
    private $composerFile;

    /** @var ComposerLockFile */
    private $composerLockFile;

    /**
     * Composer constructor.
     * @param ComposerFile $composerFile
     * @param ComposerLockFile $composerLockFile
     */
    public function __construct(ComposerFile $composerFile, ComposerLockFile $composerLockFile)
    {
        $this->composerFile = $composerFile;
        $this->composerLockFile = $composerLockFile;
    }

    /**
     * Handle the composer phase.
     *
     * @param ConfigurationHandler $configuration
     * @return void
     */
    public function handle(ConfigurationHandler $configuration)
    {
        $this->removeFromArrayRecursive('scripts', 'artisan development:commands');

        foreach ($configuration->getFromAllPackages('composer.commands') as $event => $package) {
            $this->composerFile->addCommand(
                'php artisan development:commands ' . $event,
                $event,
                'Illuminate\\Foundation\\ComposerScripts::postUpdate'
            );
        }

        $this->composerFile->write();

        $packages = $this->getPackagesFromComposerFiles();

        $packages = $configuration->getPackagesByEnvironment($packages);

        $this->requirePackages($packages['production'], false);
        $this->requirePackages($packages['development'], true);
    }

    public function skipApproval()
    {
        $this->skipApproval = true;
    }

    /**
     * Filters composer packages from the composer.json file in laravel packages
     *
     * @return array
     */
    protected function getPackagesFromComposerFiles()
    {
        $callbackIsInLockFile = function ($package) {
            return !$this->composerLockFile->isPackageInFile($package['name']);
        };

        $calbackCombineNameAndVersion = function ($item) {
            return $item['name'] . ':' . $item['version'];
        };

        $production = new Collection();
        $development = new Collection();

        foreach (PackagesFinder::findComposerFiles() as $packageName => $composerFiles) {
            foreach ($composerFiles as $composerFile) {
                /* @var $composerFile \Symfony\Component\Finder\SplFileInfo */
                $composerFile = new ComposerFile($composerFile->getPathname());

                $production = $production
                    ->merge((new Collection($composerFile->getPackages(true, false)))->filter($callbackIsInLockFile));
                $development = $development
                    ->merge((new Collection($composerFile->getPackages(false, true)))->filter($callbackIsInLockFile));
            }
        }

        $production = $production->unique('name')->map($calbackCombineNameAndVersion)->toArray();
        $development = $development->unique('name')->map($calbackCombineNameAndVersion)->toArray();

        return ['production' => $production, 'development' => $development];
    }

    /**
     * Remove command from the commands list in the composer file.
     *
     * @param $key
     * @param $partialCommand
     */
    protected function removeFromArrayRecursive($key, $partialCommand)
    {
        $values = array_get($this->composerFile->read(), $key, null);

        if (is_array($values)) {
            foreach ($values as &$value) {
                foreach ($value as $position => $command) {
                    if (strpos($command, $partialCommand)) {
                        unset($value[$position]);
                        $value = array_values($value);
                    }
                }
            }
        }

        if (!empty($values)) {
            array_set($this->composerFile->read(), $key, $values);
        }
    }

    /**
     * Require packages with the "composer require" command
     *
     * @param $packages
     * @param bool $development
     */
    private function requirePackages($packages, $development = true)
    {
        if (!empty($packages)) {
            $command = 'composer require ';

            if (true === $development) {
                $command .= '--dev ';
            }

            foreach ($packages as $package) {
                $command .= escapeshellarg($package) . ' ';
            }

            ConfirmShellCommand::exec($command, $this->skipApproval);
        }
    }
}