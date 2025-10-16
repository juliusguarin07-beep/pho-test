<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Automatic Alert Checking Command
Artisan::command('alerts:check-automatic', function () {
    $service = new \App\Services\AutomaticAlertService();
    $service->checkAndCreateAutomaticAlerts();
    $this->info('Automatic alert check completed');
})->purpose('Check disease thresholds and create automatic alerts');
