<?php
namespace Synga\LaravelDevelopment\Packages\Configuration;

use Synga\LaravelDevelopment\Packages\Configuration\Merge\Configuration;

/**
 * Class Merge
 * @package Synga\LaravelDevelopment\Packages\Configuration
 */
class Merge
{
    /**
     * @var Configuration[]
     */
    private $parts;

    /**
     * @param Configuration $part
     * @return $this
     */
    public function addMergeHandler(Configuration $part)
    {
        $this->parts[] = $part;

        return $this;
    }

    /**
     * @param array ...$configurations
     * @return array
     */
    public function merge($configurations)
    {
        /* @todo Please make this code a lot less ugly;) Nobody understands this... seriously why did I you create this? */
        $result = [];
        $skip = [];

        $keysToObject = $this->getKeysToObject();
        $configurationsWithKey = [];

        $lastConfiguration = end($configurations);

        foreach ($lastConfiguration as $packageName => $configuration) {
            if (empty($configuration)) {
                $skip[] = $packageName;
            }
        }

        foreach ($keysToObject as $key => $part) {
            foreach ($configurations as $configuration) {
                foreach ($configuration as $packageName => $packageConfiguration) {
                    if(in_array($packageName, $skip)){
                        continue;
                    }

                    if (isset($packageConfiguration[$key])) {
                        if (!isset($configurationsWithKey[$packageName])) {
                            $configurationsWithKey[$packageName] = [];
                        }

                        if (!isset($configurationsWithKey[$packageName][$key])) {
                            $configurationsWithKey[$packageName][$key] = [];
                        }

                        $configurationsWithKey[$packageName][$key][] = $packageConfiguration[$key];
                    }
                }
            }
        }

        foreach ($configurationsWithKey as $packageName => $packageConfiguration) {
            foreach ($packageConfiguration as $keyName => $item) {
                if (isset($keysToObject[$keyName]) && !empty($item)) {
                    if (!isset($configurationsWithKey[$packageName])) {
                        $configurationsWithKey[$packageName] = [];
                    }

                    if (!isset($configurationsWithKey[$packageName][$keyName])) {
                        $configurationsWithKey[$packageName][$keyName] = [];
                    }

                    $result[$packageName][$keyName] = $keysToObject[$keyName]->merge($item);
                }
            }
        }

        return $result;
    }

    /**
     * @return array
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
}