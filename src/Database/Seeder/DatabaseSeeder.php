<?php

namespace Synga\LaravelDevelopment\Database\Seeder;

use Illuminate\Database\Seeder;
use Synga\LaravelDevelopment\Files\DevelopmentFile;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $path = base_path('development.json');
        if (file_exists($path)) {
            $developmentFile = new DevelopmentFile($path);
            $seeds = $developmentFile->get('seeder');
            if (is_array($seeds)) {
                foreach ($seeds as $seed) {
                    if (@class_exists($seed, true)) {
                        $this->call($seed);
                    }
                }
            }
        }
    }
}