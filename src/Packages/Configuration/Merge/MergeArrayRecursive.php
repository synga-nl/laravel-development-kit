<?php
namespace Synga\LaravelDevelopment\Packages\Configuration\Merge;

class MergeArrayRecursive implements Configuration
{
    use MultipleKeys;

    /**
     * @param array ...$configurations
     * @return array
     */
    public function merge($configurations)
    {
        return call_user_func_array('array_merge_recursive', $configurations);
    }
}