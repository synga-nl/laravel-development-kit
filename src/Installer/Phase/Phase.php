<?php
namespace Synga\LaravelDevelopment\Installer\Phase;

use Synga\LaravelDevelopment\Installer\ConfigurationHandler;

/**
 * Interface Phase
 * @package Synga\LaravelDevelopment\Installer\Phase
 */
interface Phase
{
    /**
     * Handle the current phase based on the configuration.
     *
     * @param ConfigurationHandler $configuration
     * @return mixed
     */
    public function handle(ConfigurationHandler $configuration);

    /**
     * Use no approval for a shell command for this phase.
     *
     * @return void
     */
    public function skipApproval();
}