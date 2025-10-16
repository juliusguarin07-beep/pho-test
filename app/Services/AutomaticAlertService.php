<?php

namespace App\Services;

use App\Models\OutbreakAlert;
use App\Models\CaseReport;
use App\Models\Disease;
use App\Models\Municipality;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AutomaticAlertService
{
    /**
     * Check thresholds and create automatic alerts if needed
     */
    public function checkAndCreateAutomaticAlerts()
    {
        try {
            // Get threshold analysis data (same logic as AnalyticsController)
            $thresholdAnalysis = $this->getThresholdAnalysis();

            foreach ($thresholdAnalysis as $analysis) {
                // Only create alerts for yellow, orange, or red levels
                if (in_array($analysis['alert_level'], ['yellow', 'orange', 'red'])) {
                    $this->createAutomaticAlert($analysis);
                }
            }

            Log::info('Automatic alert check completed successfully');
        } catch (\Exception $e) {
            Log::error('Error in automatic alert creation: ' . $e->getMessage());
        }
    }

    /**
     * Get threshold analysis (same logic as AnalyticsController)
     */
    private function getThresholdAnalysis()
    {
        return CaseReport::select(
                'disease_id',
                'municipality_id',
                DB::raw('count(*) as current_cases')
            )
            ->with(['disease:id,name', 'municipality:id,name'])
            ->whereDate('date_of_onset', '>=', now()->subWeek())
            ->groupBy(['disease_id', 'municipality_id'])
            ->get()
            ->map(function ($item) {
                // Fixed threshold system: 10+ yellow, 25+ orange, 50+ red
                $yellowThreshold = 10;
                $orangeThreshold = 25;
                $redThreshold = 50;

                $alertLevel = 'normal';
                $threshold = $yellowThreshold; // Base threshold for calculations

                if ($item->current_cases >= $redThreshold) {
                    $alertLevel = 'red';
                    $threshold = $redThreshold;
                } elseif ($item->current_cases >= $orangeThreshold) {
                    $alertLevel = 'orange';
                    $threshold = $orangeThreshold;
                } elseif ($item->current_cases >= $yellowThreshold) {
                    $alertLevel = 'yellow';
                    $threshold = $yellowThreshold;
                }

                return [
                    'disease_id' => $item->disease_id,
                    'municipality_id' => $item->municipality_id,
                    'disease' => $item->disease->name ?? 'Unknown',
                    'municipality' => $item->municipality->name ?? 'Unknown',
                    'current_cases' => $item->current_cases,
                    'threshold' => $threshold,
                    'alert_level' => $alertLevel,
                    'percentage_of_threshold' => round(($item->current_cases / $threshold) * 100),
                ];
            })
            ->filter(function ($item) {
                return in_array($item['alert_level'], ['yellow', 'orange', 'red']);
            });
    }

    /**
     * Create automatic alert for threshold breach
     */
    private function createAutomaticAlert($analysis)
    {
        // Check if an automatic alert already exists for this disease/municipality combination
        $existingAlert = OutbreakAlert::where('disease_id', $analysis['disease_id'])
            ->where('municipality_id', $analysis['municipality_id'])
            ->where('is_automatic', true)
            ->where('status', 'draft')
            ->where('created_at', '>=', now()->subDays(1)) // Check within last 24 hours
            ->first();

        if ($existingAlert) {
            // Update existing alert with latest data
            $existingAlert->update([
                'number_of_cases' => $analysis['current_cases'],
                'case_count' => $analysis['current_cases'],
                'threshold_reached' => $analysis['threshold'],
                'alert_level' => $this->mapAlertLevel($analysis['alert_level']),
            ]);
            return $existingAlert;
        }

        // Determine if alert should be auto-published (severe alerts are auto-published)
        $shouldAutoPublish = $analysis['alert_level'] === 'red';

        // Create new automatic alert
        $alert = OutbreakAlert::create([
            'disease_id' => $analysis['disease_id'],
            'municipality_id' => $analysis['municipality_id'],
            'alert_title' => $this->generateAlertTitle($analysis),
            'alert_details' => $this->generateAlertDetails($analysis),
            'alert_level' => $this->mapAlertLevel($analysis['alert_level']),
            'number_of_cases' => $analysis['current_cases'],
            'case_count' => $analysis['current_cases'],
            'threshold_reached' => $analysis['threshold'],
            'alert_start_date' => now()->toDateString(),
            'health_advisory' => $this->generateHealthAdvisory($analysis),
            'preventive_measures' => $this->generatePreventiveMeasures($analysis),
            'dos_and_donts' => $this->generateDosAndDonts($analysis),
            'emergency_contacts' => $this->getEmergencyContacts(),
            'status' => $shouldAutoPublish ? 'published' : 'draft', // Auto-publish severe alerts
            'published_at' => $shouldAutoPublish ? now() : null,
            'is_automatic' => true,
            'is_active' => $shouldAutoPublish, // Activate if auto-published
            'created_by' => 1, // System user
        ]);

        $logMessage = "Automatic alert created for {$analysis['disease']} in {$analysis['municipality']}";
        if ($shouldAutoPublish) {
            $logMessage .= " (AUTO-PUBLISHED - Severe level)";
        }

        Log::info($logMessage, [
            'alert_id' => $alert->id,
            'cases' => $analysis['current_cases'],
            'threshold' => $analysis['threshold'],
            'level' => $analysis['alert_level'],
            'auto_published' => $shouldAutoPublish
        ]);

        return $alert;
    }

    /**
     * Map color-based alert levels to system levels
     */
    private function mapAlertLevel($colorLevel)
    {
        return match($colorLevel) {
            'yellow' => OutbreakAlert::LEVEL_MODERATE,
            'orange' => OutbreakAlert::LEVEL_HIGH,
            'red' => OutbreakAlert::LEVEL_SEVERE,
            default => OutbreakAlert::LEVEL_MODERATE
        };
    }

    /**
     * Generate alert title
     */
    private function generateAlertTitle($analysis)
    {
        $levelText = match($analysis['alert_level']) {
            'yellow' => 'Moderate Risk Alert',
            'orange' => 'High Risk Alert',
            'red' => 'Severe Outbreak Alert',
            default => 'Health Alert'
        };

        return "{$levelText}: {$analysis['disease']} in {$analysis['municipality']}";
    }

    /**
     * Generate alert details
     */
    private function generateAlertDetails($analysis)
    {
        return "AUTOMATIC ALERT GENERATED\n\n" .
               "The surveillance system has detected a significant increase in {$analysis['disease']} cases " .
               "in {$analysis['municipality']} municipality.\n\n" .
               "Current Status:\n" .
               "• Cases reported (last 7 days): {$analysis['current_cases']}\n" .
               "• Threshold level: {$analysis['threshold']}\n" .
               "• Percentage of threshold: {$analysis['percentage_of_threshold']}%\n" .
               "• Alert Level: " . strtoupper($analysis['alert_level']) . "\n\n" .
               "This alert was automatically generated based on epidemiological thresholds. " .
               "Please review and take appropriate action.";
    }

    /**
     * Generate health advisory
     */
    private function generateHealthAdvisory($analysis)
    {
        $advisories = [
            'yellow' => "Enhanced surveillance is recommended. Monitor the situation closely and ensure proper case reporting.",
            'orange' => "Immediate investigation required. Implement enhanced control measures and contact tracing.",
            'red' => "Urgent outbreak response needed. Activate emergency protocols and implement comprehensive control measures."
        ];

        return $advisories[$analysis['alert_level']] ?? $advisories['yellow'];
    }

    /**
     * Generate preventive measures
     */
    private function generatePreventiveMeasures($analysis)
    {
        return "• Strengthen surveillance activities\n" .
               "• Enhance case investigation and contact tracing\n" .
               "• Implement infection prevention and control measures\n" .
               "• Increase community awareness and education\n" .
               "• Coordinate with healthcare facilities\n" .
               "• Monitor high-risk populations\n" .
               "• Ensure adequate medical supplies and resources";
    }

    /**
     * Generate dos and don'ts
     */
    private function generateDosAndDonts($analysis)
    {
        return "DO:\n" .
               "• Report suspected cases immediately\n" .
               "• Follow infection control protocols\n" .
               "• Seek medical attention for symptoms\n" .
               "• Practice good hygiene\n" .
               "• Follow health authority guidelines\n\n" .
               "DON'T:\n" .
               "• Ignore symptoms or delay medical care\n" .
               "• Spread unverified information\n" .
               "• Neglect preventive measures\n" .
               "• Self-medicate without medical advice";
    }

    /**
     * Get emergency contacts
     */
    private function getEmergencyContacts()
    {
        return "Provincial Health Office: (02) 123-4567\n" .
               "Emergency Hotline: 911\n" .
               "Disease Surveillance Unit: (02) 123-4568\n" .
               "Municipal Health Office: Contact your local MHO";
    }
}
