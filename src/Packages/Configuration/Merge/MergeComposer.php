<?php
namespace Synga\LaravelDevelopment\Packages\Configuration\Merge;

/**
 * Class MergeComposer
 * @package Synga\LaravelDevelopment\Packages\Configuration\Merge
 */
class MergeComposer implements Configuration
{
    /**
     * @var array
     */
    protected $pickLast = ['version'];

    /**
     * @return string
     */
    public function getKey()
    {
        return 'composer';
    }

    /**
     * @param array ...$configurations
     * @return array
     */
    public function merge($configurations)
    {
        $result = [];
        $mergeResult = call_user_func_array('array_merge_recursive', $configurations);

        foreach ($mergeResult as $key => $pickLast) {
            if (is_array($pickLast) && in_array($key, $this->pickLast)) {
                $result[$key] = end($pickLast);

                continue;
            }

            $result[$key] = $pickLast;
        }

        return $result;
    }
}