<?php
namespace Synga\LaravelDevelopment\Console\Command\Modified;

use Synga\LaravelDevelopment\RunCommandTrait;

/**
 * Class ResourceMakeCommand
 * @package Synga\LaravelDevelopment\Console\Command\Modified
 */
class ResourceMakeCommand extends \Illuminate\Foundation\Console\ResourceMakeCommand
{
    use RunCommandTrait, ModifyCommandTrait;

    /**
     * @var string
     */
    private $path = 'Resources';

    /**
     * Execute the console command.
     *
     * @return null|false
     */
    public function fire()
    {
        foreach ($this->mandatoryData as $data) {
            if (empty($data)) {
                return false;
            }
        }

        parent::fire();
    }

    /**
     * Adds file to git after creation
     */
    public function handle(){
        parent::handle();

        $pathName = $this->getPath($this->parseName($this->argument('name')));

        $this->addFileToGit($pathName);
    }

    /**
     * Calls a command and checks if we have an overruled command
     *
     * @param string $command
     * @param array $arguments
     *
     * @return int|void
     */
    public function call($command, array $arguments = [])
    {
        $command = $this->createCommand($command);
        if (method_exists($command, 'setData')) {
            $command->setData($this->mandatoryData);
        }

        $this->runCommand($command, $arguments);
    }

    /**
     * Get the destination class path.
     *
     * @param  string $name
     *
     * @return string
     */
    protected function getPath($name)
    {
        return $this->getPathTrait($name);
    }

    /**
     * Parse the name and format according to the root namespace.
     *
     * @param  string $name
     *
     * @return string
     */
    protected function parseName($name)
    {
        return $this->parseNameTrait($name);
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string $rootNamespace
     *
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\\' . $this->path;
    }

    /**
     * Get the root namespace for the class.
     *
     * @return string
     */
    protected function rootNamespace()
    {
        return $this->mandatoryData['root_namespace'];
    }
}