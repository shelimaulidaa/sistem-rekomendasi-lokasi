<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Batch extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nama_batch',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function observasiLokasis()
    {
        return $this->hasMany(ObservasiLokasi::class);
    }

    public function hasilPerhitungans()
    {
        return $this->hasMany(HasilPerhitungan::class);
    }
}
