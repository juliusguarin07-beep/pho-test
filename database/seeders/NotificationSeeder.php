<?php

namespace Database\Seeders;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        // Get some users to create notifications for
        $users = User::where('is_active', true)->get();

        if ($users->isEmpty()) {
            return;
        }

        // Sample notifications for demonstration
        $notifications = [
            [
                'title' => '🚨 New Outbreak Alert',
                'message' => 'Dengue outbreak alert has been issued for Baguio City. 15 cases reported in the last 7 days.',
                'type' => 'danger',
                'link' => '/outbreak-alerts/1',
            ],
            [
                'title' => '📝 Case Report Submitted',
                'message' => 'A new COVID-19 case report has been submitted for validation.',
                'type' => 'info',
                'link' => '/case-reports/1',
            ],
            [
                'title' => '✅ Case Report Validated',
                'message' => 'Case report for John Doe (Typhoid) has been successfully validated.',
                'type' => 'success',
                'link' => '/case-reports/2',
            ],
            [
                'title' => '⚠️ Moderate Outbreak Alert',
                'message' => 'Influenza cases are increasing in La Trinidad. Monitor closely.',
                'type' => 'warning',
                'link' => '/outbreak-alerts/2',
            ],
            [
                'title' => '↩️ Case Report Returned',
                'message' => 'Case report for Maria Santos has been returned for additional information.',
                'type' => 'warning',
                'link' => '/case-reports/3',
            ],
        ];

        foreach ($users as $user) {
            // Create 2-3 notifications per user with some variety
            $userNotifications = array_slice($notifications, 0, rand(2, 4));

            foreach ($userNotifications as $index => $notificationData) {
                Notification::create([
                    'user_id' => $user->id,
                    'title' => $notificationData['title'],
                    'message' => $notificationData['message'],
                    'type' => $notificationData['type'],
                    'link' => $notificationData['link'],
                    'is_read' => $index > 1, // First 2 notifications are unread
                    'read_at' => $index > 1 ? now()->subHours(rand(1, 24)) : null,
                    'created_at' => now()->subMinutes(rand(5, 1440)), // Random time in last 24 hours
                ]);
            }
        }
    }
}
