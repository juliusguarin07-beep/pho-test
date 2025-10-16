<?php

namespace App\Console\Commands;

use App\Models\OutbreakAlert;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TestPublicAPI extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'test:public-api';

    /**
     * The console command description.
     */
    protected $description = 'Test the public API that residents use';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing Public API for Residents...');

        // Test direct database query first
        $publishedAlerts = OutbreakAlert::where('status', 'published')
            ->where('alert_start_date', '<=', now())
            ->where(function ($q) {
                $q->whereNull('alert_end_date')
                  ->orWhere('alert_end_date', '>=', now());
            })
            ->with(['disease:id,name', 'municipality:id,name'])
            ->get();

        $this->info("📊 Published alerts in database: {$publishedAlerts->count()}");

        foreach ($publishedAlerts as $alert) {
            $this->line("   - {$alert->alert_title}");
            $this->line("     Disease: {$alert->disease->name}");
            $this->line("     Municipality: {$alert->municipality->name}");
            $this->line("     Status: {$alert->status}");
            $this->line("     Is Automatic: " . ($alert->is_automatic ? 'Yes' : 'No'));
            $this->line("     Active: " . ($alert->is_active ? 'Yes' : 'No'));
            $this->line("");
        }

        // Test API endpoint via HTTP
        try {
            $this->info('Testing API endpoint at http://127.0.0.1:8000/api/v1/public/alerts...');

            $response = Http::get('http://127.0.0.1:8000/api/v1/public/alerts');

            if ($response->successful()) {
                $data = $response->json();
                $this->info("✅ API responded successfully");
                $this->info("📊 API returned {$data['data']['total']} alerts");

                foreach ($data['data']['data'] as $alert) {
                    // Use the actual field name from the API response
                    $title = $alert['alert_title'] ?? $alert['title'] ?? 'Unknown Title';
                    $this->line("   - API Alert: {$title}");
                    $isAutomatic = isset($alert['is_automatic']) && $alert['is_automatic'] ? 'Yes' : 'No';
                    $this->line("     Is Automatic: {$isAutomatic}");
                }
            } else {
                $this->error("❌ API request failed: " . $response->status());
            }
        } catch (\Exception $e) {
            $this->error("❌ API test failed: " . $e->getMessage());
        }

        return 0;
    }
}
