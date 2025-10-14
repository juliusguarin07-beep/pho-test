<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Municipality;

class UpdatePangasinanMunicipalitiesSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Complete list of Pangasinan municipalities and cities
        $municipalities = [
            'Agno',
            'Aguilar',
            'Alcala',
            'Alaminos City',
            'Anda',
            'Asingan',
            'Balungao',
            'Bani',
            'Basista',
            'Bautista',
            'Bayambang',
            'Binalonan',
            'Binmaley',
            'Bolinao',
            'Bugallon',
            'Burgos',
            'Calasiao',
            'Dagupan City',
            'Dasol',
            'Infanta',
            'Labrador',
            'Laoac',
            'Lingayen',
            'Mabini',
            'Malasiqui',
            'Manaoag',
            'Mangaldan',
            'Mangatarem',
            'Mapandan',
            'Natividad',
            'Pozorrubio',
            'Rosales',
            'San Carlos City',
            'San Fabian',
            'San Jacinto',
            'San Manuel',
            'San Nicolas',
            'San Quintin',
            'Santa Barbara',
            'Santa Maria',
            'Santo Tomas',
            'Sison',
            'Sual',
            'Tayug',
            'Umingan',
            'Urbiztondo',
            'Urdaneta City',
            'Villasis',
        ];

        foreach ($municipalities as $municipalityName) {
            Municipality::updateOrCreate(
                ['name' => $municipalityName],
                [
                    'name' => $municipalityName,
                    'code' => strtoupper(str_replace(' ', '_', $municipalityName)),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        $this->command->info('Updated ' . count($municipalities) . ' municipalities for Pangasinan province.');
    }
}
