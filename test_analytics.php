<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== TESTING ANALYTICS QUERIES ===\n\n";

try {
    // Test Monthly Trend
    echo "Testing Monthly Trend Query...\n";
    $monthlyTrend = \App\Models\CaseReport::select(
            \Illuminate\Support\Facades\DB::raw("strftime('%Y-%m', date_reported) as month"),
            \Illuminate\Support\Facades\DB::raw('count(*) as total')
        )
        ->where('date_reported', '>=', now()->subMonths(12))
        ->groupBy('month')
        ->orderBy('month')
        ->get();

    echo "✓ Monthly Trend: " . $monthlyTrend->count() . " months\n";
    foreach ($monthlyTrend as $item) {
        echo "  {$item->month}: {$item->total} cases\n";
    }

    // Test Age Distribution
    echo "\nTesting Age Distribution Query...\n";
    $ageDistribution = \App\Models\CaseReport::select(
            \Illuminate\Support\Facades\DB::raw("CASE
                WHEN (strftime('%Y', 'now') - strftime('%Y', date_of_birth)) < 1 THEN '0-1'
                WHEN (strftime('%Y', 'now') - strftime('%Y', date_of_birth)) BETWEEN 1 AND 4 THEN '1-4'
                WHEN (strftime('%Y', 'now') - strftime('%Y', date_of_birth)) BETWEEN 5 AND 9 THEN '5-9'
                WHEN (strftime('%Y', 'now') - strftime('%Y', date_of_birth)) BETWEEN 10 AND 14 THEN '10-14'
                WHEN (strftime('%Y', 'now') - strftime('%Y', date_of_birth)) BETWEEN 15 AND 19 THEN '15-19'
                WHEN (strftime('%Y', 'now') - strftime('%Y', date_of_birth)) BETWEEN 20 AND 29 THEN '20-29'
                WHEN (strftime('%Y', 'now') - strftime('%Y', date_of_birth)) BETWEEN 30 AND 39 THEN '30-39'
                WHEN (strftime('%Y', 'now') - strftime('%Y', date_of_birth)) BETWEEN 40 AND 49 THEN '40-49'
                WHEN (strftime('%Y', 'now') - strftime('%Y', date_of_birth)) BETWEEN 50 AND 59 THEN '50-59'
                ELSE '60+'
            END as age_group"),
            \Illuminate\Support\Facades\DB::raw('count(*) as total')
        )
        ->groupBy('age_group')
        ->get();

    echo "✓ Age Distribution: " . $ageDistribution->count() . " groups\n";
    foreach ($ageDistribution as $item) {
        echo "  {$item->age_group}: {$item->total} cases\n";
    }

    echo "\n✅ All queries successful!\n";

} catch (\Exception $e) {
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
