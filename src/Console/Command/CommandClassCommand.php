<?php
namespace Synga\LaravelDevelopment\Console\Command;

use Illuminate\Console\Command;

class CommandClassCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'development:classes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Executes all the commands specified in the development config';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $table = [];
        
        foreach (\Artisan::all() as $command) {
            $table[] = [
                $command->getName(),
                get_class($command)
            ];
        }

        $this->table([], $table, 'compact');
    }
}