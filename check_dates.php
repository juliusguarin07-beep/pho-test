<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== DATE RANGE ANALYSIS ===\n\n";

$report = \App\Models\CaseReport::first();
if ($report) {
    echo "Case Report Date: {$report->date_reported}\n";
    echo "Case Report Created: {$report->created_at}\n";
}

echo "\nCurrent Date: " . now()->toDateString() . "\n";
echo "Default Start Date (30 days ago): " . now()->subDays(30)->toDateString() . "\n";
echo "Default End Date (today): " . now()->toDateString() . "\n";

echo "\n=== Testing with different date ranges ===\n";

// Test with last 30 days
$count30 = \App\Models\CaseReport::whereBetween('date_reported', [
    now()->subDays(30)->toDateString(),
    now()->toDateString()
])->count();
echo "Cases in last 30 days: {$count30}\n";

// Test with last 90 days
$count90 = \App\Models\CaseReport::whereBetween('date_reported', [
    now()->subDays(90)->toDateString(),
    now()->toDateString()
])->count();
echo "Cases in last 90 days: {$count90}\n";

// Test with all time
$countAll = \App\Models\CaseReport::count();
echo "Total cases (all time): {$countAll}\n";
