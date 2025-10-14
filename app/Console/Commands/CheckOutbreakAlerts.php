<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\OutbreakAlert;

class CheckOutbreakAlerts extends Command
{
    protected $signature = 'debug:outbreak-alerts';
    protected $description = 'Check outbreak alert data';

    public function handle()
    {
        $this->info('=== OUTBREAK ALERTS DEBUG ===');

        $count = OutbreakAlert::count();
        $this->info("Total outbreak alerts: {$count}");

        if ($count > 0) {
            $alerts = OutbreakAlert::with(['disease', 'municipality'])->get();
            foreach ($alerts as $alert) {
                $this->line("ID: {$alert->id}");
                $this->line("Title: {$alert->alert_title}");
                $this->line("Details: " . substr($alert->alert_details ?? 'NULL', 0, 100));
                $this->line("Health Advisory: " . substr($alert->health_advisory ?? 'NULL', 0, 100));
                $this->line("Disease: " . ($alert->disease?->name ?? 'NULL'));
                $this->line("Municipality: " . ($alert->municipality?->name ?? 'NULL'));
                $this->line("Status: {$alert->status}");
                $this->line("---");
            }
        } else {
            $this->warn("No outbreak alerts found. Need to create sample data.");
        }

        return 0;
    }
}
