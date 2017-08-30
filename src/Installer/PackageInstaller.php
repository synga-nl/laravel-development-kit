<?php

namespace Synga\LaravelDevelopment\Installer;

use Synga\LaravelDevelopment\Installer\Phase\Phase;

/**
 * Class PackageInstaller
 * @package Synga\LaravelDevelopment\Installer
 */
class PackageInstaller
{
    /**
     * @var Phase[]
     */
    private $phases = [];

    /**
     * Adds a phase, this is called when install method is called.
     *
     * @param Phase $phase
     */
    public function addPhase(Phase $phase)
    {
        array_push($this->phases, $phase);
    }

    /**
     * Adds multiple phases, called when install method is called.
     *
     * @param Phase[] $phases
     */
    public function addPhases($phases)
    {
        foreach ($phases as $phase) {
            if ($phase instanceof Phase) {
                array_push($this->phases, $phase);
            }
        }
    }

    /**
     * Runs every phase added to the object.
     *
     * @param ConfigurationHandler $configuration
     */
    public function install(ConfigurationHandler $configuration)
    {
        foreach ($this->phases as $phase) {
            $phase->handle($configuration);
        }
    }
}