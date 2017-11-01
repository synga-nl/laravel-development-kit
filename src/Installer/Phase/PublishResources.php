<?php
namespace Synga\LaravelDevelopment\Installer\Phase;

use Symfony\Component\Console\Output\BufferedOutput;
use Synga\LaravelDevelopment\Installer\ConfigurationHandler;

/**
 * Class PublishResources
 * @package Synga\LaravelDevelopment\Installer\Phase
 */
class PublishResources implements Phase
{
    /** @var bool */
    protected $skipApproval = false;

    /**
     * Executes all publish commands specified in the packages.php file.
     *
     * @param ConfigurationHandler $configuration
     * @return void
     */
    public function handle(ConfigurationHandler $configuration)
    {
        foreach ($configuration->getFromAllPackages('publish') as $commandString) {
            $output = new BufferedOutput();
            \Artisan::call('vendor:publish', $this->convertCommandString($commandString), $output);

            echo $output->fetch();
        }
    }

    public function skipApproval()
    {
        $this->skipApproval = true;
    }

    /**
     * Converts a string into arguments to execute a artisan command.
     *
     * @param string $commandString
     * @return array
     */
    protected function convertCommandString(string $commandString)
    {
        $arguments = [];

        foreach (explode(' ', $commandString) as $argument) {
            if (1 === substr_count($argument, '=')) {
                list($argument, $value) = explode('=', $argument);
                $arguments[$argument] = trim($value, "\"'");
            }
        }

        return $arguments;
    }
}