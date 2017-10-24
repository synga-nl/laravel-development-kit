<?php
namespace Synga\LaravelDevelopment\Packages\Configuration\Merge;

interface Configuration
{
    /**
     * @return array|string
     */
    public function getKey();

    /**
     * @param array ...$configurations
     * @return mixed
     */
    public function merge($configurations);
}