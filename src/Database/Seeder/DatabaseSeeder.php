<?php

namespace Synga\LaravelDevelopment\Database\Seeder;

use Illuminate\Database\Seeder;
use Synga\LaravelDevelopment\Files\DevelopmentFile;

/**
 * Class DatabaseSeeder
 * @package Synga\LaravelDevelopment\Database\Seeder
 */
class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $seeds = $this->getSeeds();

        if (is_array($seeds)) {
            foreach ($seeds as $seed) {
                if (@class_exists($seed, true)) {
                    $this->call($seed);
                }
            }
        }
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function getSeeds()
    {
        $path = \Config::get('development.file'), base_path('development.json');

        if (!file_exists($path)) {
            throw new \Exception('There is no development.json file found, make sure this file exists');
        }

        $developmentFile = new DevelopmentFile($path);

        $seeds = $developmentFile->get('seeder');

        if (empty($seeds)) {
            throw new \Exception(
                'There are no seeds in the development.json file, make sure this file contains seeds'
            );
        }
        
        return $seeds;
    }
}