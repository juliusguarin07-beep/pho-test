<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== DATE FORMAT ANALYSIS ===\n\n";

// Check what the default date range is
$startDate = now()->subDays(30)->toDateString();
$endDate = now()->toDateString();

echo "Server Current Time: " . now()->toString() . "\n";
echo "Server Current Date: " . now()->toDateString() . "\n\n";

echo "Default Date Range (last 30 days):\n";
echo "  Start: {$startDate}\n";
echo "  End: {$endDate}\n\n";

// Test if case falls in range
$case = \App\Models\CaseReport::first();
$caseDate = \Carbon\Carbon::parse($case->date_reported)->toDateString();
echo "Case Report Date: {$caseDate}\n\n";

// Check if date is in range
echo "Is case in range? ";
if ($caseDate >= $startDate && $caseDate <= $endDate) {
    echo "YES\n";
} else {
    echo "NO\n";
    echo "  Case date {$caseDate} is NOT between {$startDate} and {$endDate}\n";
}

// Now test with the actual query
echo "\nTesting with actual queries:\n";
$total = \App\Models\CaseReport::whereDate('date_reported', '>=', $startDate)
    ->whereDate('date_reported', '<=', $endDate)
    ->count();
echo "Total cases in range: {$total}\n";
