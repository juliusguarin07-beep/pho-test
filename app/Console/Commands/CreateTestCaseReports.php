<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CaseReport;
use App\Models\Disease;
use App\Models\Municipality;
use App\Models\Barangay;

class CreateTestCaseReports extends Command
{
    protected $signature = 'create:test-cases';
    protected $description = 'Create additional test case reports for demonstration';

    public function handle()
    {
        $this->info('=== CREATING TEST CASE REPORTS ===');

        // Get IDs
        $leptospirosis = Disease::where('name', 'Leptospirosis')->first();
        $chickenPox = Disease::where('name', 'Chicken Pox')->first();
        $binmaley = Municipality::where('name', 'Binmaley')->first();
        $lingayen = Municipality::where('name', 'Lingayen')->first();
        $encoderUser = \App\Models\User::where('email', 'encoder@dcho.gov.ph')->first();

        if (!$leptospirosis || !$chickenPox || !$binmaley || !$lingayen || !$encoderUser) {
            $this->error('Required data not found!');
            return 1;
        }

        // Create 2 more Leptospirosis cases in Binmaley
        for ($i = 1; $i <= 2; $i++) {
            CaseReport::create([
                'case_id' => 'CASE-2025-' . str_pad(4 + $i, 6, '0', STR_PAD_LEFT),
                'disease_id' => $leptospirosis->id,
                'date_reported' => now(),
                'case_classification' => 'Suspect',
                'outcome' => 'Alive',
                'last_name' => 'TestPatient',
                'first_name' => 'Leptospirosis' . $i,
                'sex' => 'Male',
                'date_of_birth' => now()->subYears(25),
                'age' => 25,
                'address' => 'Test Address ' . $i,
                'barangay_id' => Barangay::where('municipality_id', $binmaley->id)->first()->id,
                'municipality_id' => $binmaley->id,
                'nationality' => 'Filipino',
                'date_of_onset' => now()->subDays(3),
                'clinical_outcome' => 'Alive',
                'reporting_facility_id' => 1,
                'reporting_health_worker' => 'Test Health Worker',
                'health_worker_designation' => 'Nurse',
                'reported_by' => $encoderUser->id,
                'status' => 'approved'
            ]);
        }

        // Create 2 more Chicken Pox cases in Lingayen
        for ($i = 1; $i <= 2; $i++) {
            CaseReport::create([
                'case_id' => 'CASE-2025-' . str_pad(6 + $i, 6, '0', STR_PAD_LEFT),
                'disease_id' => $chickenPox->id,
                'date_reported' => now(),
                'case_classification' => 'Confirmed',
                'outcome' => 'Alive',
                'last_name' => 'TestChild',
                'first_name' => 'ChickenPox' . $i,
                'sex' => 'Female',
                'date_of_birth' => now()->subYears(8),
                'age' => 8,
                'address' => 'School District ' . $i,
                'barangay_id' => Barangay::where('municipality_id', $lingayen->id)->first()->id,
                'municipality_id' => $lingayen->id,
                'nationality' => 'Filipino',
                'date_of_onset' => now()->subDays(5),
                'clinical_outcome' => 'Alive',
                'reporting_facility_id' => 1,
                'reporting_health_worker' => 'School Nurse',
                'health_worker_designation' => 'Nurse',
                'reported_by' => $encoderUser->id,
                'status' => 'approved'
            ]);
        }

        $this->info('Test case reports created successfully!');
        $this->line('- Added 2 Leptospirosis cases in Binmaley (total should be 2)');
        $this->line('- Added 2 Chicken Pox cases in Lingayen (total should be 3)');

        return 0;
    }
}
