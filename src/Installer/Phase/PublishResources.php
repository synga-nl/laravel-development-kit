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
    /**
     * Executes all publish commands specified in the packages.php file.
     *
     * @param ConfigurationHandler $configuration
     */
    public function handle(ConfigurationHandler $configuration)
    {
        foreach ($configuration->getFromAllPackages('publish') as $commandString) {
            $output = new BufferedOutput();
            \Artisan::call('vendor:publish', $this->convertCommandString($commandString), $output);

            echo $output->fetch();
        }
    }

    /**
     * Converts a string into arguments to execute a artisan command.
     *
     * @param $commandString
     * @return array
     */
    protected function convertCommandString($commandString)
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