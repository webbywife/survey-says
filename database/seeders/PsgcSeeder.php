<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PsgcSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
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
        }); // end DB::transaction
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
            'NCR-MANILA' => [],
            'ISA-SAN_MARIANO' => [
                'Alibadabad',
                'Balagan',
                'Binatug',
                'Bitabian',
                'Cadsalan',
                'Casala',
                'Cataguing',
                'Daragutan East',
                'Daragutan West',
                'Del Pilar',
                'Dibuluan',
                'Dipusu',
                'Disulap',
                'Disusuan',
                'Gangalan',
                'Ibujan',
                'Libertad',
                'Macayucayu',
                'Mallabo',
                'Marannao',
                'Minanga',
                'Old San Mariano',
                'Palutan',
                'Panninan',
                'San Jose',
                'San Pablo',
                'San Pedro',
                'Sta. Filomena',
                'Ueg',
                'Zone 2',
                'Zone 3',
            ],
            'CSU-GARCHITORENA' => [
                'Ason',
                'Bahi',
                'Barangay 1',
                'Barangay 2',
                'Barangay 3',
                'Barangay 4',
                'Binagasbasan',
                'Burabod',
                'Cagamutan',
                'Cagnipa',
                'Canlong',
                'Dangla',
                'Del Pilar',
                'Harrison',
                'Mansangat',
                'Pambuhan',
                'Sagrada',
                'Salvacion',
                'San Vicente',
                'Sumaoy',
                'Tamiawon',
                'Toytoy',
            ],
            'SUN-DAPA' => [
                'Brgy. 1',
                'Brgy. 2',
                'Brgy. 3',
                'Brgy. 4',
                'Brgy. 5',
                'Brgy. 6',
                'Brgy. 8',
                'Brgy. 9',
                'Brgy. 10',
                'Brgy. 11',
                'Brgy. 12',
                'Brgy. 13',
                'Buenavista',
                'Cabawa',
                'Cambas Ac',
                'Dagohoy',
                'Don Paulino',
                'Jubang',
                'San Carlo',
                'San Miguel',
                'Santa Felomina',
                'Union',
            ],
            'MGN-PARANG' => [
                'Bongo Island',
                'Campo Islam',
                'Cotongan',
                'Gadungan',
                'Guiday Biruar',
                'Gumagadong',
            ],
        ];
    }
}
