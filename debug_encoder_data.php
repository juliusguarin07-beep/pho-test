<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

// Check case reports
echo "=== CASE REPORTS DEBUG ===\n";
echo "Total Case Reports: " . App\Models\CaseReport::count() . "\n\n";

// Check all case reports
$reports = App\Models\CaseReport::with(['reporter', 'disease'])->get();
foreach ($reports as $report) {
    echo "Case ID: " . $report->case_id . "\n";
    echo "Status: " . $report->status . "\n";
    echo "Reported By: " . $report->reported_by . " (" . ($report->reporter ? $report->reporter->name : 'Unknown') . ")\n";
    echo "Disease: " . ($report->disease ? $report->disease->name : 'Unknown') . "\n";
    echo "Created: " . $report->created_at . "\n";
    echo "---\n";
}

// Check users with encoder role
echo "\n=== ENCODER USERS ===\n";
$encoders = App\Models\User::whereHas('roles', function($q) {
    $q->where('name', 'encoder');
})->get(['id', 'name', 'email']);

foreach ($encoders as $encoder) {
    echo "ID: " . $encoder->id . " - " . $encoder->name . " (" . $encoder->email . ")\n";

    // Check their case reports
    $userReports = App\Models\CaseReport::where('reported_by', $encoder->id)->count();
    echo "  Reports: " . $userReports . "\n";
}

echo "\nDone.\n";
