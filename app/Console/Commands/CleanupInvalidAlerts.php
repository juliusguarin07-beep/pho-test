<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\OutbreakAlertService;

class CleanupInvalidAlerts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alerts:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up automatic alerts that no longer meet case thresholds';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting automatic alert cleanup...');

        $alertService = new OutbreakAlertService();
        $alertService->cleanupInvalidAlerts();

        $this->info('Alert cleanup completed successfully.');

        return Command::SUCCESS;
    }
}
