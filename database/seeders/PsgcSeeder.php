<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PsgcSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('psgc_barangays')->delete();
        DB::table('psgc_cities')->delete();
        DB::table('psgc_provinces')->delete();

        $provinces = $this->provinces();
        DB::table('psgc_provinces')->insert($provinces);

        $cities = [];
        foreach ($this->citiesByProvince() as $provinceCode => $list) {
            foreach ($list as [$name, $class]) {
                $slug = strtoupper(preg_replace('/[^A-Za-z0-9]+/', '_', $name));
                $cities[] = [
                    'code'          => $provinceCode . '-' . $slug,
                    'name'          => $name,
                    'province_code' => $provinceCode,
                    'city_class'    => $class,
                ];
            }
        }
        foreach (array_chunk($cities, 200) as $chunk) {
            DB::table('psgc_cities')->insert($chunk);
        }

        $barangays = $this->barangaysByCity();
        $rows = [];
        foreach ($barangays as $cityCode => $names) {
            foreach ($names as $name) {
                $slug = strtoupper(preg_replace('/[^A-Za-z0-9]+/', '_', $name));
                $rows[] = [
                    'code'      => $cityCode . '-' . $slug,
                    'name'      => $name,
                    'city_code' => $cityCode,
                ];
            }
        }
        foreach (array_chunk($rows, 200) as $chunk) {
            DB::table('psgc_barangays')->insert($chunk);
        }
    }

    private function provinces(): array
    {
        return [
            ['code' => 'NCR', 'name' => 'Metro Manila (NCR)',     'region_code' => 'NCR',   'region_name' => 'National Capital Region'],
            ['code' => 'ISA', 'name' => 'Isabela',                'region_code' => 'II',    'region_name' => 'Cagayan Valley'],
            ['code' => 'CSU', 'name' => 'Camarines Sur',          'region_code' => 'V',     'region_name' => 'Bicol Region'],
            ['code' => 'SUN', 'name' => 'Surigao del Norte',      'region_code' => 'XIII',  'region_name' => 'CARAGA'],
            ['code' => 'MGN', 'name' => 'Maguindanao del Norte',  'region_code' => 'BARMM', 'region_name' => 'Bangsamoro Autonomous Region in Muslim Mindanao'],
        ];
    }

    // Each entry: [name, class]
    private function citiesByProvince(): array
    {
        return [
            'NCR' => [
                ['Manila', 'City'],
            ],
            'ISA' => [
                ['San Mariano', 'Municipality'],
            ],
            'CSU' => [
                ['Garchitorena', 'Municipality'],
            ],
            'SUN' => [
                ['Dapa', 'Municipality'],
            ],
            'MGN' => [
                ['Parang', 'Municipality'],
            ],
        ];
    }

    // Barangays keyed by city code (provinceCode-CITYSLUG)
    private function barangaysByCity(): array
    {
        return [
            // Tondo district barangays within Manila
            'NCR-MANILA' => [
                'Tondo I',
                'Tondo II',
            ],
            'ISA-SAN_MARIANO' => [
                'Abulan',
                'Alibago',
                'Alinguigan',
                'Awallan',
                'Bacneng',
                'Balagan',
                'Baligatan',
                'Baliuag Nuevo',
                'Banquero',
                'Blos',
                'Buenavista',
                'Buyon',
                'Cabaruan',
                'Cabugao',
                'Calamagan',
                'Calao',
                'Camunatan',
                'Capirpiriwan',
                'Caquiling',
                'Carataggaman',
                'Carolina',
                'Casala',
                'Casambalangan',
                'Catayauan',
                'Concepcion',
                'Convertida',
                'Cumabao',
                'Dicarma',
                'Dipaluda',
                'Dispensacion',
                'Dummun',
                'Duroc',
                'Estacion',
                'Gaddangao',
                'Ignacio B. Jurado',
                'Ipil',
                'Iraga',
                'La Union',
                'Laurel',
                'Linao',
                'Mabini',
                'Macalauat',
                'Maligaya',
                'Manaring',
                'Manggasian',
                'Marayat',
                'Matalag',
                'Nagbabalayan',
                'Narra',
                'Nilumisu',
                'Pag-asa',
                'Palagao Norte',
                'Palagao Sur',
                'Pared',
                'Pilar',
                'Pinueg',
                'Poblacion North',
                'Poblacion South',
                'Quezon',
                'Ragan Norte',
                'Ragan Sur',
                'Ramona',
                'Rosario',
                'Salvador',
                'San Antonio',
                'San Bernardo',
                'San Francisco',
                'San Jose',
                'San Luis',
                'San Pablo',
                'San Pedro',
                'San Ramon',
                'Santa Cruz',
                'Santa Maria',
                'Santa Rosa',
                'Santo Tomas',
                'Sinippan',
                'Swag',
                'Tucalan Libres',
                'Ueg',
                'Villa Ibanez',
                'Divisoria',
            ],
            'CSU-GARCHITORENA' => [
                'Bagacay',
                'Bahay',
                'Balite',
                'Banaao',
                'Barcellona',
                'Bislig',
                'Burabod',
                'Cagbunga',
                'Cahayag',
                'Caidquid',
                'Cala',
                'Codon',
                'Comagaycay',
                'Cotmon',
                'Dagñon',
                'Del Carmen',
                'Denrica',
                'Francia',
                'Grijalvo',
                'Huyonhuyon',
                'Iraya',
                'Joroan',
                'Jupitero',
                'Kaibigan',
                'Lagundi',
                'Lañgon',
                'Lañgon East',
                'Mabini',
                'Malangcog',
                'Mayngaway',
                'Nacawayan',
                'Napawiran',
                'Odicon',
                'Ogbong',
                'Osmena',
                'Palangon',
                'Panagan',
                'Poblacion',
                'Polo',
                'Salvacion',
                'San Isidro',
                'San Jose',
                'San Juan',
                'San Ramon',
                'Santa Cruz',
                'Siramag',
                'Soledad',
                'Taisan',
                'Tigbao',
                'Tinampo',
                'Tinco',
                'Tinopan',
                'Tomalaytay',
                'Tribulan',
                'Ulag',
                'Villa Aurora',
                'Villa Belen',
            ],
            'SUN-DAPA' => [
                'Bagakay',
                'Cabugo',
                'Cagdianao',
                'Consolacion',
                'Corregidor',
                'Del Pilar',
                'General',
                'Igualdad Interior',
                'Igualdad Riverside',
                'Junction',
                'Lahi',
                'Montserrat',
                'Osmeña',
                'Poblacion',
                'Quezon',
                'San Jose',
                'Santa Fe',
                'Tagbuyawan',
                'Tigbao',
            ],
            'MGN-PARANG' => [
                'Balong',
                'Buayan',
                'Daito',
                'Damalasak',
                'Gaddong',
                'Kalumamis',
                'Kapimpilan',
                'Kibleg',
                'Kibucay',
                'Kitango',
                'Kitapok',
                'Mada',
                'Makat',
                'Malango',
                'Margues',
                'Midsayap',
                'Milagros',
                'Mileb',
                'Nabundas',
                'Nalin',
                'Nuro',
                'Pigcawaran',
                'Poblacion',
                'Pongco',
                'Sabaken',
                'Salam',
                'Simuay',
                'Sua',
                'Tambunan',
                'Tapian',
                'Tual',
            ],
        ];
    }
}
