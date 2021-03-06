<?php

namespace Synga\LaravelDevelopment\Installer;

/**
 * Class ConfigurationHandler
 * @package Synga\LaravelDevelopment\Installer
 */
class ConfigurationHandler
{
    /**
     * @var array
     */
    private $configuration;

    /**
     * ConfigurationHandler constructor.
     * @param $config
     */
    public function __construct($config)
    {
        $this->configuration = $config;
    }

    /**
     * Gets values based on the key for all packages.
     *
     * @param $key
     * @return array
     */
    public function getFromAllPackages($key)
    {
        $result = [];

        foreach ($this->configuration as $config) {
            $result = array_merge_recursive($result, array_get($config, $key, []));
        }

        return $result;
    }

    /**
     * Get all packages and their environment (development or production).
     *
     * @return array
     */
    public function getPackagesByEnvironment($existingPackages = [])
    {
        $packages = [
            'production' => (isset($existingPackages['production'])) ? $existingPackages['production'] : [],
            'development' => (isset($existingPackages['development'])) ? $existingPackages['development'] : []
        ];

        foreach ($this->configuration as $name => $package) {
            if (false === strpos($name, '/')) {
                continue;
            }

            $type = (isset($package['development']) && true === $package['development']) ? 'development' : 'production';

            if (isset($package['composer'], $package['composer']['version'])) {
                $packages[$type][] = $name . ':' . $package['composer']['version'];

                continue;
            }

            $packages[$type][] = $name;
        }

        return $packages;
    }
}