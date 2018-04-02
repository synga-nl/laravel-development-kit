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
     * @var DevelopmentFile
     */
    protected $developmentFile;

    /**
     * DatabaseSeeder constructor.
     * @param DevelopmentFile $developmentFile
     */
    public function __construct(DevelopmentFile $developmentFile)
    {
        $this->developmentFile = $developmentFile;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     * @throws \Exception
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
        $seeds = $this->developmentFile->get('seeder');

        if (empty($seeds)) {
            throw new \Exception(
                'There are no seeds in the development.json file, make sure this file contains seeds'
            );
        }
        
        return $seeds;
    }
}