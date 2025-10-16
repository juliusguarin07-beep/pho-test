<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Models\OutbreakAlert;

class NotificationService
{
    /**
     * Create notifications for new outbreak alert
     */
    public function createOutbreakAlertNotification(OutbreakAlert $alert)
    {
        // Get all users who should be notified based on their role and location
        $users = collect();

        // PESU Admins get all notifications
        $pesuAdmins = User::where('user_type', 'pesu_admin')
            ->where('is_active', true)
            ->get();
        $users = $users->merge($pesuAdmins);

        // Validators and encoders in the same municipality get notifications
        if ($alert->municipality_id) {
            $localUsers = User::whereIn('user_type', ['validator', 'encoder'])
                ->where('municipality_id', $alert->municipality_id)
                ->where('is_active', true)
                ->get();
            $users = $users->merge($localUsers);
        }

        // Remove duplicates
        $users = $users->unique('id');

        // Create notifications
        foreach ($users as $user) {
            Notification::create([
                'user_id' => $user->id,
                'title' => '🚨 New Outbreak Alert',
                'message' => $this->getNotificationMessage($alert, $user),
                'type' => $this->getNotificationType($alert->alert_level),
                'link' => route('outbreak-alerts.show', $alert->id),
                'is_read' => false,
            ]);
        }
    }

    /**
     * Create notification for case report status change
     */
    public function createCaseReportNotification($caseReport, $action, $user = null)
    {
        $targetUsers = collect();

        // Notify the case reporter
        if ($caseReport->reportedBy) {
            $targetUsers->push($caseReport->reportedBy);
        }

        // Notify facility validators if case is submitted
        if (in_array($action, ['submitted', 'returned'])) {
            $validators = User::where('user_type', 'validator')
                ->where('facility_id', $caseReport->reporting_facility_id)
                ->where('is_active', true)
                ->get();
            $targetUsers = $targetUsers->merge($validators);
        }

        // Notify PESU admins for validated cases
        if ($action === 'validated') {
            $pesuAdmins = User::where('user_type', 'pesu_admin')
                ->where('is_active', true)
                ->get();
            $targetUsers = $targetUsers->merge($pesuAdmins);
        }

        $targetUsers = $targetUsers->unique('id');

        foreach ($targetUsers as $targetUser) {
            Notification::create([
                'user_id' => $targetUser->id,
                'title' => $this->getCaseReportNotificationTitle($action),
                'message' => $this->getCaseReportNotificationMessage($caseReport, $action),
                'type' => $this->getCaseReportNotificationType($action),
                'link' => route('case-reports.show', $caseReport->id),
                'is_read' => false,
            ]);
        }
    }

    /**
     * Get notification message based on alert and user
     */
    private function getNotificationMessage(OutbreakAlert $alert, User $user): string
    {
        $message = $alert->alert_title;

        if ($alert->municipality && $user->municipality_id === $alert->municipality_id) {
            $message .= " - This alert affects your municipality.";
        } elseif ($alert->municipality) {
            $message .= " in " . $alert->municipality->name;
        }

        if ($alert->alert_level === 'Red') {
            $message .= " ⚠️ URGENT: Immediate action required.";
        }

        return $message;
    }

    /**
     * Get notification type based on alert level
     */
    private function getNotificationType(string $alertLevel): string
    {
        return match($alertLevel) {
            'Red' => 'danger',
            'Orange' => 'warning',
            'Yellow' => 'warning',
            'Green' => 'info',
            default => 'info',
        };
    }

    /**
     * Get case report notification title
     */
    private function getCaseReportNotificationTitle(string $action): string
    {
        return match($action) {
            'submitted' => '📝 Case Report Submitted',
            'validated' => '✅ Case Report Validated',
            'approved' => '✅ Case Report Approved',
            'returned' => '↩️ Case Report Returned',
            'rejected' => '❌ Case Report Rejected',
            default => '📄 Case Report Updated',
        };
    }

    /**
     * Get case report notification message
     */
    private function getCaseReportNotificationMessage($caseReport, string $action): string
    {
        $patientName = $caseReport->first_name . ' ' . $caseReport->last_name;
        $disease = $caseReport->disease->name ?? 'Unknown Disease';

        return match($action) {
            'submitted' => "Case report for {$patientName} ({$disease}) has been submitted for validation.",
            'validated' => "Case report for {$patientName} ({$disease}) has been validated.",
            'approved' => "Case report for {$patientName} ({$disease}) has been approved.",
            'returned' => "Case report for {$patientName} ({$disease}) has been returned for revision.",
            'rejected' => "Case report for {$patientName} ({$disease}) has been rejected.",
            default => "Case report for {$patientName} ({$disease}) has been updated.",
        };
    }

    /**
     * Get case report notification type
     */
    private function getCaseReportNotificationType(string $action): string
    {
        return match($action) {
            'validated', 'approved' => 'success',
            'returned', 'rejected' => 'warning',
            'submitted' => 'info',
            default => 'info',
        };
    }
}
