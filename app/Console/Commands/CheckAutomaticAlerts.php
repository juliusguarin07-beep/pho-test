<?php

namespace App\Console\Commands;

use App\Services\AutomaticAlertService;
use Illuminate\Console\Command;

class CheckAutomaticAlerts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alerts:check-automatic
                            {--force : Force check even if recently checked}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check disease thresholds and create automatic alerts when needed';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting automatic alert check...');

        try {
            $service = new AutomaticAlertService();
            $service->checkAndCreateAutomaticAlerts();

            $this->info('✅ Automatic alert check completed successfully');
        } catch (\Exception $e) {
            $this->error('❌ Error during automatic alert check: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
