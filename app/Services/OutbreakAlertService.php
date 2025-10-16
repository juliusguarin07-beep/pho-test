<?php

namespace App\Services;

use App\Models\OutbreakAlert;
use App\Models\CaseReport;
use App\Models\Disease;
use App\Models\Municipality;
use Carbon\Carbon;

class OutbreakAlertService
{
    /**
     * Check and generate automatic alerts based on case thresholds
     */
    public function checkAndGenerateAlerts($diseaseId, $municipalityId)
    {
        // Count active cases for this disease and municipality in the last 30 days
        $caseCount = CaseReport::where('disease_id', $diseaseId)
            ->where('municipality_id', $municipalityId)
            ->where('status', '!=', 'draft')
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->count();

        $alertLevel = OutbreakAlert::getAlertLevel($caseCount);

        if ($alertLevel) {
            // Check if there's already an active alert for this disease and municipality
            $existingAlert = OutbreakAlert::where('disease_id', $diseaseId)
                ->where('municipality_id', $municipalityId)
                ->where('is_automatic', true)
                ->where('is_active', true)
                ->first();

            if ($existingAlert) {
                // Update existing alert if case count or level changed
                if ($existingAlert->case_count != $caseCount || $existingAlert->alert_level != $alertLevel) {
                    $this->updateAlert($existingAlert, $caseCount, $alertLevel);
                }
            } else {
                // Create new automatic alert
                $this->createAlert($diseaseId, $municipalityId, $caseCount, $alertLevel);
            }
        }
    }

    /**
     * Create a new automatic alert
     */
    private function createAlert($diseaseId, $municipalityId, $caseCount, $alertLevel)
    {
        $disease = Disease::find($diseaseId);
        $municipality = Municipality::find($municipalityId);

        $threshold = $this->getThresholdForLevel($alertLevel);
        $description = OutbreakAlert::getAlertDescription($alertLevel);
        $icon = OutbreakAlert::getAlertIcon($alertLevel);

        $alert = OutbreakAlert::create([
            'disease_id' => $diseaseId,
            'municipality_id' => $municipalityId,
            'alert_title' => "{$icon} Automatic Alert: {$disease->name} in {$municipality->name}",
            'alert_details' => "Automatic alert generated due to {$caseCount} cases of {$disease->name} reported in {$municipality->name} within the last 30 days. {$description}",
            'alert_level' => ucfirst($alertLevel),
            'case_count' => $caseCount,
            'threshold_reached' => $threshold,
            'number_of_cases' => $caseCount,
            'alert_start_date' => now()->toDateString(),
            'health_advisory' => $this->generateHealthAdvisory($disease->name, $alertLevel),
            'preventive_measures' => $this->generatePreventiveMeasures($disease->name),
            'emergency_contacts' => $this->generateEmergencyContacts(),
            'status' => 'published',
            'is_automatic' => true,
            'is_active' => true,
            'created_by' => 1, // System user
            'published_at' => now(),
        ]);

        // Create notifications for automatic alerts
        $notificationService = new \App\Services\NotificationService();
        $notificationService->createOutbreakAlertNotification($alert);
    }

    /**
     * Update existing alert
     */
    private function updateAlert($alert, $caseCount, $alertLevel)
    {
        $threshold = $this->getThresholdForLevel($alertLevel);
        $description = OutbreakAlert::getAlertDescription($alertLevel);
        $icon = OutbreakAlert::getAlertIcon($alertLevel);

        $disease = $alert->disease;
        $municipality = $alert->municipality;

        $alert->update([
            'alert_title' => "{$icon} Automatic Alert: {$disease->name} in {$municipality->name}",
            'alert_details' => "Automatic alert updated due to {$caseCount} cases of {$disease->name} reported in {$municipality->name} within the last 30 days. {$description}",
            'alert_level' => ucfirst($alertLevel),
            'case_count' => $caseCount,
            'threshold_reached' => $threshold,
            'number_of_cases' => $caseCount,
            'health_advisory' => $this->generateHealthAdvisory($disease->name, $alertLevel),
        ]);
    }

    /**
     * Get threshold value for alert level
     */
    private function getThresholdForLevel($level)
    {
        return match($level) {
            'moderate' => OutbreakAlert::THRESHOLD_MODERATE,
            'high' => OutbreakAlert::THRESHOLD_HIGH,
            'severe' => OutbreakAlert::THRESHOLD_SEVERE,
            default => 0
        };
    }

    /**
     * Generate health advisory based on disease and alert level
     */
    private function generateHealthAdvisory($diseaseName, $alertLevel)
    {
        $baseAdvisory = "Health authorities are monitoring the situation closely. ";

        return match($alertLevel) {
            'moderate' => $baseAdvisory . "Residents are advised to maintain good hygiene practices and seek medical attention if symptoms develop.",
            'high' => $baseAdvisory . "Enhanced surveillance measures are in effect. Immediate medical consultation is recommended for anyone showing symptoms.",
            'severe' => $baseAdvisory . "Emergency response protocols are activated. All suspected cases must be reported immediately to health authorities.",
            default => $baseAdvisory
        };
    }

    /**
     * Generate preventive measures
     */
    private function generatePreventiveMeasures($diseaseName)
    {
        return "• Practice proper hand hygiene\n• Maintain clean environment\n• Avoid crowded areas if possible\n• Seek immediate medical attention for symptoms\n• Follow local health guidelines";
    }

