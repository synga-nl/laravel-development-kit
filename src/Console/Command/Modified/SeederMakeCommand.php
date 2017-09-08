<?php

namespace Synga\LaravelDevelopment\Console\Command\Modified;

use Synga\LaravelDevelopment\RunCommandTrait;

class SeederMakeCommand extends \Illuminate\Database\Console\Seeds\SeederMakeCommand
{
    use RunCommandTrait, ModifyCommandTrait;

    /**
     * @var string
     */
    private $path = 'Database\Migrations';

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

        $path = base_path('development.json');
        if (!file_exists($path)) {
            file_put_contents($path, '');
        }

        $developmentData = json_decode(file_get_contents($path), true);
        $seeder = array_get($developmentData,'seeder', []);

        $seeder = array_unique($seeder);
        foreach($seeder as $key => $seed){
            if(!class_exists($seed)){
                unset($seeder[$key]);
            }
        }

        $seeder[] = $this->parseName($this->argument('name'));

        array_set($developmentData, 'seeder', array_values($seeder));


        file_put_contents($path, json_encode($developmentData));

        parent::handle();
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