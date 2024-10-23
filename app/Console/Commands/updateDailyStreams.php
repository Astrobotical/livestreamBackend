<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class updateDailyStreams extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-daily-streams';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command should turn all streams for that day to inactive';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Scheduled task ran successfully at ' . now());
    }
}
