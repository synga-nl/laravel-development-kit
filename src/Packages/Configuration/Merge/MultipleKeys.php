<?php
namespace Synga\LaravelDevelopment\Packages\Configuration\Merge;

/**
 * Trait MultipleKeys
 * @package Synga\LaravelDevelopment\Packages\Configuration\Merge
 */
trait MultipleKeys
{
    /** @var */
    private $key;

    /**
     * MergeRecursive constructor.
     * @param $key
     */
    function __construct($key)
    {
        $this->key = $key;
    }

    /**
     * @return array
     */
    public function getKey()
    {
        return $this->key;
    }
}