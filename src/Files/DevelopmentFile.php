<?php

namespace Synga\LaravelDevelopment\Files;

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

    public function get($key)
    {
        return array_get($this->read(), $key, []);
    }

    public function set($key, $data)
    {
        if (!empty($data)) {
            array_set($this->read(), $key, $data);
        }
    }
}