<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Facility;
use App\Models\Municipality;

class FacilitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing facilities
        Facility::truncate();

        // Get default municipality (we'll use Lingayen as default for now)
        $defaultMunicipality = Municipality::where('name', 'Lingayen')->first();
        if (!$defaultMunicipality) {
            $defaultMunicipality = Municipality::first();
        }

        // District Hospitals
        $districtHospitals = [
            'Lingayen District Hospital',
            'Western Pangasinan District Hospital (Alaminos)',
            'Eastern Pangasinan District Hospital (Tayug)',
            'Urdaneta District Hospital',
            'Bayambang District Hospital',
            'Mangatarem District Hospital',
            'Asingan Community Hospital',
            'Bolinao Community Hospital',
            'Dasol Community Hospital',
            'Manaoag Community Hospital',
            'Mapandan Community Hospital',
            'Pozorrubio Community Hospital',
            'Umingan Community Hospital'
        ];

        foreach ($districtHospitals as $hospital) {
            Facility::create([
                'name' => $hospital,
                'type' => 'District Hospital',
                'municipality_id' => $defaultMunicipality->id,
                'address' => 'Pangasinan',
                'contact_number' => null,
                'email' => null,
                'is_active' => true
            ]);
        }

        // Provincial Hospital
        Facility::create([
            'name' => 'Pangasinan Provincial Hospital San Carlos City',
            'type' => 'Provincial Hospital',
            'municipality_id' => $defaultMunicipality->id,
            'address' => 'San Carlos City, Pangasinan',
            'contact_number' => null,
            'email' => null,
            'is_active' => true
        ]);

        // Rural Health Units (RHU)
        $rhus = [
            'Balungao RHU',
            'Natividad RHU',
            'Rosales RHU',
            'San Nicolas RHU',
            'San Quintin RHU',
            'Sta. Maria RHU',
            'Tayug RHU',
            'Umingan RHU I & II',
            'Alcala RHU',
            'Basista RHU',
            'Bautista RHU',
            'Bayambang RHU',
            'Sto. Tomas RHU',
            'Alaminos RHU',
            'Lingayen RHU I & II',
            'Bugallon RHU I & II',
            'Mangatarem RHU I & II',
            'Urbiztondo RHU',
            'Binmaley RHU I & II',
            'Malasiqui RHU I & II',
            'San Fabian RHU',
            'Sta. Mangaldan RHU I & II',
            'San Quintin RHU'
        ];

        foreach ($rhus as $rhu) {
            Facility::create([
                'name' => $rhu,
                'type' => 'RHU',
                'municipality_id' => $defaultMunicipality->id,
                'address' => 'Pangasinan',
                'contact_number' => null,
                'email' => null,
                'is_active' => true
            ]);
        }

        $this->command->info('Facilities seeded successfully!');
        $this->command->line('- ' . count($districtHospitals) . ' District Hospitals');
        $this->command->line('- 1 Provincial Hospital');
        $this->command->line('- ' . count($rhus) . ' Rural Health Units (RHU)');
    }
}
