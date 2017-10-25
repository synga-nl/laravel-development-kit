<?php
namespace Synga\LaravelDevelopment\Packages\Configuration\Merge;

class MergeArray implements ConfigurationMerger
{
    use MultipleKeys;

    /**
     * @param array ...$configurations
     * @return array
     */
    public function merge($configurations)
    {
        return array_unique(call_user_func_array('array_merge', $configurations));
    }
}