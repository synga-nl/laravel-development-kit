<?php

namespace Synga\LaravelDevelopment\Console\Command\Modified;

use Synga\LaravelDevelopment\RunCommandTrait;

/**
 * Class ControllerMakeCommand
 * @package Synga\LaravelDevelopment\Console\Command\Modified
 */
class ControllerMakeCommand extends \Illuminate\Routing\Console\ControllerMakeCommand
{
    use RunCommandTrait, ModifyCommandTrait;

    /**
     * @var string
     */
    private $path = 'Http\Controllers';

    /**
     * @var string
     */
    private $baseController = '<?php

namespace {{ namespace }};

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
';

    /**
     * Adds file to git after creation
     */
    public function handle(){
        parent::handle();

        $pathName = $this->getPath($this->parseName($this->argument('name')));

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
     * Builds class and creates the base controller when needed
     *
     * @param string $name
     * @return string
     */
    protected function buildClass($name)
    {
        $result = parent::buildClass($name);

        $namespace = $this->getDefaultNamespace($this->rootNamespace(false));
        $path = $this->getPathTrait($namespace . '\Controller');

        if (!file_exists($path)) {
            file_put_contents($path, str_replace('{{ namespace }}', $namespace, $this->baseController));
        }

        $this->addFileToGit($path);

        return $result;
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
    protected function rootNamespace($withTrailingSlash = true)
    {
        return ($this->mandatoryData['root_namespace'] . ((true === $withTrailingSlash) ? '\\' : ''));
    }
}