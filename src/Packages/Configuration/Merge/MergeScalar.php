<?php
namespace Synga\LaravelDevelopment\Packages\Configuration\Merge;

class MergeScalar implements Configuration
{
    use MultipleKeys;

    /**
     * @param array ...$configurations
     * @return mixed
     */
    public function merge($configurations)
    {
        return end($configurations);
    }
}