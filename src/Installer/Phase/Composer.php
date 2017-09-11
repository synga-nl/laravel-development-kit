<?php

namespace Synga\LaravelDevelopment\Installer\Phase;

use Synga\LaravelDevelopment\Files\ComposerFile;
use Synga\LaravelDevelopment\Installer\ConfigurationHandler;

/**
 * Class Composer
 * @package Synga\LaravelDevelopment\Installer\Phase
 */
class Composer implements Phase
{
    /**
     * @var ComposerFile
     */
    private $composerFile;

    /**
     * Composer constructor.
     * @param ComposerFile $composerFile
     */
    public function __construct(ComposerFile $composerFile)
    {
        $this->composerFile = $composerFile;
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

        $packages = $configuration->getPackagesByEnvironment();

        $this->requirePackages($packages['require_dev'], true);
        $this->requirePackages($packages['require'], false);
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

            exec($command);
        }
    }
}