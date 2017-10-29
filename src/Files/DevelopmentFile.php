<?php

namespace Synga\LaravelDevelopment\Files;

/**
 * Class DevelopmentFile
 * @package Synga\LaravelDevelopment\Files
 */
class DevelopmentFile extends File
{
    /**
     * ComposerFile constructor.
     * @param $path
     */
    public function __construct($path = null)
    {
        if(!file_exists($path)){
            file_put_contents($path, '{}');
        }

        parent::__construct($path);
    }

    /**
     * @param string $keyInFile
     * @param string $class
     * @return $this
     */
    public function addClassAtKey(string $keyInFile, string $class)
    {
        $seeder = $this->get($keyInFile);

        if (is_array($seeder)) {
            foreach ($seeder as $key => $seed) {
                if (!class_exists($seed)) {
                    unset($seeder[$key]);
                }
            }
        }

        $seeder[] = $class;
        $seeder = array_unique($seeder);

        $this->set($keyInFile, array_values($seeder));

        return $this;
    }
}