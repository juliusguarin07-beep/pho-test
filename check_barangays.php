<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$municipalities = \App\Models\Municipality::withCount('barangays')->get();

echo "Barangay Distribution:\n";
echo str_repeat('=', 50) . "\n";

foreach ($municipalities as $m) {
    echo sprintf("%-20s: %3d barangays\n", $m->name, $m->barangays_count);
}

echo str_repeat('=', 50) . "\n";
echo sprintf("%-20s: %3d barangays\n", "TOTAL", \App\Models\Barangay::count());
