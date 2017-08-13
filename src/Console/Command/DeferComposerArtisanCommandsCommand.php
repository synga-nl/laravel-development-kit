<?php

namespace Synga\LaravelDevelopment\Console\Command;

use Illuminate\Console\Command;

class DeferComposerArtisanCommandsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'development:commands {type}';

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
        $type = $this->argument('type');

        $packages = \Config::get('development.packages');

        foreach ($packages as $package) {
            if (isset($package['composer'][$type . '_commands']['artisan'])) {
                foreach ($package['composer'][$type . '_commands']['artisan'] as $command) {
                    if (isset(\Artisan::all()[$command])) {
                        $this->call($command);
                    }
                }
            }
        }
    }
}