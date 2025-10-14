<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Facility;

class AssignUserFacilities extends Command
{
    protected $signature = 'assign:user-facilities';
    protected $description = 'Assign facilities to existing users';

    public function handle()
    {
        $this->info('=== ASSIGNING USER FACILITIES ===');

        // Get some facilities to assign
        $lingayenRhu = Facility::where('name', 'LIKE', '%Lingayen RHU%')->first();
        $rimc = Facility::where('name', 'LIKE', '%Region I Medical Center%')->first();
        $dagupanHealth = Facility::where('name', 'LIKE', '%Dagupan%')->first();
        $provincialHospital = Facility::where('type', 'Provincial Hospital')->first();

        // If we don't have RIMC, get any district hospital
        if (!$rimc) {
            $rimc = Facility::where('type', 'District Hospital')->first();
        }

        // If we don't have Dagupan facility, get any RHU
        if (!$dagupanHealth) {
            $dagupanHealth = $lingayenRhu ?? Facility::where('type', 'RHU')->first();
        }

        // Assign facilities to users
        $assignments = [
            'pesu@pangasinan.gov.ph' => $provincialHospital,
            'validator@rimc.gov.ph' => $rimc,
            'encoder@dcho.gov.ph' => $dagupanHealth,
            'sean@gmail.com' => $lingayenRhu ?? $rimc
        ];

        foreach ($assignments as $email => $facility) {
            $user = User::where('email', $email)->first();
            if ($user && $facility) {
                $user->facility_id = $facility->id;
                $user->save();
                $this->line("✅ {$user->name} → {$facility->name}");
            } else {
                $this->error("❌ Could not assign facility to {$email}");
            }
        }

        $this->info('User facility assignments completed!');
        return 0;
    }
}
