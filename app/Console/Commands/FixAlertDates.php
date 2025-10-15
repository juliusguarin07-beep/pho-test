<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\OutbreakAlert;
use Carbon\Carbon;

class FixAlertDates extends Command
{
    protected $signature = 'fix:alert-dates';
    protected $description = 'Fix outbreak alert dates to make them active';

    public function handle()
    {
        $this->info('=== FIXING OUTBREAK ALERT DATES ===');

        $alerts = OutbreakAlert::where('status', 'published')->get();

        foreach ($alerts as $alert) {
            $this->line("Fixing Alert ID: {$alert->id} - {$alert->alert_title}");

            // Set end date to 7 days from now or null for ongoing alerts
            $alert->alert_end_date = now()->addDays(7);

            // Ensure start date is today or earlier
            if ($alert->alert_start_date > now()) {
                $alert->alert_start_date = now();
            }

            $alert->save();

            $this->line("Updated - Start: {$alert->alert_start_date}, End: {$alert->alert_end_date}");
        }

        $this->info('=== TESTING UPDATED ALERTS ===');

        // Test the API query again
        $apiAlerts = OutbreakAlert::query()
            ->where('status', 'published')
            ->where('alert_start_date', '<=', now())
            ->where(function ($q) {
                $q->whereNull('alert_end_date')
                  ->orWhere('alert_end_date', '>=', now());
            })
            ->get();

        $this->info("API query now returns: " . $apiAlerts->count() . " alerts");

        return 0;
    }
}
