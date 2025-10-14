<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\CaseReport;

class DebugValidatorCases extends Command
{
    protected $signature = 'debug:validator-cases';
    protected $description = 'Debug why validator cannot see case reports';

    public function handle()
    {
        $this->info('=== VALIDATOR CASE VISIBILITY DEBUG ===');

        // Get validator user
        $validator = User::where('email', 'validator@rimc.gov.ph')->with('facility')->first();

        if (!$validator) {
            $this->error('Validator user not found!');
            return 1;
        }

        $this->line("Validator: {$validator->name}");
        $this->line("Validator Facility ID: {$validator->facility_id}");
        $this->line("Validator Facility: " . ($validator->facility ? $validator->facility->name : 'NULL'));
        $this->line('');

        // Get all case reports
        $reports = CaseReport::with(['reportingFacility', 'disease', 'municipality', 'reportedBy'])
            ->latest()
            ->take(5)
            ->get();

        $this->info('RECENT CASE REPORTS:');
        foreach ($reports as $report) {
            $facilityName = $report->reportingFacility ? $report->reportingFacility->name : 'Unknown';
            $reporterName = $report->reportedBy ? $report->reportedBy->name : 'Unknown';
            $matches = ($report->reporting_facility_id == $validator->facility_id) ? '✅ MATCH' : '❌ NO MATCH';

            $this->line("Case: {$report->case_id}");
            $this->line("  Created by: {$reporterName}");
            $this->line("  Reporting Facility ID: {$report->reporting_facility_id} ({$facilityName})");
            $this->line("  Status: {$report->status}");
            $this->line("  Disease: " . ($report->disease ? $report->disease->name : 'Unknown'));
            $this->line("  Visibility: {$matches}");
            $this->line('');
        }        // Check what the validator should see
        $validatorReports = CaseReport::where('reporting_facility_id', $validator->facility_id)
            ->where('status', '!=', 'draft')
            ->count();

        $this->info("Cases validator should see: {$validatorReports}");

        return 0;
    }
}
