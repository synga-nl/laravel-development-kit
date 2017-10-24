<?php
namespace Synga\LaravelDevelopment\Packages\Configuration\Merge;

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
     * @return mixed
     */
    public function getKey()
    {
        return $this->key;
    }
}