<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$report = \App\Models\CaseReport::first();

echo "Case Report date_reported column: '{$report->date_reported}'\n";
echo "Start date: '2025-09-11'\n";
echo "End date: '2025-10-11'\n\n";

// Test the query with raw SQL
$result = \Illuminate\Support\Facades\DB::select("
    SELECT * FROM case_reports
    WHERE date_reported BETWEEN '2025-09-11' AND '2025-10-11'
");

echo "Query result count: " . count($result) . "\n";

if (count($result) > 0) {
    echo "Found case: " . $result[0]->case_id . "\n";
} else {
    echo "No cases found\n\n";

    // Check what's in the date field
    $all = \Illuminate\Support\Facades\DB::select("SELECT case_id, date_reported FROM case_reports");
    foreach ($all as $case) {
        echo "Case {$case->case_id}: date_reported = '{$case->date_reported}'\n";
    }

    // Try with datetime comparison
    echo "\nTrying with datetime format...\n";
    $result2 = \Illuminate\Support\Facades\DB::select("
        SELECT * FROM case_reports
        WHERE date(date_reported) BETWEEN '2025-09-11' AND '2025-10-11'
    ");
    echo "Result count with date(): " . count($result2) . "\n";
}
