<?php

namespace App\Console\Commands;

use App\Models\OutbreakAlert;
use App\Services\OutbreakAlertService;
use Illuminate\Console\Command;

class TestInternalAlerts extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'test:internal-alerts';

    /**
     * The console command description.
     */
    protected $description = 'Test internal alert system for encoder/validator/PESU users';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Testing Internal Alert System for Dashboard Banner...');

        // Test the service that feeds the dashboard
        $service = new OutbreakAlertService();
        $automaticAlerts = $service->getCurrentAutomaticAlerts();

        $this->info("📊 Automatic alerts for internal users: {$automaticAlerts->count()}");

        foreach ($automaticAlerts as $alert) {
            $statusIcon = $alert->status === 'published' ? '📢' : '📝';
            $this->line("   {$statusIcon} {$alert->alert_title}");
            $this->line("     Disease: {$alert->disease->name}");
            $this->line("     Municipality: {$alert->municipality->name}");
            $this->line("     Status: {$alert->status}");
            $this->line("     Cases: {$alert->number_of_cases}");
            $this->line("     Threshold: {$alert->threshold_reached}");
            $this->line("     Is Active: " . ($alert->is_active ? 'Yes' : 'No'));
            $this->line("");
        }

        if ($automaticAlerts->count() === 0) {
            $this->warn('⚠️  No automatic alerts found for internal dashboard.');
            $this->info('💡 This could mean:');
            $this->line('   - No automatic alerts have been created');
            $this->line('   - All automatic alerts have been resolved');
            $this->line('   - The thresholds are not being met');
        } else {
            $this->info('✅ Internal users should see these alerts in their dashboard banner!');
        }

        // Also test the published alerts for comparison
        $publishedAlerts = $service->getPublishedAutomaticAlerts();
        $this->info("📊 Published automatic alerts for residents: {$publishedAlerts->count()}");

        return 0;
    }
}
