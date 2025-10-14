<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== ALL USERS ===\n\n";

$users = \App\Models\User::with('roles', 'facility')->get();

foreach ($users as $user) {
    echo "Name: {$user->name}\n";
    echo "  Email: {$user->email}\n";
    echo "  Roles: " . $user->roles->pluck('name')->implode(', ') . "\n";
    echo "  Facility ID: " . ($user->facility_id ?? 'NULL') . "\n";
    if ($user->facility) {
        echo "  Facility Name: {$user->facility->name}\n";
    }
    echo "\n";
}
