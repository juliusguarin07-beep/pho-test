<?php

require_once 'vendor/autoload.php';

// Load Laravel app
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Municipality;

echo "=== Municipality Coordinates Check ===\n";

$withCoordinates = Municipality::whereNotNull('latitude')->whereNotNull('longitude')->count();
$withoutCoordinates = Municipality::whereNull('latitude')->orWhereNull('longitude')->get();

echo "Municipalities WITH coordinates: $withCoordinates\n";
echo "Municipalities WITHOUT coordinates: " . $withoutCoordinates->count() . "\n\n";

if ($withoutCoordinates->count() > 0) {
    echo "Missing coordinates:\n";
    foreach ($withoutCoordinates as $municipality) {
        echo "- {$municipality->name} (lat: {$municipality->latitude}, lng: {$municipality->longitude})\n";
    }
}

// Show a few examples of municipalities with coordinates
echo "\nExamples with coordinates:\n";
$withCoords = Municipality::whereNotNull('latitude')->whereNotNull('longitude')->limit(5)->get();
foreach ($withCoords as $municipality) {
    echo "- {$municipality->name} (lat: {$municipality->latitude}, lng: {$municipality->longitude})\n";
}
