<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PsgcProvince;
use App\Models\PsgcCity;
use App\Models\PsgcBarangay;
use Illuminate\Support\Facades\Cache;

class PsgcController extends Controller
{
    public function provinces()
    {
        $data = Cache::remember('psgc.provinces', 86400, function () {
            return PsgcProvince::orderBy('name')
                ->get(['code', 'name', 'region_code', 'region_name'])
                ->toArray();
        });
        return response()->json($data);
    }

    public function cities(string $provinceCode)
    {
        $data = Cache::remember('psgc.cities.' . $provinceCode, 86400, function () use ($provinceCode) {
            return PsgcCity::where('province_code', $provinceCode)
                ->orderBy('name')
                ->get(['code', 'name', 'city_class'])
                ->toArray();
        });
        return response()->json($data);
    }

    public function barangays(string $cityCode)
    {
        $data = Cache::remember('psgc.barangays.' . $cityCode, 86400, function () use ($cityCode) {
            return PsgcBarangay::where('city_code', $cityCode)
                ->orderBy('name')
                ->get(['code', 'name'])
                ->toArray();
        });
        return response()->json($data);
    }
}
