<?php

namespace Synga\LaravelDevelopment\Console\Command\Modified;

use Illuminate\Filesystem\Filesystem;
use Synga\LaravelDevelopment\Files\DevelopmentFile;
use Synga\LaravelDevelopment\RunCommandTrait;

/**
 * Class ConsoleMakeCommand
 * @package Synga\LaravelDevelopment\Console\Command\Modified
 */
class ConsoleMakeCommand extends \Illuminate\Foundation\Console\ConsoleMakeCommand
{
    use RunCommandTrait, ModifyCommandTrait;

    /**
     * @var string
     */
    private $path = 'Console\Command';

    /**
     * @var DevelopmentFile
     */
    private $developmentFile;

    /**
     * ConsoleMakeCommand constructor.
     * @param Filesystem $files
     * @param DevelopmentFile $developmentFile
     */
    public function __construct(Filesystem $files, DevelopmentFile $developmentFile)
    {
        parent::__construct($files);

        $this->developmentFile = $developmentFile;
    }

    /**
     * Execute the console command.
     *
     * @return null|false
     */
    public function handle()
    {
        foreach ($this->mandatoryData as $data) {
            if (empty($data)) {
                return false;
            }
        }

        $className = $this->parseName($this->argument('name'));

        $this->developmentFile
            ->addClassAtKey('command', $className)
            ->write();

        parent::handle();

        $this->addFileToGit($this->getPath($className));
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    public function getStub()
    {
        return $this->getStubTrait();
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