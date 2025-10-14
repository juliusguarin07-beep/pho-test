<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\CaseReport;

class CheckUserFacilityMapping extends Command
{
    protected $signature = 'debug:user-facility';
    protected $description = 'Check user-facility mapping for validators';

    public function handle()
    {
        $this->info('=== USER-FACILITY MAPPING DEBUG ===');

        // Get all users and their roles
        $users = User::with('roles')->get();

        foreach ($users as $user) {
            $roles = $user->roles->pluck('name')->toArray();
            $this->line("User: {$user->name} ({$user->email})");
            $this->line("  ID: {$user->id}");
            $this->line("  Roles: " . implode(', ', $roles));
            $this->line("  Facility ID: " . ($user->facility_id ?? 'NULL'));

            if ($user->hasRole('validator')) {
                $this->line("  [VALIDATOR] Should see reports from facility: {$user->facility_id}");

                // Check what reports they should see
                if ($user->facility_id) {
                    $validatorReports = CaseReport::where('reporting_facility_id', $user->facility_id)
                        ->whereIn('status', ['submitted', 'validated', 'returned', 'approved'])
                        ->get(['case_id', 'status', 'reporting_facility_id']);

                    $this->line("  Can see {$validatorReports->count()} reports:");
                    foreach ($validatorReports as $report) {
                        $this->line("    - {$report->case_id} ({$report->status}) from facility {$report->reporting_facility_id}");
                    }
                } else {
                    $this->error("  [ERROR] Validator has no facility_id assigned!");
                }
            }

            if ($user->hasRole('encoder')) {
                $encoderReports = CaseReport::where('reported_by', $user->id)->get(['case_id', 'status']);
                $this->line("  [ENCODER] Can see {$encoderReports->count()} reports:");
                foreach ($encoderReports as $report) {
                    $this->line("    - {$report->case_id} ({$report->status})");
                }
            }

            $this->line("---");
        }

        // Check the existing case report
        $this->info("\n=== EXISTING CASE REPORT DETAILS ===");
        $report = CaseReport::with(['reportingFacility'])->first();
        if ($report) {
            $this->line("Case ID: {$report->case_id}");
            $this->line("Status: {$report->status}");
            $this->line("Reported By: {$report->reported_by}");
            $this->line("Reporting Facility ID: {$report->reporting_facility_id}");
            $this->line("Reporting Facility: " . ($report->reportingFacility?->name ?? 'Unknown'));
        }

        return 0;
    }
}
