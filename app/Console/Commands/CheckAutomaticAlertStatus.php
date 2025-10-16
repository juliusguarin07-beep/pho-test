<?php

namespace App\Console\Commands;

use App\Models\OutbreakAlert;
use Illuminate\Console\Command;

class CheckAutomaticAlertStatus extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'alerts:check-status';

    /**
     * The console command description.
     */
    protected $description = 'Check status of automatic alerts and publish if needed';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $automaticAlerts = OutbreakAlert::where('is_automatic', true)->get();

        $this->info("Found {$automaticAlerts->count()} automatic alerts");

        foreach ($automaticAlerts as $alert) {
            $this->line("Alert ID: {$alert->id} - {$alert->alert_title}");
            $this->line("  Status: {$alert->status}");
            $this->line("  Active: " . ($alert->is_active ? 'Yes' : 'No'));
            $this->line("  Cases: {$alert->number_of_cases}");

            if ($alert->status === 'draft') {
                $this->warn("  ⚠️  This alert is still in draft status - it won't appear in banners");

                if ($this->confirm("Do you want to publish this alert?")) {
                    $alert->update([
                        'status' => 'published',
                        'published_at' => now(),
                        'is_active' => true,
                    ]);
                    $this->info("  ✅ Alert published successfully!");
                }
            } else {
                $this->info("  ✅ Alert status is good for banner display");
            }
            $this->line("");
        }

        return 0;
    }
}
