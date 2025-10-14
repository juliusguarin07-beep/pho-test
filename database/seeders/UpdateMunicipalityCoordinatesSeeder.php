<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateMunicipalityCoordinatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pangasinan municipalities with their actual coordinates
        $municipalities = [
            // Major Cities
            ['name' => 'Dagupan City', 'latitude' => 16.0433, 'longitude' => 120.3336],
            ['name' => 'San Carlos City', 'latitude' => 15.9325, 'longitude' => 120.3444],
            ['name' => 'Urdaneta City', 'latitude' => 15.9761, 'longitude' => 120.5711],
            ['name' => 'Alaminos City', 'latitude' => 16.1556, 'longitude' => 119.9808],

            // Municipalities (Major ones)
            ['name' => 'Lingayen', 'latitude' => 16.0191, 'longitude' => 120.2297],
            ['name' => 'Binmaley', 'latitude' => 16.0319, 'longitude' => 120.2681],
            ['name' => 'Mangaldan', 'latitude' => 16.0700, 'longitude' => 120.4022],
            ['name' => 'San Fabian', 'latitude' => 16.1156, 'longitude' => 120.4086],
            ['name' => 'Manaoag', 'latitude' => 16.0431, 'longitude' => 120.4878],
            ['name' => 'Bayambang', 'latitude' => 15.8114, 'longitude' => 120.4522],
            ['name' => 'Malasiqui', 'latitude' => 15.9200, 'longitude' => 120.4089],
            ['name' => 'Santa Barbara', 'latitude' => 15.9628, 'longitude' => 120.4147],
            ['name' => 'Calasiao', 'latitude' => 16.0131, 'longitude' => 120.3569],
            ['name' => 'Mapandan', 'latitude' => 16.0264, 'longitude' => 120.4681],
            ['name' => 'Villasis', 'latitude' => 15.9006, 'longitude' => 120.5889],
            ['name' => 'Rosales', 'latitude' => 15.8972, 'longitude' => 120.6267],
            ['name' => 'Alcala', 'latitude' => 15.8417, 'longitude' => 120.5250],
            ['name' => 'Bautista', 'latitude' => 15.7636, 'longitude' => 120.5506],
            ['name' => 'Basista', 'latitude' => 16.0378, 'longitude' => 120.6078],
            ['name' => 'Binalonan', 'latitude' => 16.0539, 'longitude' => 120.5931],
            ['name' => 'Bugallon', 'latitude' => 15.9811, 'longitude' => 120.1850],
            ['name' => 'Aguilar', 'latitude' => 15.8833, 'longitude' => 120.2083],
            ['name' => 'Labrador', 'latitude' => 15.9744, 'longitude' => 120.0928],
            ['name' => 'Mabini', 'latitude' => 15.8939, 'longitude' => 120.0581],
            ['name' => 'Sual', 'latitude' => 16.0717, 'longitude' => 120.0961],
            ['name' => 'Dasol', 'latitude' => 15.9719, 'longitude' => 119.8739],
            ['name' => 'Infanta', 'latitude' => 15.7342, 'longitude' => 119.8583],
            ['name' => 'Tayug', 'latitude' => 16.0289, 'longitude' => 120.7494],
            ['name' => 'Natividad', 'latitude' => 16.0425, 'longitude' => 120.7944],
            ['name' => 'San Quintin', 'latitude' => 16.0542, 'longitude' => 120.7403],
            ['name' => 'San Manuel', 'latitude' => 16.0711, 'longitude' => 120.6636],
            ['name' => 'Asingan', 'latitude' => 16.0033, 'longitude' => 120.6694],
            ['name' => 'Balungao', 'latitude' => 15.8972, 'longitude' => 120.6722],
            ['name' => 'Laoac', 'latitude' => 15.9542, 'longitude' => 120.5614],
            ['name' => 'San Jacinto', 'latitude' => 15.9017, 'longitude' => 120.4694],
            ['name' => 'San Nicolas', 'latitude' => 16.0764, 'longitude' => 120.3636],
            ['name' => 'Sta. Maria', 'latitude' => 15.8083, 'longitude' => 120.6917],
            ['name' => 'Pozorrubio', 'latitude' => 16.1128, 'longitude' => 120.5464],
            ['name' => 'Sison', 'latitude' => 16.1875, 'longitude' => 120.4536],
        ];

        foreach ($municipalities as $municipality) {
            DB::table('municipalities')
                ->where('name', $municipality['name'])
                ->update([
                    'latitude' => $municipality['latitude'],
                    'longitude' => $municipality['longitude'],
                    'updated_at' => now(),
                ]);
        }

        $this->command->info('Municipality coordinates updated successfully!');
    }
}
