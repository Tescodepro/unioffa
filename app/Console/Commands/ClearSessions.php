<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ClearSessions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clear-sessions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
   public function handle()
{
    $path = storage_path('framework/sessions');

    if (is_dir($path)) {
        foreach (glob("$path/*") as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        $this->info('All session files cleared successfully.');
    } else {
        $this->error('Session directory not found.');
    }
}

}
