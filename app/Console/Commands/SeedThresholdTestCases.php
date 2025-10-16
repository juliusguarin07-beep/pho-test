<?php

namespace App\Console\Commands;

use App\Models\CaseReport;
use App\Models\Disease;
use App\Models\Municipality;
use App\Services\AutomaticAlertService;
use Illuminate\Console\Command;

class SeedThresholdTestCases extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'seed:threshold-cases
                            {--disease= : Specific disease ID to focus on}
                            {--municipality= : Specific municipality ID to focus on}
                            {--cases=30 : Number of cases to create}
                            {--clean : Clean existing test data first}';

    /**
     * The console command description.
     */
    protected $description = 'Seed realistic test cases to trigger automatic alert thresholds';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('clean')) {
            $this->info('🧹 Cleaning existing test data...');
            CaseReport::where('case_id', 'like', 'SEED%')->delete();
            \App\Models\OutbreakAlert::where('is_automatic', true)->delete();
            $this->info('✅ Test data cleaned.');
        }

        $diseaseId = $this->option('disease') ?? 1; // Default to Dengue
        $municipalityId = $this->option('municipality') ?? 1; // Default to first municipality
        $caseCount = (int) $this->option('cases');

        // Get disease and municipality info
        $disease = Disease::find($diseaseId);
        $municipality = Municipality::find($municipalityId);

        if (!$disease || !$municipality) {
            $this->error('❌ Invalid disease or municipality ID');
            return 1;
        }

        $this->info("🦠 Creating {$caseCount} cases for {$disease->name} in {$municipality->name}...");

        // Create realistic cases with varied dates and demographics
        for ($i = 1; $i <= $caseCount; $i++) {
            $this->createRealisticCase($i, $diseaseId, $municipalityId, $disease->name);
        }

        $this->info("✅ Created {$caseCount} test cases");

        // Calculate threshold before running alert check
        $this->info('📊 Threshold Analysis (Fixed Thresholds):');

        $this->line("   Cases created: {$caseCount}");
        $this->line("   🟨 Yellow threshold: 10 cases");
        $this->line("   🟧 Orange threshold: 25 cases");
        $this->line("   🟥 Red threshold: 50 cases");

        if ($caseCount >= 50) {
            $alertLevel = 'red (SEVERE - Auto-published)';
        } elseif ($caseCount >= 25) {
            $alertLevel = 'orange';
        } elseif ($caseCount >= 10) {
            $alertLevel = 'yellow';
        } else {
            $alertLevel = 'none';
        }

        if ($caseCount >= 10) {
            $this->warn("   🚨 THRESHOLD EXCEEDED - Expected level: {$alertLevel}");
        } else {
            $this->info("   ℹ️  Threshold not exceeded - no alert expected");
        }

        // Run automatic alert check
        $this->info('🔍 Running automatic alert check...');

        $service = new AutomaticAlertService();
        $service->checkAndCreateAutomaticAlerts();

        $this->info('✅ Automatic alert check completed');

        // Check results
        $alertCount = \App\Models\OutbreakAlert::where('is_automatic', true)->count();
        $this->info("📊 Total automatic alerts in system: {$alertCount}");

        $recentAlerts = \App\Models\OutbreakAlert::where('is_automatic', true)
            ->where('created_at', '>=', now()->subMinutes(5))
            ->with(['disease', 'municipality'])
            ->get();

        if ($recentAlerts->count() > 0) {
            $this->info('🚨 Recently created automatic alerts:');
            foreach ($recentAlerts as $alert) {
                $statusIcon = $alert->status === 'published' ? '📢' : '📝';
                $this->line("   {$statusIcon} {$alert->disease->name} in {$alert->municipality->name} ({$alert->alert_level}) - {$alert->number_of_cases} cases - Status: {$alert->status}");
            }
        } else {
            $this->warn('⚠️  No new automatic alerts were created.');
        }

        return 0;
    }

    /**
     * Create a realistic case report
     */
    private function createRealisticCase($index, $diseaseId, $municipalityId, $diseaseName)
    {
        $genders = ['Male', 'Female'];
        $lastNames = ['Santos', 'Cruz', 'Reyes', 'Garcia', 'Mendoza', 'Lopez', 'Gonzales', 'Perez', 'Flores', 'Rivera'];
        $firstNames = ['Juan', 'Maria', 'Jose', 'Ana', 'Pedro', 'Rosa', 'Carlos', 'Elena', 'Miguel', 'Sofia'];

        $gender = $genders[array_rand($genders)];
        $lastName = $lastNames[array_rand($lastNames)];
        $firstName = $firstNames[array_rand($firstNames)];

        // Vary onset dates within the last 7 days (threshold calculation period)
        $daysAgo = rand(1, 6);
        $onsetDate = now()->subDays($daysAgo);

        // Age variation for realism
        $age = rand(5, 80);
        $birthYear = now()->year - $age;

        CaseReport::create([
            'case_id' => 'SEED' . str_pad($index, 3, '0', STR_PAD_LEFT),
            'last_name' => $lastName,
            'first_name' => $firstName,
            'sex' => $gender,
            'date_of_birth' => "{$birthYear}-" . str_pad(rand(1, 12), 2, '0', STR_PAD_LEFT) . "-" . str_pad(rand(1, 28), 2, '0', STR_PAD_LEFT),
            'address' => "Brgy. Sample, Test Street #{$index}",
            'municipality_id' => $municipalityId,
            'barangay_id' => 1, // Use first barangay
            'disease_id' => $diseaseId,
            'reporting_facility_id' => 1,
            'reporting_health_worker' => 'Dr. Test Worker',
            'reported_by' => 1,
            'date_of_onset' => $onsetDate->toDateString(),
            'date_reported' => $onsetDate->addDays(rand(0, 2))->toDateString(), // Report 0-2 days after onset
            'case_classification' => 'confirmed',
            'outcome' => rand(1, 100) > 5 ? 'alive' : 'died', // 5% mortality rate
            'status' => 'confirmed',
            'validator_id' => 1,
            'validated_at' => now()
        ]);
    }
}
