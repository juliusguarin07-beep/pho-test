<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Update validator's facility to Facility 1
$validator = \App\Models\User::where('email', 'validator@rimc.gov.ph')->first();

if ($validator) {
    $validator->facility_id = 1;
    $validator->save();

    echo "Validator facility updated!\n";
    echo "Name: {$validator->name}\n";
    echo "Facility ID: {$validator->facility_id}\n";

    $facility = \App\Models\Facility::find($validator->facility_id);
    if ($facility) {
        echo "Facility Name: {$facility->name}\n";
    }
} else {
    echo "Validator not found!\n";
}
