<?php

namespace Synga\LaravelDevelopment\Console\Command;

use Illuminate\Console\Command;

class SeedCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'development:seed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed development seeders';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->call('db:seed', ['--class' => \Synga\LaravelDevelopment\Database\Seeder\DatabaseSeeder::class]);
    }
}