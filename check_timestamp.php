<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\CaseReport;

echo "=== CHECKING TIMESTAMP FORMAT ===\n";

$report = CaseReport::where('case_id', 'CASE-2025-000007')->first();

if ($report) {
    echo "returned_at raw: " . $report->returned_at . "\n";
    echo "returned_at type: " . gettype($report->returned_at) . "\n";
    echo "Current time: " . now() . "\n";
    echo "Current timezone: " . config('app.timezone') . "\n";
    echo "PHP timezone: " . date_default_timezone_get() . "\n";

    if ($report->returned_at) {
        echo "\nFormatting tests:\n";
        echo "ISO format: " . $report->returned_at . "\n";
        echo "Human readable: " . \Carbon\Carbon::parse($report->returned_at)->format('M j, Y g:i A') . "\n";
        echo "With timezone: " . \Carbon\Carbon::parse($report->returned_at)->setTimezone(config('app.timezone', 'UTC'))->format('M j, Y g:i A') . "\n";
    }
} else {
    echo "Case report not found\n";
}
