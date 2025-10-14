<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== DEBUGGING SUMMARY STATISTICS ===\n\n";

$startDate = '2025-09-11';
$endDate = '2025-10-11';

echo "Date Range: {$startDate} to {$endDate}\n\n";

// Check the actual case report
$report = \App\Models\CaseReport::first();
echo "Case Report Details:\n";
echo "  date_reported: {$report->date_reported}\n";
echo "  case_classification: {$report->case_classification}\n";
echo "  outcome: {$report->outcome}\n";
echo "  status: {$report->status}\n\n";

// Test Total Cases
echo "Testing Total Cases Query:\n";
$totalCases = \App\Models\CaseReport::whereDate('date_reported', '>=', $startDate)
    ->whereDate('date_reported', '<=', $endDate)
    ->count();
echo "  Result: {$totalCases}\n\n";

// Test Confirmed Cases
echo "Testing Confirmed Cases Query:\n";
$confirmedCases = \App\Models\CaseReport::whereDate('date_reported', '>=', $startDate)
    ->whereDate('date_reported', '<=', $endDate)
    ->where('case_classification', 'Confirmed')
    ->count();
echo "  Result: {$confirmedCases}\n";
echo "  (Looking for case_classification = 'Confirmed', actual = '{$report->case_classification}')\n\n";

// Test Deaths
echo "Testing Deaths Query:\n";
$deaths = \App\Models\CaseReport::whereDate('date_reported', '>=', $startDate)
    ->whereDate('date_reported', '<=', $endDate)
    ->where('outcome', 'Died')
    ->count();
echo "  Result: {$deaths}\n";
echo "  (Looking for outcome = 'Died', actual = '{$report->outcome}')\n\n";

// Check current date vs filter dates
echo "Date Comparison:\n";
echo "  Current Date: " . now()->toDateString() . "\n";
echo "  Filter Start: {$startDate}\n";
echo "  Filter End: {$endDate}\n";
echo "  Case Date: " . \Carbon\Carbon::parse($report->date_reported)->toDateString() . "\n";
