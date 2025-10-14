<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\CaseReport;

echo "=== TESTING VALIDATOR FEEDBACK DISPLAY ===\n";

// Check if we have any returned case reports with feedback
$returnedReports = CaseReport::where('status', 'returned')
    ->whereNotNull('validator_feedback')
    ->get();

echo "Found " . $returnedReports->count() . " returned case reports with feedback\n\n";

foreach ($returnedReports as $report) {
    echo "Case Report: " . $report->case_id . "\n";
    echo "Status: " . $report->status . "\n";
    echo "Validator Feedback: " . substr($report->validator_feedback, 0, 100) . "...\n";
    echo "Returned At: " . ($report->returned_at ?? 'NULL') . "\n";
    echo "Returned By: " . ($report->returned_by ?? 'NULL') . "\n";
    echo "---\n";
}

if ($returnedReports->count() === 0) {
    echo "No returned reports found. Creating a test case...\n";
    
    // Find a submitted report to test with
    $submittedReport = CaseReport::where('status', 'submitted')->first();
    
    if ($submittedReport) {
        echo "Found submitted report: " . $submittedReport->case_id . "\n";
        echo "Setting test feedback...\n";
        
        $submittedReport->update([
            'status' => 'returned',
            'validator_feedback' => 'This is a test feedback message from the validator. Please review the patient information section and ensure all required fields are properly filled out.',
            'returned_by' => 1, // Assuming user ID 1 exists
            'returned_at' => now(),
        ]);
        
        echo "✅ Test case created successfully!\n";
        echo "Case Report " . $submittedReport->case_id . " now has validator feedback.\n";
    } else {
        echo "❌ No submitted reports found to create test case.\n";
    }
}

echo "\n=== CHECKING FEEDBACK FIELD AVAILABILITY ===\n";

// Check if the validator_feedback column exists and is being retrieved
$sampleReport = CaseReport::first();
if ($sampleReport) {
    $attributes = $sampleReport->getAttributes();
    if (array_key_exists('validator_feedback', $attributes)) {
        echo "✅ validator_feedback field is available in model\n";
    } else {
        echo "❌ validator_feedback field NOT found in model attributes\n";
    }
    
    // Show all status-related fields
    echo "\nStatus-related fields:\n";
    foreach (['status', 'validator_feedback', 'returned_by', 'returned_at'] as $field) {
        echo "- $field: " . ($attributes[$field] ?? 'NULL') . "\n";
    }
} else {
    echo "❌ No case reports found in database\n";
}