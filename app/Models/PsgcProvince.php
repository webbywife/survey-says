<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PsgcProvince extends Model
{
    public $timestamps = false;
    protected $primaryKey = 'code';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = ['code', 'name', 'region_code', 'region_name'];

    public function cities()
    {
        return $this->hasMany(PsgcCity::class, 'province_code', 'code')->orderBy('name');
    }
}
