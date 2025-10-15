<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\OutbreakAlert;
use Carbon\Carbon;

class CheckAlertDates extends Command
{
    protected $signature = 'debug:alert-dates';
    protected $description = 'Check outbreak alert dates and filtering';

    public function handle()
    {
        $this->info('=== OUTBREAK ALERT DATES DEBUG ===');
        $this->info('Current date: ' . now()->format('Y-m-d H:i:s'));

        $alerts = OutbreakAlert::all();

        foreach ($alerts as $alert) {
            $this->line("Alert ID: {$alert->id}");
            $this->line("Title: {$alert->alert_title}");
            $this->line("Status: {$alert->status}");
            $this->line("Start Date: " . ($alert->alert_start_date ? $alert->alert_start_date : 'NULL'));
            $this->line("End Date: " . ($alert->alert_end_date ? $alert->alert_end_date : 'NULL'));

            // Check if it matches the API filter
            $isActive = $alert->status === 'published'
                && $alert->alert_start_date <= now()
                && ($alert->alert_end_date === null || $alert->alert_end_date >= now());

            $this->line("Would be shown in API: " . ($isActive ? 'YES' : 'NO'));
            $this->line("---");
        }

        // Test the actual API query
        $this->info('=== TESTING API QUERY ===');
        $apiAlerts = OutbreakAlert::query()
            ->where('status', 'published')
            ->where('alert_start_date', '<=', now())
            ->where(function ($q) {
                $q->whereNull('alert_end_date')
                  ->orWhere('alert_end_date', '>=', now());
            })
            ->get();

        $this->info("API query returned: " . $apiAlerts->count() . " alerts");

        return 0;
    }
}
