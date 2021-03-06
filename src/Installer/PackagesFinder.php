<?php

namespace Synga\LaravelDevelopment\Installer;

use Symfony\Component\Finder\Finder;

/**
 * Class PackagesFinder
 * @package Synga\LaravelDevelopment\Packages
 */
class PackagesFinder
{
    /**
     * @param string $directory
     * @return Finder
     */
    public static function getFinder(string $directory = null)
    {
        return Finder::create()->in(self::getDirectory($directory));
    }

    /**
     * @param string|null $directory
     * @return mixed|string
     */
    protected static function getDirectory(string $directory = null)
    {
        if (file_exists($directory)) {
            return $directory;
        }

        $directory = \Config::get('development.packages_directory', null);

        if (empty($directory)) {
            return false;
        }
        
        if (!file_exists($directory)) {
            mkdir($directory, 0776, true);
        }

        return $directory;
    }

    /**
     * @param string|null $directory
     * @return bool
     */
    protected static function packagesDirectoryExists(string $directory = null)
    {
        return false !== self::getDirectory($directory);
    }

    /**
     * Finds all packages in a certain directory
     *
     * @param string|null $directory
     * @return \Symfony\Component\Finder\SplFileInfo[]
     */
    public static function findPackages(string $directory = null)
    {
        $packageNames = [];

        if (self::packagesDirectoryExists($directory)) {
            foreach (self::getFinder($directory)->directories()->depth(1) as $dir) {
                /* @var $dir \Symfony\Component\Finder\SplFileInfo */
                $packageNames[str_replace(self::getDirectory($directory) . DIRECTORY_SEPARATOR, '', $dir->getPathname())] = $dir;
            }
        }

        return $packageNames;
    }

    /**
     * @param string|null $directory
     * @return \Symfony\Component\Finder\SplFileInfo[]
     */
    public static function findComposerFiles(string $directory = null)
    {
        $result = [];

        foreach (self::findPackages($directory) as $package => $directory) {
            foreach (Finder::create()->name('composer.json')->in($directory->getPathname()) as $file) {
                if (!isset($result[$package])) {
                    $result[$package] = [];
                }

                $result[$package][] = $file;
            }
        }

        return $result;
    }
}
