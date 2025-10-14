<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Municipality;
use App\Models\Barangay;
use App\Models\Facility;
use App\Models\Disease;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create roles
        $pesuAdmin = Role::create(['name' => 'pesu_admin']);
        $validator = Role::create(['name' => 'validator']);
        $encoder = Role::create(['name' => 'encoder']);

        // Create permissions
        $permissions = [
            'view case reports',
            'create case reports',
            'edit case reports',
            'delete case reports',
            'validate case reports',
            'approve case reports',
            'manage users',
            'manage facilities',
            'manage diseases',
            'create outbreak alerts',
            'view audit logs',
            'export reports',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Assign permissions to roles
        $pesuAdmin->givePermissionTo(Permission::all());
        $validator->givePermissionTo(['view case reports', 'validate case reports', 'export reports']);
        $encoder->givePermissionTo(['view case reports', 'create case reports', 'edit case reports']);

        // Create municipalities (Pangasinan)
        $municipalities = [
            ['name' => 'Alaminos City', 'code' => '155501'],
            ['name' => 'San Carlos City', 'code' => '155502'],
            ['name' => 'Dagupan City', 'code' => '155503'],
            ['name' => 'Urdaneta City', 'code' => '155504'],
            ['name' => 'Lingayen', 'code' => '155524'],
            ['name' => 'Mangaldan', 'code' => '155528'],
            ['name' => 'Binmaley', 'code' => '155515'],
            ['name' => 'Calasiao', 'code' => '155519'],
        ];

        foreach ($municipalities as $municipality) {
            Municipality::create($municipality);
        }

        // Create sample barangays for all municipalities
        $municipalityBarangays = [
            'Alaminos City' => [
                'Alos', 'Amandiego', 'Amangbangan', 'Balangobong', 'Balayang', 'Baleyadaan',
                'Bisocol', 'Bolaney', 'Bued', 'Cabatuan', 'Cayucay', 'Dulacac', 'Inerangan',
                'Landoc', 'Linmansangan', 'Lucap', 'Maawi', 'Macatiw', 'Magsaysay', 'Mona',
                'Palamis', 'Pandan', 'Pangapisan', 'Poblacion', 'Pocal-pocal', 'Pogo', 'Polo',
                'Quibuar', 'Sabangan', 'San Antonio', 'San Jose', 'San Roque', 'San Vicente',
                'Santa Maria', 'Tanaytay', 'Tangcarang', 'Tawin-tawin', 'Telbang', 'Victoria'
            ],
            'Binmaley' => [
                'Amancoro', 'Balagan', 'Balogo', 'Basing', 'Baybay Lopez', 'Baybay Polong',
                'Biec', 'Buenlag', 'Calit', 'Caloocan Dupo', 'Caloocan Norte', 'Caloocan Sur',
                'Camaley', 'Canaoalan', 'Dulag', 'Gayaman', 'Linoc', 'Lomboy', 'Malindong',
                'Manat', 'Nagpalangan', 'Naguilayan', 'Pallas', 'Papagueyan', 'Parayao',
                'Pangapisan Norte', 'Pangapisan Sur', 'Poblacion', 'Pototan', 'Sabangan',
                'Salapingao', 'San Isidro Norte', 'San Isidro Sur', 'Santa Rosa', 'Taloctoc', 'Tombor'
            ],
            'Calasiao' => [
                'Ambonao', 'Ambuetel', 'Banaoang', 'Bued', 'Buenlag', 'Cabilocaan', 'Dinalaoan',
                'Doyong', 'Gabon', 'Lasip', 'Longos', 'Lumbang', 'Macabito', 'Malabago',
                'Mancup', 'Nagsaing', 'Nalsian', 'Poblacion East', 'Poblacion West', 'Quesban',
                'San Miguel', 'San Vicente', 'Songkoy', 'Talibaew'
            ],
            'Dagupan City' => [
                'Bacayao Norte', 'Bacayao Sur', 'Barangay I (T. Bugallon)', 'Barangay II (Nueva)',
                'Barangay IV (Zamora)', 'Bolosan', 'Bonuan Binloc', 'Bonuan Boquig', 'Bonuan Gueset',
                'Calmay', 'Carael', 'Caranglaan', 'Herrero', 'Lasip Chico', 'Lasip Grande',
                'Lomboy', 'Lucao', 'Malued', 'Mamalingling', 'Mangin', 'Mayombo', 'Pantal',
                'Poblacion Oeste', 'Pogo Chico', 'Pogo Grande', 'Pugaro Suit', 'Salapingao',
                'Salisay', 'Tambac', 'Tapuac', 'Tebeng'
            ],
            'Lingayen' => [
                'Aliwekwek', 'Baay', 'Balangobong', 'Balococ', 'Balolang', 'Banmabolo',
                'Bantayan', 'Basing', 'Capandanan', 'Domalandan Center', 'Domalandan East',
                'Domalandan West', 'Dorongan', 'Dulag', 'Estanza', 'Lasip', 'Libsong East',
                'Libsong West', 'Malawa', 'Malimpuec', 'Maniboc', 'Matalava', 'Naguelguel',
                'Namolan', 'Pangapisan North', 'Pangapisan Sur', 'Poblacion', 'Quibaol',
                'Rosario', 'Sabangan', 'Talogtog', 'Tonton', 'Tumbar', 'Wawa'
            ],
            'Mangaldan' => [
                'Alitaya', 'Amansabina', 'Anolid', 'Banaoang', 'Bantayan', 'Bari', 'Bateng',
                'Buenlag', 'David', 'Embarcadero', 'Guiguilonen', 'Guesang', 'Lanas', 'Landas',
                'Maasin', 'Macayug', 'Malabago', 'Mangaldan Proper', 'Nagkaguilingan', 'Palua',
                'Pogo', 'Poblacion', 'Salay', 'Salay East', 'Salay West', 'San Agustin',
                'Sinabaan', 'Talogtog', 'Tombod', 'Torres'
            ],
            'San Carlos City' => [
                'Abanon', 'Agdao', 'Anando', 'Ano', 'Antipangol', 'Aponit', 'Bacnar', 'Balaya',
                'Balayong', 'Baldog', 'Balite Sur', 'Balococ', 'Bani', 'Bega', 'Bocboc',
                'Bogaoan', 'Bolingit', 'Bolosan', 'Bonifacio', 'Buenglat', 'Bugallon-Posadas Street',
                'Burgos-Padlan', 'Cacaritan', 'Caingal', 'Calobaoan', 'Calomboyan', 'Caoayan-Kiling',
                'Capataan', 'Cobol', 'Coliling', 'Cruz', 'Doyong', 'Gamata', 'Guelew', 'Ilang',
                'Inerangan', 'Isla', 'Libas', 'Lilimasan', 'Longos', 'Lucban', 'M. Soriano',
                'Mabalbalino', 'Mabini', 'Magtaking', 'Malacañang', 'Maliwara', 'Mamarlao',
                'Manzon', 'Matagdem', 'Mestizo Norte', 'Naguilayan', 'Nilentap', 'Padilla-Gomez',
                'Pagal', 'Paitan-Panoypoy', 'Palaming', 'Palaris', 'Palospos', 'Pangalangan',
                'Pangoloan', 'Pangpang', 'Parayao', 'Payapa', 'Payar', 'Perez Boulevard',
                'PNR Station Site', 'Poblacion', 'Polo', 'Puli', 'Quezon Boulevard', 'Quintong',
                'Rizal Boulevard', 'Roxas Boulevard', 'Salinap', 'San Juan', 'San Pedro',
                'San Vicente', 'Sancagulis', 'Tandoc', 'Talang', 'Tamayo', 'Tarece', 'Tiñgu',
                'Turac', 'Zone I', 'Zone II', 'Zone III', 'Zone IV', 'Zone V', 'Zone VI'
            ],
            'Urdaneta City' => [
                'Anonas', 'Bactad East', 'Bayaoas', 'Bolaoen', 'Cabaruan', 'Cabuloan',
                'Camanang', 'Camantiles', 'Casantaan', 'Catablan', 'Cayambanan', 'Consolacion',
                'Dilan-Paurido', 'Labit East', 'Labit West', 'Nancamaliran East', 'Nancamaliran West',
                'Nancayasan', 'Oltama', 'Palina East', 'Palina West', 'Pinmaludpod', 'Poblacion',
                'San Jose', 'San Vicente', 'Santa Lucia', 'Santo Domingo', 'Santo Niño',
                'Sugcong', 'Tabuyoc', 'Talugtug', 'Tiposu', 'Tulong', 'Urdaneta Village'
            ]
        ];

        foreach ($municipalityBarangays as $municipalityName => $barangays) {
            $municipality = Municipality::where('name', $municipalityName)->first();
            if ($municipality) {
                foreach ($barangays as $barangayName) {
                    Barangay::create([
                        'municipality_id' => $municipality->id,
                        'name' => $barangayName,
                    ]);
                }
            }
        }

        // Clear existing facilities
        Facility::truncate();

        // Call the comprehensive facility seeder
        $this->call(FacilitySeeder::class);

        // Create diseases
        $contagiousDiseases = [
            'Dengue', 'Leptospirosis', 'Typhoid Fever', 'Cholera',
            'Measles', 'COVID-19', 'Tuberculosis', 'Rabies',
        ];

        $nonContagiousDiseases = [
            'Acute Bloody Diarrhea', 'Acute Encephalitis Syndrome',
            'Chicken Pox', 'Diarrhea', 'Influenza-like Illness',
            'Pneumonia', 'Food Poisoning',
        ];

        foreach ($contagiousDiseases as $disease) {
            Disease::create([
                'name' => $disease,
                'category' => 'Contagious',
            ]);
        }

        foreach ($nonContagiousDiseases as $disease) {
            Disease::create([
                'name' => $disease,
                'category' => 'Non-Contagious',
            ]);
        }

        // Create PESU Super Admin
        $pesuUser = User::create([
            'name' => 'PESU Administrator',
            'email' => 'pesu@pangasinan.gov.ph',
            'password' => Hash::make('password'),
            'position' => 'Provincial Epidemiologist',
            'contact_number' => '0917-123-4567',
            'user_type' => 'pesu_admin',
            'is_active' => true,
        ]);
        $pesuUser->assignRole('pesu_admin');

        // Create District Hospital Validator
        $validatorUser = User::create([
            'name' => 'Dr. Juan Dela Cruz',
            'email' => 'validator@rimc.gov.ph',
            'password' => Hash::make('password'),
            'facility_id' => 2,
            'municipality_id' => $dagupan->id,
            'position' => 'Medical Officer III',
            'contact_number' => '0918-234-5678',
            'user_type' => 'validator',
            'is_active' => true,
        ]);
        $validatorUser->assignRole('validator');

        // Create Encoder
        $encoderUser = User::create([
            'name' => 'Maria Santos',
            'email' => 'encoder@dcho.gov.ph',
            'password' => Hash::make('password'),
            'facility_id' => 1,
            'municipality_id' => $dagupan->id,
            'position' => 'Disease Surveillance Officer',
            'contact_number' => '0919-345-6789',
            'user_type' => 'encoder',
            'is_active' => true,
        ]);
        $encoderUser->assignRole('encoder');
    }
}
