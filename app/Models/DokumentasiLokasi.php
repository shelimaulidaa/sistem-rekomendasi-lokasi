<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DokumentasiLokasi extends Model
{
    protected $table = 'dokumentasi_lokasi';
    protected $primaryKey = 'foto_id';

    protected $fillable = [
        'observasi_lokasi_id',
        'foto_path',
    ];

    public function observasiLokasi(): BelongsTo
    {
        return $this->belongsTo(ObservasiLokasi::class, 'observasi_lokasi_id');
    }
}
