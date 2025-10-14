<?php

use App\Http\Controllers\OutbreakAlertController;
use App\Models\OutbreakAlert;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Diagnostic Route for Testing Outbreak Alerts
|--------------------------------------------------------------------------
|
| This route helps diagnose issues with the show() and edit() methods
| Access: http://127.0.0.1:8000/test-outbreak-alerts
|
*/

Route::get('/test-outbreak-alerts', function () {
    try {
        // Get first alert
        $alert = OutbreakAlert::first();

        if (!$alert) {
            return response()->json([
                'status' => 'error',
                'message' => 'No outbreak alerts found in database',
                'solution' => 'Create an alert first'
            ]);
        }

        // Test loading relationships
        $alert->load(['disease', 'municipality', 'creator']);

        // Test route generation
        $showUrl = route('outbreak-alerts.show', $alert->id);
        $editUrl = route('outbreak-alerts.edit', $alert->id);

        return response()->json([
            'status' => 'success',
            'alert_id' => $alert->id,
            'alert_title' => $alert->alert_title,
            'disease' => $alert->disease?->name ?? 'Not loaded',
            'municipality' => $alert->municipality?->name ?? 'Not loaded',
            'creator' => $alert->creator?->name ?? 'Not loaded',
            'routes' => [
                'show' => $showUrl,
                'edit' => $editUrl
            ],
            'test_links' => [
                'show' => '<a href="' . $showUrl . '">Click to test Show page</a>',
                'edit' => '<a href="' . $editUrl . '">Click to test Edit page</a>'
            ],
            'message' => 'All data loaded successfully. Click the test links above.'
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
})->name('test.outbreak-alerts');