    /**
     * Generate emergency contacts
     */
    private function generateEmergencyContacts()
    {
        return "Provincial Health Office: (075) 542-4216\nEmergency Hotline: 911\nDOH Hotline: 1555";
    }

    /**
     * Get active alerts for dashboard
     */
    public function getActiveAlerts($userRole = null, $municipalityId = null)
    {
        $query = OutbreakAlert::with(['disease', 'municipality'])
            ->where('is_active', true)
            ->where('status', 'published')
            ->orderBy('alert_level', 'desc')
            ->orderBy('case_count', 'desc');

        // Filter by municipality for validators and encoders
        if ($municipalityId && in_array($userRole, ['validator', 'encoder'])) {
            $query->where('municipality_id', $municipalityId);
        }

        return $query->get();
    }

    /**
     * Resolve an alert
     */
    public function resolveAlert($alertId, $userId)
    {
        $alert = OutbreakAlert::find($alertId);
        if ($alert) {
            $alert->update([
                'is_active' => false,
                'resolved_at' => now(),
                'resolved_by' => $userId,
            ]);
        }
    }

    /**
     * Get all current automatic alerts for dashboard display
     * Shows both draft and published automatic alerts for internal users
     */
    public function getCurrentAutomaticAlerts()
    {
        $alerts = OutbreakAlert::where('is_automatic', true)
            ->whereIn('status', ['draft', 'published']) // Show both draft and published for internal users
            ->with(['disease', 'municipality'])
            ->get();

        // Filter alerts to ensure they still meet the threshold based on actual case counts
        $validAlerts = $alerts->filter(function ($alert) {
            // For draft alerts, use the stored case count and don't auto-deactivate
            if ($alert->status === 'draft') {
                return true; // Always show draft alerts to internal users
            }

            // For published alerts, validate against current case counts
            $actualCaseCount = CaseReport::where('disease_id', $alert->disease_id)
                ->where('municipality_id', $alert->municipality_id)
                ->where('status', '!=', 'draft')
                ->where('created_at', '>=', Carbon::now()->subDays(30))
                ->count();

            // Check if the alert is still valid based on actual case count
            $alertLevel = OutbreakAlert::getAlertLevel($actualCaseCount);

            // If no alert level is needed anymore, mark this alert as resolved
            if (!$alertLevel) {
                $alert->update([
                    'is_active' => false,
                    'resolved_at' => now(),
                ]);
                return false;
            }

            // Update the alert with actual case count if it differs
            if ($alert->case_count != $actualCaseCount) {
                $alert->update([
                    'case_count' => $actualCaseCount,
                    'alert_level' => ucfirst($alertLevel),
                ]);
            }

            return true;
        });

        return $validAlerts->sortByDesc('case_count')->values();
    }

    /**
     * Get only published automatic alerts for public consumption (residents)
     */
    public function getPublishedAutomaticAlerts()
    {
        $alerts = OutbreakAlert::where('is_automatic', true)
            ->where('is_active', true)
            ->where('status', 'published') // Only published for residents
            ->with(['disease', 'municipality'])
            ->get();

        // Filter alerts to ensure they still meet the threshold based on actual case counts
        $validAlerts = $alerts->filter(function ($alert) {
            // Recount actual cases for this alert
            $actualCaseCount = CaseReport::where('disease_id', $alert->disease_id)
                ->where('municipality_id', $alert->municipality_id)
                ->where('status', '!=', 'draft')
                ->where('created_at', '>=', Carbon::now()->subDays(30))
                ->count();

            // Check if the alert is still valid based on actual case count
            $alertLevel = OutbreakAlert::getAlertLevel($actualCaseCount);

            // If no alert level is needed anymore, mark this alert as resolved
            if (!$alertLevel) {
                $alert->update([
                    'is_active' => false,
                    'resolved_at' => now(),
                ]);
                return false;
            }

            // Update the alert with actual case count if it differs
            if ($alert->case_count != $actualCaseCount) {
                $alert->update([
                    'case_count' => $actualCaseCount,
                    'alert_level' => ucfirst($alertLevel),
                ]);
            }

            return true;
        });

        return $validAlerts->sortByDesc('case_count')->values();
    }

    /**
     * Clean up automatic alerts that no longer meet thresholds
     */
    public function cleanupInvalidAlerts()
    {
        $alerts = OutbreakAlert::where('is_automatic', true)
            ->where('is_active', true)
            ->get();

        foreach ($alerts as $alert) {
            // Recount actual cases for this alert
            $actualCaseCount = CaseReport::where('disease_id', $alert->disease_id)
                ->where('municipality_id', $alert->municipality_id)
                ->where('status', '!=', 'draft')
                ->where('created_at', '>=', Carbon::now()->subDays(30))
                ->count();

            // Check if the alert should still be active
            $alertLevel = OutbreakAlert::getAlertLevel($actualCaseCount);

            if (!$alertLevel) {
                // No longer meets threshold, resolve the alert
                $alert->update([
                    'is_active' => false,
                    'resolved_at' => now(),
                ]);
            } else {
                // Update with actual case count
                $alert->update([
                    'case_count' => $actualCaseCount,
                    'alert_level' => ucfirst($alertLevel),
                ]);
            }
        }
    }
}
