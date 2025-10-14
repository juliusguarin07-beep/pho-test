<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class ReassignValidatorFacility extends Command
{
    protected $signature = 'reassign:validator-facility';
    protected $description = 'Assign validator to same facility as encoder';

    public function handle()
    {
        $this->info('=== FIXING VALIDATOR FACILITY ASSIGNMENT ===');

        // Get encoder and validator
        $encoder = User::where('email', 'encoder@dcho.gov.ph')->first();
        $validator = User::where('email', 'validator@rimc.gov.ph')->first();

        if (!$encoder || !$validator) {
            $this->error('Encoder or validator not found!');
            return 1;
        }

        $this->line("Encoder: {$encoder->name} - Facility ID: {$encoder->facility_id}");
        $this->line("Validator: {$validator->name} - Facility ID: {$validator->facility_id}");

        // Assign validator to same facility as encoder
        $validator->facility_id = $encoder->facility_id;
        $validator->save();

        $this->info("✅ Validator reassigned to same facility as encoder (ID: {$encoder->facility_id})");

        return 0;
    }
}
