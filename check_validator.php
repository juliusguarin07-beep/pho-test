<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== VALIDATOR DEBUGGING ===\n\n";

// Check validator user
$validator = \App\Models\User::where('email', 'validator@pangasinan.gov.ph')->first();
if ($validator) {
    echo "Validator Found:\n";
    echo "  Name: {$validator->name}\n";
    echo "  Facility ID: " . ($validator->facility_id ?? 'NULL') . "\n";
    echo "  Roles: " . $validator->roles->pluck('name')->implode(', ') . "\n";

    if ($validator->facility_id) {
        $facility = \App\Models\Facility::find($validator->facility_id);
        if ($facility) {
            echo "  Facility Name: {$facility->name}\n";
        }
    }
} else {
    echo "Validator not found!\n";
}

echo "\n=== SUBMITTED CASE REPORTS ===\n";
$submittedReports = \App\Models\CaseReport::where('status', 'submitted')->get();
echo "Total Submitted: {$submittedReports->count()}\n\n";

if ($submittedReports->count() > 0) {
    foreach ($submittedReports as $report) {
        echo "Case ID: {$report->case_id}\n";
        echo "  Status: {$report->status}\n";
        echo "  Reporting Facility ID: " . ($report->reporting_facility_id ?? 'NULL') . "\n";
        echo "  Reported By: {$report->reported_by}\n";

        if ($report->reporting_facility_id) {
            $facility = \App\Models\Facility::find($report->reporting_facility_id);
            if ($facility) {
                echo "  Facility Name: {$facility->name}\n";
            }
        }

        $reporter = \App\Models\User::find($report->reported_by);
        if ($reporter) {
            echo "  Reporter: {$reporter->name} ({$reporter->email})\n";
        }
        echo "\n";
    }
}

echo "=== ALL CASE REPORTS ===\n";
$allReports = \App\Models\CaseReport::all();
echo "Total Reports: {$allReports->count()}\n";
foreach ($allReports as $report) {
    echo "  {$report->case_id} - Status: {$report->status} - Facility ID: " . ($report->reporting_facility_id ?? 'NULL') . "\n";
}
