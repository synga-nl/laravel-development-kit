<?php

namespace Synga\LaravelDevelopment\Files;

/**
 * Class DevelopmentFile
 * @package Synga\LaravelDevelopment\Files
 */
class DevelopmentFile extends File
{
    /**
     * ComposerFile constructor.
     * @param $path
     */
    public function __construct($path = null)
    {
        if (!file_exists($path)) {
            file_put_contents($path, json_encode([
                'stubs' => [
                    'seeder' => [
                        'vendor/synga/laravel-development-kit/src/Console/Command/Stubs/seeder.stub'
                    ],
                    'controller' => [
                        'vendor/synga/laravel-development-kit/src/Console/Command/Stubs/controller.api.stub'
                    ]
                ],
                'api_trait' => \Synga\LaravelDevelopment\Common\ApiTrait::class
            ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        }

        parent::__construct($path);
    }

    /**
     * @param string $keyInFile
     * @param string $class
     * @return $this
     */
    public function addClassAtKey(string $keyInFile, string $class)
    {
        $seeder = $this->get($keyInFile);

        if (is_array($seeder)) {
            foreach ($seeder as $key => $seed) {
                if (!class_exists($seed)) {
                    unset($seeder[$key]);
                }
            }
        }

        $seeder[] = $class;
        $seeder = array_unique($seeder);

        $this->set($keyInFile, array_values($seeder));

        return $this;
    }
}