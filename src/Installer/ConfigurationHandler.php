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

        foreach ($this->configuration['packages'] as $config) {
            $result = array_merge_recursive($result, array_get($config, $key, []));
        }

        return $result;
    }

    /**
     * Get all packages and their environment (development or production).
     *
     * @return array
     */
    public function getPackagesByEnvironment()
    {
        $packages = ['require' => [], 'require_dev' => []];

        foreach ($this->configuration['packages'] as $package) {
            if (isset($package['dev']) && true === $package['dev']) {
                $type = 'require_dev';
            } else {
                $type = 'require';
            }

            $packages[$type][] = $package['composer']['name'];
        }
        return $packages;
    }
}