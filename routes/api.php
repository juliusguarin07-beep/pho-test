<?php

use App\Http\Controllers\Api\PublicAlertController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Public API for Resident Mobile App
Route::prefix('v1/public')->group(function () {
    // Outbreak Alerts
    Route::get('/alerts', [PublicAlertController::class, 'index']);
    Route::get('/alerts/{id}', [PublicAlertController::class, 'show']);
    Route::get('/alerts-statistics', [PublicAlertController::class, 'statistics']);
    Route::get('/alerts-map', [PublicAlertController::class, 'mapData']);
    Route::get('/alerts-recent', [PublicAlertController::class, 'recent']);

    // Location Data
    Route::get('/municipalities', [PublicAlertController::class, 'municipalities']);
    Route::get('/barangays', [PublicAlertController::class, 'barangays']);
});
