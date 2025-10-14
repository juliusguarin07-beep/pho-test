<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CaseReport;
use App\Models\Disease;
use App\Models\Municipality;
use App\Models\Barangay;
use App\Models\User;
use Carbon\Carbon;

class FillCaseIdGaps extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fill:case-gaps {--count=5 : Number of cases to create}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fill case ID gaps by creating real case reports';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== Filling Case ID Gaps ===');

        // Show current state
        $this->showCurrentState();

        $count = $this->option('count');

        // Get required data
        $disease = Disease::first();
        $municipality = Municipality::first();
        $barangay = Barangay::where('municipality_id', $municipality->id)->first();
        $user = User::first();

        if (!$disease || !$municipality || !$barangay || !$user) {
            $this->error('Missing required data (disease, municipality, barangay, or user)');
            return Command::FAILURE;
        }

        $this->info("\nCreating {$count} case reports to fill gaps...");

        for ($i = 1; $i <= $count; $i++) {
            $this->createTestCase($disease, $municipality, $barangay, $user, $i);
        }

        $this->info("\n=== Final State ===");
        $this->showCurrentState();

        return Command::SUCCESS;
    }

    private function showCurrentState()
    {
        $activeCases = CaseReport::orderBy('case_id')->pluck('case_id')->toArray();
        $deletedCases = CaseReport::onlyTrashed()->orderBy('case_id')->pluck('case_id')->toArray();

        $this->line('Active cases: ' . (empty($activeCases) ? 'None' : implode(', ', $activeCases)));
        $this->line('Deleted cases: ' . (empty($deletedCases) ? 'None' : implode(', ', $deletedCases)));

        if (!empty($activeCases)) {
            $activeNumbers = array_map(function($id) {
                return intval(substr($id, -6));
            }, $activeCases);
            $this->line('Active numbers: ' . implode(', ', $activeNumbers));
        }
    }

    private function createTestCase($disease, $municipality, $barangay, $user, $index)
    {
        $names = [
            ['John', 'Doe'], ['Jane', 'Smith'], ['Mike', 'Johnson'],
            ['Sarah', 'Williams'], ['David', 'Brown'], ['Lisa', 'Davis'],
            ['Tom', 'Wilson'], ['Emma', 'Garcia'], ['Alex', 'Martinez'],
            ['Kate', 'Anderson'], ['Ryan', 'Taylor'], ['Amy', 'Thomas']
        ];

        $name = $names[($index - 1) % count($names)];

        try {
            $caseReport = CaseReport::create([
                'disease_id' => $disease->id,
                'municipality_id' => $municipality->id,
                'barangay_id' => $barangay->id,
                'date_reported' => Carbon::now()->subDays(rand(1, 30))->toDateString(),
                'last_name' => $name[1],
                'first_name' => $name[0],
                'date_of_birth' => Carbon::now()->subYears(rand(1, 80))->toDateString(),
                'age' => rand(1, 80),
                'sex' => rand(0, 1) ? 'Male' : 'Female',
                'address' => "Test Address {$index}",
                'date_of_onset' => Carbon::now()->subDays(rand(1, 14))->toDateString(),
                'date_of_consultation' => Carbon::now()->subDays(rand(1, 7))->toDateString(),
                'case_classification' => 'Confirmed',
                'outcome' => 'Recovered',
                'reporting_facility_id' => 1,
                'reporting_health_worker' => "Dr. {$name[0]} {$name[1]}",
                'health_worker_designation' => 'Physician',
                'reported_by' => $user->id,
                'status' => 'submitted',
            ]);

            $this->line("✓ Created case: {$caseReport->case_id} ({$name[0]} {$name[1]})");

        } catch (\Exception $e) {
            $this->error("✗ Failed to create case {$index}: " . $e->getMessage());
        }
    }
}
