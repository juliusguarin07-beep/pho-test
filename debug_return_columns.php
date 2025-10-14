<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Schema;

echo "=== CASE REPORTS TABLE COLUMNS ===\n";

$columns = Schema::getColumnListing('case_reports');

foreach ($columns as $column) {
    echo "- $column\n";
}

echo "\n=== CHECKING FOR RETURN COLUMNS ===\n";

$returnColumns = ['validator_feedback', 'returned_by', 'returned_at'];

foreach ($returnColumns as $column) {
    if (in_array($column, $columns)) {
        echo "✅ $column - EXISTS\n";
    } else {
        echo "❌ $column - MISSING\n";
    }
}

echo "\n=== SAMPLE CASE REPORT DATA ===\n";

$caseReport = \App\Models\CaseReport::first();
if ($caseReport) {
    echo "Case ID: " . $caseReport->case_id . "\n";
    echo "Status: " . $caseReport->status . "\n";
    echo "Reporting Facility ID: " . ($caseReport->reporting_facility_id ?? 'NULL') . "\n";
} else {
    echo "No case reports found\n";
}
