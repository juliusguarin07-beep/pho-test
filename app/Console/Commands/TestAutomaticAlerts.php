<?php

namespace App\Console\Commands;

use App\Models\CaseReport;
use App\Services\AutomaticAlertService;
use Illuminate\Console\Command;

class TestAutomaticAlerts extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'test:automatic-alerts {--clean : Clean existing test data first}';

    /**
     * The console command description.
     */
    protected $description = 'Create test cases and trigger automatic alerts for testing';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('clean')) {
            $this->info('Cleaning existing test data...');
            CaseReport::where('case_id', 'like', 'AUTO%')->delete();
            $this->info('Test data cleaned.');
        }

        $this->info('Creating 35 test cases...');

        for ($i = 1; $i <= 35; $i++) {
            CaseReport::create([
                'case_id' => 'AUTO' . $i,
                'last_name' => 'TestCase',
                'first_name' => 'Auto' . $i,
                'sex' => 'Male',
                'date_of_birth' => '1990-01-01',
                'address' => 'Test Address ' . $i,
                'municipality_id' => 1,
                'barangay_id' => 1,
                'disease_id' => 1,
                'reporting_facility_id' => 1,
                'reporting_health_worker' => 'Test Worker',
                'reported_by' => 1,
                'date_of_onset' => now()->subDays(rand(1, 6))->toDateString(),
                'date_reported' => now()->toDateString(),
                'case_classification' => 'confirmed',
                'outcome' => 'alive',
                'status' => 'confirmed',
                'validator_id' => 1,
                'validated_at' => now()
            ]);
        }

        $this->info('✅ Created 35 test cases for automatic alert testing');

        $this->info('Running automatic alert check...');

        $service = new AutomaticAlertService();
        $service->checkAndCreateAutomaticAlerts();

        $this->info('✅ Automatic alert check completed');

        // Show threshold analysis details
        $this->info('📊 Threshold Analysis Details:');
        $casesByDisease = \App\Models\CaseReport::with('disease:id,name', 'municipality:id,name')
            ->whereDate('date_of_onset', '>=', now()->subWeek())
            ->get()
            ->groupBy(['disease.name', 'municipality.name']);

        foreach ($casesByDisease as $disease => $municipalities) {
            foreach ($municipalities as $municipality => $cases) {
                $caseCount = $cases->count();
                $historicalAvg = max(1, round($caseCount * 0.7));
                $threshold = max(5, $historicalAvg + (2 * sqrt($historicalAvg)));

                $this->line("   {$disease} in {$municipality}: {$caseCount} cases, threshold: {$threshold}");

                if ($caseCount >= $threshold) {
                    $alertLevel = 'yellow';
                    if ($caseCount >= $threshold * 2) {
                        $alertLevel = 'red';
                    } elseif ($caseCount >= $threshold * 1.5) {
                        $alertLevel = 'orange';
                    }
                    $this->warn("     ⚠️  THRESHOLD EXCEEDED - Level: {$alertLevel}");
                }
            }
        }

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
                $this->line("   - {$alert->disease->name} in {$alert->municipality->name} ({$alert->alert_level}) - {$alert->number_of_cases} cases");
            }
        } else {
            $this->warn('No new automatic alerts were created. This might be normal if thresholds weren\'t reached.');
        }

        return 0;
    }
}
