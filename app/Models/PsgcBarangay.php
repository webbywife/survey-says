<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PsgcBarangay extends Model
{
    public $timestamps = false;
    protected $primaryKey = 'code';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = ['code', 'name', 'city_code'];
}
