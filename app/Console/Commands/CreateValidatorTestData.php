<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CaseReport;
use App\Models\User;
use App\Models\Disease;
use App\Models\Municipality;
use App\Models\Facility;
use Carbon\Carbon;

class CreateValidatorTestData extends Command
{
    protected $signature = 'create:validator-test-data';
    protected $description = 'Create test case reports for validator analytics testing';

    public function handle()
    {
        $this->info('=== CREATING VALIDATOR TEST DATA ===');

        // Find a validator user
        $validator = User::whereHas('roles', function($q) {
            $q->where('name', 'validator');
        })->first();

        if (!$validator) {
            $this->error('No validator user found in the system.');
            return 1;
        }

        $this->info("Found validator: {$validator->name} (ID: {$validator->id})");
        $this->info("Facility ID: {$validator->facility_id}");

        if (!$validator->facility_id) {
            $this->error('Validator has no facility assigned.');
            return 1;
        }

        // Get some diseases and municipality
        $diseases = Disease::take(3)->get();
        $municipality = Municipality::first();

        if ($diseases->isEmpty() || !$municipality) {
            $this->error('Missing diseases or municipality data.');
            return 1;
        }

        $this->info("Creating test cases for facility: {$validator->facility_id}");

        $cases = [];
        $startDate = now()->subDays(30);

        // Create 10 test case reports
        for ($i = 0; $i < 10; $i++) {
            $disease = $diseases->random();
            $onsetDate = $startDate->copy()->addDays(rand(0, 25));

            $case = CaseReport::create([
                'case_id' => 'TEST-VAL-' . str_pad($i + 1, 3, '0', STR_PAD_LEFT),
                'disease_id' => $disease->id,
                'patient_name' => 'Test Patient ' . ($i + 1),
                'age' => rand(1, 80),
                'sex' => rand(0, 1) ? 'Male' : 'Female',
                'date_of_birth' => now()->subYears(rand(1, 80)),
                'address' => 'Test Barangay ' . ($i % 3 + 1),
                'barangay' => 'Test Barangay ' . ($i % 3 + 1),
                'municipality_id' => $municipality->id,
                'date_of_onset' => $onsetDate,
                'date_of_consultation' => $onsetDate->copy()->addDays(rand(1, 3)),
                'date_reported' => $onsetDate->copy()->addDays(rand(1, 2)),
                'case_classification' => ['Suspected', 'Probable', 'Confirmed'][rand(0, 2)],
                'outcome' => ['Alive', 'Died', 'Unknown'][rand(0, 2)],
                'status' => ['submitted', 'validated', 'approved'][rand(0, 2)],
                'reporting_facility_id' => $validator->facility_id,
                'reported_by' => $validator->id,
                'created_at' => $onsetDate->copy()->addDays(1),
                'updated_at' => $onsetDate->copy()->addDays(1),
            ]);

            $cases[] = $case;
            $this->line("Created case: {$case->case_id} - {$disease->name} ({$case->status})");
        }

        $this->info("Successfully created " . count($cases) . " test case reports.");
        $this->info("These cases are assigned to facility ID: {$validator->facility_id}");
        $this->info("Validator can now see analytics data in their dashboard.");

        return 0;
    }
}
