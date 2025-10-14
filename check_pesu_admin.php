<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

echo "=== CHECKING PESU ADMIN USERS ===\n";

$pesuAdmins = User::role('pesu_admin')->get();

echo "Found " . $pesuAdmins->count() . " PESU admin(s):\n";

foreach ($pesuAdmins as $admin) {
    echo "- ID: {$admin->id}, Name: {$admin->name}, Email: {$admin->email}\n";
}

if ($pesuAdmins->count() == 0) {
    echo "\n⚠️  No PESU admin users found. The delete button will not appear.\n";
    echo "To test, you need to:\n";
    echo "1. Create a user with 'pesu_admin' role, or\n";
    echo "2. Assign 'pesu_admin' role to an existing user\n";
} else {
    echo "\n✅ PESU admin users exist. Delete button should appear for these users.\n";
}

echo "\n=== AVAILABLE ROLES ===\n";
$roles = \Spatie\Permission\Models\Role::all();
foreach ($roles as $role) {
    $userCount = User::role($role->name)->count();
    echo "- {$role->name}: {$userCount} user(s)\n";
}
