<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class PrescriptionCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'prescription:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This gonna pickup the active prescriptions and insert on medices to take';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Log::info("Cron starting...");
        
        Command::FAILURE;
        return Command::SUCCESS;
    }
}
