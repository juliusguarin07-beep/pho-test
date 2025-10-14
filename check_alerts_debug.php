<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\OutbreakAlert;

echo "=== OUTBREAK ALERTS DEBUG ===\n\n";

// Check total alerts
$totalAlerts = OutbreakAlert::count();
echo "Total alerts in database: $totalAlerts\n";

// Check published alerts
$publishedAlerts = OutbreakAlert::where('status', 'published')->count();
echo "Published alerts: $publishedAlerts\n";

// Check active alerts (based on date criteria)
$activeAlerts = OutbreakAlert::where('status', 'published')
    ->where('alert_start_date', '<=', now())
    ->where(function ($q) {
        $q->whereNull('alert_end_date')
          ->orWhere('alert_end_date', '>=', now());
    })->count();
echo "Active alerts (published + date criteria): $activeAlerts\n\n";

// List all alerts with details
echo "=== ALL ALERTS DETAILS ===\n";
$alerts = OutbreakAlert::with(['disease', 'municipality'])->get();
foreach ($alerts as $alert) {
    echo "ID: {$alert->id}\n";
    echo "  Disease: " . ($alert->disease->name ?? 'N/A') . "\n";
    echo "  Municipality: " . ($alert->municipality->name ?? 'N/A') . "\n";
    echo "  Level: {$alert->alert_level}\n";
    echo "  Status: {$alert->status}\n";
    echo "  Start Date: {$alert->alert_start_date}\n";
    echo "  End Date: " . ($alert->alert_end_date ?? 'None') . "\n";
    echo "  Created: {$alert->created_at}\n";
    echo "  Updated: {$alert->updated_at}\n";
    echo "  ---\n";
}

// Check what the API endpoint returns
echo "\n=== API MAPDATA SIMULATION ===\n";
$query = OutbreakAlert::query()
    ->where('status', 'published')
    ->where('alert_start_date', '<=', now())
    ->where(function ($q) {
        $q->whereNull('alert_end_date')
          ->orWhere('alert_end_date', '>=', now());
    });

$apiAlerts = $query->with([
    'disease:id,name,category',
    'municipality:id,name,latitude,longitude'
])
->select([
    'id',
    'disease_id',
    'municipality_id',
    'alert_level',
    'alert_title',
    'alert_details',
    'affected_barangays',
    'number_of_cases',
    'alert_start_date',
    'alert_end_date',
    'updated_at'
])
->get();

echo "API would return " . $apiAlerts->count() . " alerts\n";

foreach ($apiAlerts as $alert) {
    echo "API Alert ID: {$alert->id}\n";
    echo "  Disease: " . ($alert->disease->name ?? 'Unknown') . "\n";
    echo "  Municipality: " . ($alert->municipality->name ?? 'Unknown') . "\n";
    echo "  Alert Level: {$alert->alert_level}\n";
    echo "  Coordinates: ";
    if ($alert->municipality && $alert->municipality->latitude && $alert->municipality->longitude) {
        echo "Lat: {$alert->municipality->latitude}, Lng: {$alert->municipality->longitude}\n";
    } else {
        echo "No coordinates\n";
    }
    echo "  ---\n";
}
