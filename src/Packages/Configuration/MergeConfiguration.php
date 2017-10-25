<?php

namespace Synga\LaravelDevelopment\Packages\Configuration;

use Synga\LaravelDevelopment\Packages\Configuration\Merge\ConfigurationMerger;

/**
 * Class Merge
 * @package Synga\LaravelDevelopment\Packages\Configuration
 */
class MergeConfiguration
{
    /** @var ConfigurationMerger[] */
    private $parts;

    /**
     * Adds an ConfigurationMerger object to $this->parts.
     *
     * @param ConfigurationMerger $part
     * @return $this
     */
    public function addMergeHandler(ConfigurationMerger $part)
    {
        $this->parts[] = $part;

        return $this;
    }

    /**
     * Merges the configurations following by a pattern defined in the ConfigurationMerger objects.
     *
     * @param array $configurations
     * @return array
     */
    public function merge($configurations)
    {
        $keysToObject = $this->getKeysToObject();

        $skip = $this->getSkippedPackages($configurations);

        $result = $this->mergeConfiguration($configurations, $keysToObject, $skip);

        return $result;
    }

    /**
     * Because an ConfigurationMerger object can be used for multiple configuration keys a array is created like:
     * [$key => *ConfigurationMerger*].
     *
     * @return ConfigurationMerger[]
     */
    private function getKeysToObject()
    {
        $keysToObject = [];

        foreach ($this->parts as $part) {
            $key = $part->getKey();
            if (is_array($key)) {
                foreach ($key as $key2) {
                    $keysToObject[$key2] = $part;
                }

                continue;
            }

            $keysToObject[$key] = $part;
        }

        return $keysToObject;
    }

    /**
     * If an package in the configuration has an empty array, it should be removed from the configuration.
     *
     * @param $configurations
     * @return string[]
     */
    private function getSkippedPackages($configurations)
    {
        $skip = [];
        $lastConfiguration = end($configurations);

        foreach ($lastConfiguration as $packageName => $configuration) {
            if (empty($configuration)) {
                $skip[] = $packageName;
            }
        }

        return $skip;
    }

    /**
     * Before the merge the array needs to be prepared. Every configuration key should have its version so we can pass
     * it to the ConfigurationMerger objects.
     *
     * @param $configurations
     * @param $keysToObject
     * @param $skip
     * @return array
     */
    private function prepareConfigurationForMerge($configurations, $keysToObject, $skip)
    {
        $configurationsWithKey = [];

        foreach ($keysToObject as $key => $part) {
            foreach ($configurations as $configuration) {
                foreach ($configuration as $packageName => $packageConfiguration) {
                    if (in_array($packageName, $skip)) {
                        continue;
                    }

                    if (isset($packageConfiguration[$key])) {
                        $configurationsWithKey = array_add($configurationsWithKey, $packageName . '.' . $key, []);

                        $configurationsWithKey[$packageName][$key][] = $packageConfiguration[$key];
                    }
                }
            }
        }

        return $configurationsWithKey;
    }

    /**
     * Merge the configurations.
     *
     * @param $configurations
     * @param ConfigurationMerger[] $keysToObject
     * @param $skip
     * @return array
     */
    private function mergeConfiguration($configurations, $keysToObject, $skip)
    {
        $result = [];

        $preparedConfiguration = $this->prepareConfigurationForMerge($configurations, $keysToObject, $skip);

        foreach ($preparedConfiguration as $packageName => $packageConfiguration) {
            foreach ($packageConfiguration as $keyName => $item) {
                if (isset($keysToObject[$keyName]) && !empty($item)) {
                    $result = array_add($result, $packageName . '.' . $keyName, []);

                    $result[$packageName][$keyName] = $keysToObject[$keyName]->merge($item);
                }
            }
        }
        return $result;
    }
}