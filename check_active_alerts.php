<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\OutbreakAlert;

echo "=== ALERT DATE ANALYSIS ===\n\n";

echo "Current date/time: " . now()->format('Y-m-d H:i:s') . "\n\n";

$alerts = OutbreakAlert::with(['disease', 'municipality'])->get();

foreach ($alerts as $alert) {
    echo "Alert ID: {$alert->id}\n";
    echo "  Disease: " . ($alert->disease->name ?? 'N/A') . "\n";
    echo "  Municipality: " . ($alert->municipality->name ?? 'N/A') . "\n";
    echo "  Status: {$alert->status}\n";
    echo "  Start Date: {$alert->alert_start_date} (vs now: " . ($alert->alert_start_date <= now() ? 'PAST/NOW' : 'FUTURE') . ")\n";
    echo "  End Date: " . ($alert->alert_end_date ?? 'None') . " (vs now: " . ($alert->alert_end_date ? ($alert->alert_end_date >= now() ? 'FUTURE/NOW' : 'PAST') : 'N/A') . ")\n";

    // Check if it meets the criteria
    $isPublished = $alert->status === 'published';
    $hasStarted = $alert->alert_start_date <= now();
    $hasNotEnded = !$alert->alert_end_date || $alert->alert_end_date >= now();

    $isActive = $isPublished && $hasStarted && $hasNotEnded;

    echo "  Active: " . ($isActive ? 'YES' : 'NO') . "\n";

    if (!$isActive) {
        echo "  Reasons for inactivity:\n";
        if (!$isPublished) echo "    - Not published\n";
        if (!$hasStarted) echo "    - Has not started yet\n";
        if (!$hasNotEnded) echo "    - Has already ended\n";
    }

    echo "  ---\n";
}

echo "\n=== SUMMARY ===\n";
echo "Total alerts: " . $alerts->count() . "\n";
echo "Published alerts: " . $alerts->where('status', 'published')->count() . "\n";
echo "Active alerts (meeting all criteria): " . OutbreakAlert::where('status', 'published')
    ->where('alert_start_date', '<=', now())
    ->where(function ($q) {
        $q->whereNull('alert_end_date')
          ->orWhere('alert_end_date', '>=', now());
    })->count() . "\n";
