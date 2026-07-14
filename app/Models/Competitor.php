<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Competitor extends Model
{
    protected $fillable = [
        'nama',
        'alamat',
        'provinsi',
        'kabupaten_kota',
        'kecamatan',
        'latitude',
        'longitude',
    ];
}
