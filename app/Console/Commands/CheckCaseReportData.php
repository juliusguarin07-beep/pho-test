<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CaseReport;
use App\Models\OutbreakAlert;

class CheckCaseReportData extends Command
{
    protected $signature = 'debug:case-reports-data';
    protected $description = 'Check case report data and matching with outbreak alerts';

    public function handle()
    {
        $this->info('=== CASE REPORTS DATA DEBUG ===');

        $reports = CaseReport::with(['disease', 'municipality', 'barangay'])->get();

        foreach ($reports as $report) {
            $this->line("Case ID: {$report->case_id}");
            $this->line("  Disease: " . ($report->disease?->name ?? 'NULL'));
            $this->line("  Municipality: " . ($report->municipality?->name ?? 'NULL'));
            $this->line("  Barangay: " . ($report->barangay?->name ?? 'NULL'));
            $this->line("  Status: {$report->status}");
            $this->line("  Date Reported: {$report->date_reported}");
            $this->line("  Classification: {$report->case_classification}");
            $this->line("---");
        }

        $this->info("\n=== OUTBREAK ALERTS vs CASE REPORTS ===");

        $alerts = OutbreakAlert::with(['disease', 'municipality'])->where('status', 'published')->get();

        foreach ($alerts as $alert) {
            $this->line("Alert: {$alert->alert_title}");
            $this->line("  Disease: " . ($alert->disease?->name ?? 'NULL'));
            $this->line("  Municipality: " . ($alert->municipality?->name ?? 'NULL'));

            // Count matching case reports
            $matchingCases = CaseReport::where('disease_id', $alert->disease_id)
                ->where('municipality_id', $alert->municipality_id)
                ->whereIn('status', ['submitted', 'validated', 'approved'])
                ->count();

            $this->line("  Matching Case Reports: {$matchingCases}");
            $this->line("---");
        }

        return 0;
    }
}
