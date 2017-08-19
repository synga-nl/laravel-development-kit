<?php

namespace Synga\LaravelDevelopment\Console\Command\Modified;

use Illuminate\Support\Str;

/**
 * Trait ModifyCommandTrait
 * @package Synga\LaravelDevelopment\Console\Command\Modified
 */
trait ModifyCommandTrait
{
    /**
     * @var array
     */
    protected $mandatoryData = [
        'root_namespace' => '',
        'path'           => '',
    ];

    /**
     * @param $data
     */
    public function setData($data)
    {
        foreach ($data as $key => $value) {
            if (isset($this->mandatoryData[$key])) {
                $this->mandatoryData[$key] = $data[$key];
            }
        }
    }

    /**
     * Parse the name and format according to the root namespace.
     *
     * @param  string $name
     *
     * @return string
     */
    private function parseNameTrait($name)
    {
        $rootNamespace = $this->rootNamespace();

        if (Str::startsWith($name, $rootNamespace)) {
            return $name;
        }

        if (Str::contains($name, '/')) {
            $name = str_replace('/', '\\', $name);
        }

        return $this->parseName($this->getDefaultNamespace(trim($rootNamespace, '\\')) . '\\' . $name);
    }

    /**
     * Get the destination class path.
     *
     * @param  string $name
     *
     * @return string
     */
    private function getPathTrait($name)
    {
        $name = str_replace_first($this->rootNamespace(), '', $name);

        return $this->mandatoryData['path'] . '/' . str_replace('\\', '/', $name) . '.php';
    }
}