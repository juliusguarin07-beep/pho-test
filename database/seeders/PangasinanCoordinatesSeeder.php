<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Municipality;

class PangasinanCoordinatesSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Pangasinan municipalities with their accurate coordinates
        $municipalityCoordinates = [
            'Agno' => ['latitude' => 16.1083, 'longitude' => 119.7833],
            'Aguilar' => ['latitude' => 15.8964, 'longitude' => 120.4314],
            'Alcala' => ['latitude' => 15.8500, 'longitude' => 120.5333],
            'Alaminos City' => ['latitude' => 16.1556, 'longitude' => 119.9808],
            'Anda' => ['latitude' => 16.2333, 'longitude' => 119.9667],
            'Asingan' => ['latitude' => 16.0000, 'longitude' => 120.6667],
            'Balungao' => ['latitude' => 15.8833, 'longitude' => 120.6833],
            'Bani' => ['latitude' => 16.1833, 'longitude' => 119.8333],
            'Basista' => ['latitude' => 16.2167, 'longitude' => 120.4333],
            'Bautista' => ['latitude' => 15.7833, 'longitude' => 120.5000],
            'Bayambang' => ['latitude' => 15.8167, 'longitude' => 120.4500],
            'Binalonan' => ['latitude' => 16.0500, 'longitude' => 120.6000],
            'Binmaley' => ['latitude' => 16.0333, 'longitude' => 120.2667],
            'Bolinao' => ['latitude' => 16.3833, 'longitude' => 119.8833],
            'Bugallon' => ['latitude' => 15.9667, 'longitude' => 120.1833],
            'Burgos' => ['latitude' => 15.7167, 'longitude' => 120.5833],
            'Calasiao' => ['latitude' => 16.0167, 'longitude' => 120.3667],
            'Dagupan City' => ['latitude' => 16.0433, 'longitude' => 120.3336],
            'Dasol' => ['latitude' => 16.2167, 'longitude' => 119.8833],
            'Infanta' => ['latitude' => 16.0000, 'longitude' => 119.7500],
            'Labrador' => ['latitude' => 15.9167, 'longitude' => 120.1333],
            'Laoac' => ['latitude' => 16.0833, 'longitude' => 120.4833],
            'Lingayen' => ['latitude' => 16.0191, 'longitude' => 120.2297],
            'Mabini' => ['latitude' => 15.9333, 'longitude' => 120.0667],
            'Malasiqui' => ['latitude' => 15.8833, 'longitude' => 120.4000],
            'Manaoag' => ['latitude' => 16.0500, 'longitude' => 120.4833],
            'Mangaldan' => ['latitude' => 16.0667, 'longitude' => 120.4000],
            'Mangatarem' => ['latitude' => 15.7667, 'longitude' => 120.2833],
            'Mapandan' => ['latitude' => 15.9333, 'longitude' => 120.4167],
            'Natividad' => ['latitude' => 15.9333, 'longitude' => 120.6167],
            'Pozorrubio' => ['latitude' => 16.1167, 'longitude' => 120.5500],
            'Rosales' => ['latitude' => 15.8833, 'longitude' => 120.6167],
            'San Carlos City' => ['latitude' => 15.9325, 'longitude' => 120.3444],
            'San Fabian' => ['latitude' => 16.1167, 'longitude' => 120.4000],
            'San Jacinto' => ['latitude' => 15.8833, 'longitude' => 120.3000],
            'San Manuel' => ['latitude' => 15.8000, 'longitude' => 120.6333],
            'San Nicolas' => ['latitude' => 15.7667, 'longitude' => 120.4833],
            'San Quintin' => ['latitude' => 15.8667, 'longitude' => 120.5167],
            'Santa Barbara' => ['latitude' => 15.7833, 'longitude' => 120.4167],
            'Santa Maria' => ['latitude' => 15.9333, 'longitude' => 120.1833],
            'Santo Tomas' => ['latitude' => 15.8167, 'longitude' => 120.3833],
            'Sison' => ['latitude' => 16.1667, 'longitude' => 120.6333],
            'Sual' => ['latitude' => 16.1000, 'longitude' => 120.1000],
            'Tayug' => ['latitude' => 15.7667, 'longitude' => 120.7333],
            'Umingan' => ['latitude' => 15.7167, 'longitude' => 120.6833],
            'Urbiztondo' => ['latitude' => 16.1500, 'longitude' => 120.3167],
            'Urdaneta City' => ['latitude' => 15.9761, 'longitude' => 120.5711],
            'Villasis' => ['latitude' => 15.9000, 'longitude' => 120.5833],
        ];

        foreach ($municipalityCoordinates as $name => $coordinates) {
            Municipality::where('name', $name)->update([
                'latitude' => $coordinates['latitude'],
                'longitude' => $coordinates['longitude'],
            ]);
        }

        $this->command->info('Updated coordinates for ' . count($municipalityCoordinates) . ' Pangasinan municipalities.');
    }
}
