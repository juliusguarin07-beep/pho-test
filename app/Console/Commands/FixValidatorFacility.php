<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class FixValidatorFacility extends Command
{
    protected $signature = 'fix:validator-facility {user_id} {facility_id}';
    protected $description = 'Update validator facility assignment';

    public function handle()
    {
        $userId = $this->argument('user_id');
        $facilityId = $this->argument('facility_id');

        $user = User::findOrFail($userId);
        $user->facility_id = $facilityId;
        $user->save();

        $this->info("Updated user {$user->name} (ID: {$user->id}) facility_id to: {$facilityId}");

        return 0;
    }
}
