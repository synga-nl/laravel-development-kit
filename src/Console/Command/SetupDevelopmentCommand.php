<?php

namespace Synga\LaravelDevelopment\Console\Command;

use Illuminate\Console\Command;

/**
 * Class SetupDevelopmentCommand
 * @package Synga\LaravelDevelopment\Console\Command
 */
class SetupDevelopmentCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'development:setup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup laravel for development';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $packages = $this->getPackages($this->getConfig());

        foreach ($packages as $key => $foundPackages) {
            $dev = false;

            if (false !== strpos($key, '_dev')) {
                $dev = true;
            }

            $this->executeRequireCommand($foundPackages, $dev);
        }

        $composerFile = file_get_contents('composer.json');
        $findInstall = "\"Illuminate\\\\Foundation\\\\ComposerScripts::postInstall\",\n";
        $replaceInstall = "            \"php artisan development:commands install\",\n";

        $findUpdate = "\"Illuminate\\\\Foundation\\\\ComposerScripts::postUpdate\",\n";
        $replaceUpdate = "            \"php artisan development:commands update\",\n";

        $composerFile = $this->insertAt($composerFile, $findInstall, $replaceInstall);
        $composerFile = $this->insertAt($composerFile, $findUpdate, $replaceUpdate);
        file_put_contents('composer.json', $composerFile);

        exec('composer install --dev -q');
    }

    private function insertAt($text, $find, $insert, $check = true)
    {
        if (true === $check && false !== strpos($text, $find . $insert)) {
            return $text;
        }

        return str_replace($find, $find . $insert, $text);
    }

    private function executeRequireCommand($packages, $dev = true)
    {
        if (!empty($packages)) {
            $command = 'composer require ';

            if (true === $dev) {
                $command .= '--dev ';
            }

            foreach ($packages as $package) {
                $command .= escapeshellarg($package) . ' ';
            }

            exec($command);
        }
    }

    /**
     * @return mixed
     */
    private function getConfig()
    {
        $developmentConfigFile = config_path('development.php');

        if (!file_exists($developmentConfigFile)) {
            $this->call('vendor:publish',
                ['--provider' => 'Synga\LaravelDevelopment\LaravelDevelopmentServiceProvider']);
            $developmentConfig = include_once config_path('development.php');
            return $developmentConfig;
        } else {
            $developmentConfig = \Config::get('development');
            return $developmentConfig;
        }
    }

    /**
     * @param $config
     *
     * @return array
     */
    protected function getPackages($config)
    {
        $packages = ['require' => [], 'require_dev' => []];

        foreach ($config['packages'] as $package) {
            if (isset($package['dev']) && true === $package['dev']) {
                $type = 'require_dev';
            } else {
                $type = 'require';
            }

            $packages[$type][] = $package['composer']['name'];
        }
        return $packages;
    }
}