<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CaseReport;
use App\Models\User;

class DebugEncoderIssue extends Command
{
    protected $signature = 'debug:encoder-issue';
    protected $description = 'Debug encoder data visibility issue';

    public function handle()
    {
        $this->info('=== DEBUGGING ENCODER DATA ISSUE ===');

        // Check total case reports
        $totalReports = CaseReport::count();
        $this->info("Total Case Reports: {$totalReports}");

        // Get all case reports with basic info
        $reports = CaseReport::with(['reporter'])->get();
        $this->info("\n=== ALL CASE REPORTS ===");
        foreach ($reports as $report) {
            $this->line("ID: {$report->id} | Case ID: {$report->case_id} | Status: {$report->status}");
            $this->line("  Reported By: {$report->reported_by} ({$report->reporter?->name})");
            $this->line("  Created: {$report->created_at}");
            $this->line("---");
        }

        // Check encoder users
        $this->info("\n=== ENCODER USERS ===");
        $encoders = User::whereHas('roles', function($q) {
            $q->where('name', 'encoder');
        })->get();

        foreach ($encoders as $encoder) {
            $this->line("User ID: {$encoder->id} | {$encoder->name} ({$encoder->email})");

            // Check their case reports
            $userReports = CaseReport::where('reported_by', $encoder->id)->get();
            $this->line("  Has {$userReports->count()} case reports:");
            foreach ($userReports as $report) {
                $this->line("    - {$report->case_id} ({$report->status})");
            }
        }

        // Check if there are any case reports with null reported_by
        $nullReportedBy = CaseReport::whereNull('reported_by')->count();
        $this->info("\nCase reports with NULL reported_by: {$nullReportedBy}");

        // Check sessions or authentication issues
        $this->info("\n=== POSSIBLE ISSUES ===");
        if ($totalReports > 0 && $encoders->count() > 0) {
            $this->warn("There are case reports and encoder users, but they might not be linked properly.");
            $this->warn("Check if:");
            $this->warn("1. The logged-in user's session is correct");
            $this->warn("2. The reported_by field is properly set when creating reports");
            $this->warn("3. The user has the correct role assigned");
        }

        return 0;
    }
}
