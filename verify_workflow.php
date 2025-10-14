<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== WORKFLOW VERIFICATION ===\n\n";

// Get validator
$validator = \App\Models\User::where('email', 'validator@rimc.gov.ph')->first();
echo "Validator: {$validator->name}\n";
echo "Facility ID: {$validator->facility_id}\n";
$facility = \App\Models\Facility::find($validator->facility_id);
echo "Facility: {$facility->name}\n\n";

// Check what validator should see
echo "=== Reports Validator Should See ===\n";
$validatorReports = \App\Models\CaseReport::where('reporting_facility_id', $validator->facility_id)
    ->whereIn('status', ['submitted', 'validated', 'returned'])
    ->get();

echo "Total: {$validatorReports->count()}\n\n";

foreach ($validatorReports as $report) {
    echo "Case ID: {$report->case_id}\n";
    echo "  Status: {$report->status}\n";
    echo "  Disease: {$report->disease->name}\n";
    echo "  Patient: {$report->first_name} {$report->last_name}\n";
    echo "  Facility ID: {$report->reporting_facility_id}\n";
    echo "\n";
}

echo "=== All Case Reports ===\n";
$allReports = \App\Models\CaseReport::with('disease')->get();
foreach ($allReports as $report) {
    echo "{$report->case_id} - Status: {$report->status} - Facility: {$report->reporting_facility_id}\n";
}
