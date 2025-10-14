<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\CaseReport;

echo "=== TESTING RETURN FUNCTIONALITY ===\n";

// Find a submitted case report
$caseReport = CaseReport::where('status', 'submitted')->first();

if (!$caseReport) {
    echo "❌ No submitted case reports found for testing\n";
    exit;
}

echo "✅ Found submitted case report: " . $caseReport->case_id . "\n";
echo "Current status: " . $caseReport->status . "\n";
echo "Reporting facility: " . ($caseReport->reporting_facility_id ?? 'NULL') . "\n";

// Test validation rules
$testFeedback = "dwdga"; // Short feedback like in screenshot
echo "\nTesting feedback validation:\n";
echo "Feedback: '$testFeedback'\n";
echo "Length: " . strlen($testFeedback) . " characters\n";

if (strlen($testFeedback) < 10) {
    echo "❌ VALIDATION ISSUE: Feedback must be at least 10 characters\n";
    echo "Backend validation rule: 'required|string|min:10|max:1000'\n";
} else {
    echo "✅ Feedback length is valid\n";
}

echo "\nTesting with valid feedback:\n";
$validFeedback = "This report needs more detailed information about the patient's symptoms";
echo "Feedback: '$validFeedback'\n";
echo "Length: " . strlen($validFeedback) . " characters\n";

if (strlen($validFeedback) >= 10) {
    echo "✅ Valid feedback would pass validation\n";
} else {
    echo "❌ Still too short\n";
}
