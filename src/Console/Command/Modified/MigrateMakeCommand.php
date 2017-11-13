<?php

namespace Synga\LaravelDevelopment\Console\Command\Modified;

use Synga\LaravelDevelopment\RunCommandTrait;

/**
 * Class MigrateMakeCommand
 * @package Synga\LaravelDevelopment\Console\Command\Modified
 */
class MigrateMakeCommand extends \Illuminate\Database\Console\Migrations\MigrateMakeCommand
{
    use RunCommandTrait, ModifyCommandTrait;

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

        $pathName = $this->getMigrationPath();

        $this->addFileToGit($pathName);
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
     * Get migration path (either specified by '--path' option or default location).
     *
     * @return string
     */
    protected function getMigrationPath()
    {
        if (!is_null($targetPath = $this->input->getOption('path'))) {
            return $this->laravel->basePath() . '/' . $targetPath;
        }

        $path = implode(DIRECTORY_SEPARATOR, [$this->mandatoryData['path'], 'Database', 'Migrations']);

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        $this->addFileToGit($path);

        return $path;
    }
}