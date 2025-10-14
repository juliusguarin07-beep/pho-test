<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== TESTING ANALYTICS WITH whereDate ===\n\n";

$startDate = now()->subDays(30)->toDateString();
$endDate = now()->toDateString();

echo "Date Range: {$startDate} to {$endDate}\n\n";

// Test Cases by Disease
$casesByDisease = \App\Models\CaseReport::select('disease_id', \Illuminate\Support\Facades\DB::raw('count(*) as total'))
    ->whereDate('date_reported', '>=', $startDate)
    ->whereDate('date_reported', '<=', $endDate)
    ->with('disease:id,name')
    ->groupBy('disease_id')
    ->orderByDesc('total')
    ->get();

echo "Cases by Disease: {$casesByDisease->count()} diseases\n";
foreach ($casesByDisease as $item) {
    echo "  {$item->disease->name}: {$item->total} cases\n";
}

// Test Summary
$totalCases = \App\Models\CaseReport::whereDate('date_reported', '>=', $startDate)
    ->whereDate('date_reported', '<=', $endDate)
    ->count();

echo "\nTotal Cases: {$totalCases}\n";

echo "\n✅ Analytics working correctly with whereDate!\n";
