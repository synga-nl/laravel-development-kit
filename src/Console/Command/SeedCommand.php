<?php

namespace Synga\LaravelDevelopment\Console\Command;

use Illuminate\Console\Command;
use React\EventLoop\Factory;
use Synga\InteractiveConsoleTester\Process\ReactProcess;
use Synga\InteractiveConsoleTester\Test\BaseFlowTest;
use Synga\InteractiveConsoleTester\Test\FlowTest;
use Synga\InteractiveConsoleTester\Test\OutputHandler;
use Synga\InteractiveConsoleTester\Tester\Tester;
use Synga\LaravelDevelopment\Tests\ConsoleMakeCommandFlowTest;
use Synga\LaravelDevelopment\Tests\RemovePackageCommandFlowTest;

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
        ConsoleMakeCommandFlowTest::run('php artisan development:command');
        exit('boe');
        $this->call('db:seed', ['--class' => \Synga\LaravelDevelopment\Database\Seeder\DatabaseSeeder::class]);
    }
}