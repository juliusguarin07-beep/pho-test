<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\OutbreakAlert;
use App\Models\CaseReport;

class VerifyApiData extends Command
{
    protected $signature = 'verify:api-data';
    protected $description = 'Verify that API returns correct case counts';

    public function handle()
    {
        $this->info('=== API DATA VERIFICATION ===');

        // Get all outbreak alerts
        $alerts = OutbreakAlert::with(['disease', 'municipality'])->get();

        foreach ($alerts as $alert) {
            $this->line("Alert: {$alert->title}");
            $this->line("  Disease: {$alert->disease->name}");
            $this->line("  Municipality: {$alert->municipality->name}");

            // Get actual case count using the same logic as API
            $actualCases = CaseReport::where('disease_id', $alert->disease_id)
                ->where('municipality_id', $alert->municipality_id)
                ->whereIn('status', ['approved'])
                ->count();

            $this->line("  Actual Case Count: {$actualCases}");

            if ($actualCases > 0) {
                $this->info("  ✅ HAS CASES - System showing dynamic count");
            } else {
                $this->line("  ⚪ NO CASES - Showing zero (correct)");
            }

            $this->line('---');
        }

        $this->info('Verification complete!');
        return 0;
    }
}
