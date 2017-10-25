<?php
namespace Synga\LaravelDevelopment\Packages\Configuration\Merge;

interface ConfigurationMerger
{
    /**
     * Gets the key(s) which the ConfigurationMerger is designed for.
     *
     * @return array|string
     */
    public function getKey();

    /**
     * Merge the configurations.
     *
     * @param array $configurations
     * @return mixed
     */
    public function merge($configurations);
}