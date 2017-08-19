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

        mkdir($path, 0777, true);

        return $path;
    }
}