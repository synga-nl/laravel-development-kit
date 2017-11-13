<?php

namespace Synga\LaravelDevelopment\Console\Command\Modified;

use Illuminate\Support\Str;
use Synga\LaravelDevelopment\Files\DevelopmentFile;

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
        'path' => '',
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

    /**
     * Asks which stub you want to use defined in the development.json file.
     *
     * @return string
     */
    public function getStubTrait()
    {
        $developmentFile = new DevelopmentFile(base_path('development.json'));

        $reflectionClass = new \ReflectionClass($this);
        $name = strtolower(str_replace('MakeCommand', '', $reflectionClass->getShortName()));

        $stubs = $developmentFile->get('stubs.' . $name);

        $choices = ['default'];
        foreach ($stubs as $stub) {
            if (file_exists($stub)) {
                $choices[] = $stub;
            }
        }

        if (1 === count($choices)) {
            $result = 'default';
        }

        if (!isset($result)) {
            $result = $this->choice('Which stub do you want to use?', $choices, 0, 3);
        }

        if ('default' === $result) {
            return parent::getStub();
        }

        return $result;
    }

    /**
     * Adds file to git.
     *
     * @param string $pathName
     */
    protected function addFileToGit(string $pathName)
    {
        if (file_exists(base_path('.git')) && file_exists($pathName)) {
            exec(sprintf('cd %s && git add %s', escapeshellarg(base_path()), escapeshellarg($pathName)));
        }
    }
}