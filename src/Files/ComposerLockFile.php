<?php

namespace Synga\LaravelDevelopment\Files;

class ComposerLockFile extends File
{
    public function isPackageInFile($packageName)
    {
        $fileContents = $this->read();
        $packages = array_merge($fileContents['packages'], $fileContents['packages-dev']);

        foreach ($packages as $package) {
            if (
                $packageName == $package ||
                array_key_exists($packageName, (isset($package['require'])) ? $package['require'] : []) ||
                array_key_exists($packageName, (isset($package['require-dev'])) ? $package['require-dev'] : [])
            ) {
                return true;
            }
        }

        return false;
    }
}