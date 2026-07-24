<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailPenilaian extends Model
{
    protected $table = 'detail_penilaian';
    protected $primaryKey = 'detail_id';

    protected $fillable = [
        'penilaian_id',
        'kriteria_id',
        'nilai',
    ];

    protected $casts = [
        'nilai' => 'float',
    ];


    public function penilaian(): BelongsTo
    {
        return $this->belongsTo(Penilaian::class, 'penilaian_id', 'penilaian_id');
    }

    public function kriteria(): BelongsTo
    {
        return $this->belongsTo(Kriteria::class, 'kriteria_id', 'kriteria_id');
    }
}
