<?php

namespace Synga\LaravelDevelopment\Installer\Phase;

use Illuminate\Support\Collection;
use Synga\LaravelDevelopment\Console\ApproveExecCommand;
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

        $this->getPackagesFromComposerFiles();
        exit();

        $packages = $configuration->getPackagesByEnvironment();

        $this->requirePackages($packages['production'], true);
        $this->requirePackages($packages['development'], false);
    }

    protected function getPackagesFromComposerFiles()
    {
        $result = ['production' => [], 'development' => []];

        foreach (PackagesFinder::findComposerFiles() as $packageName => $composerFiles) {
            foreach ($composerFiles as $composerFile) {
                /* @var $composerFile \Symfony\Component\Finder\SplFileInfo */
                $composerFile = new ComposerFile($composerFile->getPathname());

                $result['production'] = new Collection($composerFile->getPackages(true, false));
                $result['development'] = new Collection($composerFile->getPackages(false, true));

                var_dump($result['production']->filter(function($item){
                    return $this->isInComposerLockFile($item['name']);
                }));

//                dd($result['development']->pluck('name')->each(function($index, $item){
//                    return $this->isInComposerLockFile($item);
//                }));
            }
        }

        return $result;
    }

    protected function isInComposerLockFile($packageName)
    {
        return $this->composerLockFile->hasPackageInFile($packageName);
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
     * @param bool $dev
     */
    private function requirePackages($packages, $dev = true)
    {
        if (!empty($packages)) {
            $command = 'composer require ';

            if (true === $dev) {
                $command .= '--dev ';
            }

            foreach ($packages as $package) {
                $command .= escapeshellarg($package) . ' ';
            }

            ApproveExecCommand::exec($command);
        }
    }
}