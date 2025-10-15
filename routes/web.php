<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CaseReportController;
use App\Http\Controllers\OutbreakAlertController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\FacilityController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\ValidatorAnalyticsController;
use App\Models\OutbreakAlert;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

// Diagnostic route for testing
Route::get('/test-outbreak-alerts', function () {
    try {
        $alert = OutbreakAlert::first();

        if (!$alert) {
            return response()->json([
                'status' => 'error',
                'message' => 'No outbreak alerts found',
            ]);
        }

        $alert->load(['disease', 'municipality', 'creator']);
        $showUrl = route('outbreak-alerts.show', $alert->id);
        $editUrl = route('outbreak-alerts.edit', $alert->id);

        return response()->json([
            'status' => 'success',
            'alert_id' => $alert->id,
            'alert_title' => $alert->alert_title,
            'disease' => $alert->disease?->name,
            'municipality' => $alert->municipality?->name,
            'creator' => $alert->creator?->name,
            'show_url' => $showUrl,
            'edit_url' => $editUrl,
            'message' => 'Test the URLs above by pasting in browser'
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage(),
        ], 500);
    }
});

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Case Reports
    Route::resource('case-reports', CaseReportController::class);
    Route::post('/case-reports/{caseReport}/validate', [CaseReportController::class, 'validate'])->name('case-reports.validate');
    Route::post('/case-reports/{caseReport}/approve', [CaseReportController::class, 'approve'])->name('case-reports.approve');
    Route::post('/case-reports/{caseReport}/return', [CaseReportController::class, 'returnReport'])->name('case-reports.return');
    Route::get('/case-reports-export', [CaseReportController::class, 'export'])->name('case-reports.export');

    // Analytics - Role-based routing
    Route::get('/analytics', function () {
        $user = \Illuminate\Support\Facades\Auth::user();
        if ($user->hasRole('pesu_admin')) {
            return redirect()->route('analytics.pesu');
        } elseif ($user->hasRole('validator')) {
            return redirect()->route('analytics.validator');
        }
        // Default fallback for other roles
        return redirect()->route('dashboard');
    })->name('analytics.index');

    // PESU Admin Analytics (Full Epidemiologic Surveillance)
    Route::get('/analytics/pesu', [AnalyticsController::class, 'index'])
          ->middleware('role:pesu_admin')
          ->name('analytics.pesu');

    // Validator Analytics (Simplified Dashboard)
    Route::get('/analytics/validator', [ValidatorAnalyticsController::class, 'index'])
          ->middleware('role:validator')
          ->name('analytics.validator');

    // Outbreak Alerts
    Route::resource('outbreak-alerts', OutbreakAlertController::class);
    Route::post('/outbreak-alerts/{outbreakAlert}/publish', [OutbreakAlertController::class, 'publish'])->name('outbreak-alerts.publish');
    Route::post('/outbreak-alerts/{outbreakAlert}/resolve', [OutbreakAlertController::class, 'resolve'])->name('outbreak-alerts.resolve');
    Route::post('/outbreak-alerts/{outbreakAlert}/conclude', [OutbreakAlertController::class, 'conclude'])->name('outbreak-alerts.conclude');

    // User Management (PESU Admin only)
    Route::middleware(['role:pesu_admin'])->group(function () {
        Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserManagementController::class, 'create'])->name('users.create');
        Route::post('/users', [UserManagementController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/edit', [UserManagementController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserManagementController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');
        Route::post('/users/{user}/toggle-status', [UserManagementController::class, 'toggleStatus'])->name('users.toggle-status');

        // Facilities Management
        Route::resource('facilities', FacilityController::class);

        // Audit Logs
        Route::get('/audit-logs', [DashboardController::class, 'auditLogs'])->name('audit-logs.index');
    });

    // API endpoints for dropdowns
    Route::get('/api/barangays/{municipality}', function ($municipality) {
        return \App\Models\Barangay::where('municipality_id', $municipality)->get();
    });

    Route::get('/api/diseases', function () {
        return \App\Models\Disease::where('is_active', true)->get();
    });

    Route::get('/api/statistics', [DashboardController::class, 'statistics'])->name('api.statistics');
});

// Route to serve storage files
Route::get('/storage/{path}', function ($path) {
    $file = storage_path('app/public/' . $path);

    if (!file_exists($file)) {
        abort(404);
    }

    return response()->file($file);
})->where('path', '.*');

require __DIR__.'/auth.php';

