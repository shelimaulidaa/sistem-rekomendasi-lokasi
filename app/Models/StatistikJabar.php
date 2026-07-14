<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatistikJabar extends Model
{
    protected $table = 'statistik_jabars';
    
    protected $fillable = [
        'provinsi',
        'kabupaten_kota',
        'kecamatan',
        'umk',
        'pdrb_per_capita',
        'jumlah_penduduk_muslim',
    ];
}
