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
    public function __construct($path)
    {
        if(!file_exists($path)){
            file_put_contents($path, '{}');
        }

        parent::__construct($path);
    }

    /**
     * @param $key
     * @param $class
     * @return $this
     */
    public function addClassAtKey($keyInFile, $class)
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

    /**
     * @param $key
     * @return mixed
     */
    public function get($key)
    {
        return array_get($this->read(), $key, []);
    }

    /**
     * @param $key
     * @param $data
     */
    public function set($key, $data)
    {
        if (!empty($data)) {
            array_set($this->read(), $key, $data);
        }
    }
}