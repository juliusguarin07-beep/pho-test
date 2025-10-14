<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Facility;

class CheckUserFacilities extends Command
{
    protected $signature = 'check:user-facilities';
    protected $description = 'Check user facility assignments';

    public function handle()
    {
        $this->info('=== USER FACILITY ASSIGNMENTS ===');

        $users = User::with('facility')->get(['id', 'name', 'email', 'facility_id']);

        foreach ($users as $user) {
            $facilityInfo = $user->facility_id ?
                ($user->facility ? $user->facility->name : 'Facility not found') :
                'No facility assigned';

            $this->line("{$user->name} ({$user->email}) - Facility ID: " .
                       ($user->facility_id ?? 'NULL') . " - " . $facilityInfo);
        }

        $this->line('');
        $this->info('Available Facilities:');
        $facilities = Facility::orderBy('type')->orderBy('name')->take(5)->get(['id', 'name', 'type']);
        foreach ($facilities as $facility) {
            $this->line("ID: {$facility->id} - {$facility->name} ({$facility->type})");
        }

        $this->line('... and ' . (Facility::count() - 5) . ' more facilities');

        return 0;
    }
}
