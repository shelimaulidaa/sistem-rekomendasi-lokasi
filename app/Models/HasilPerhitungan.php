<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HasilPerhitungan extends Model
{
    protected $table = 'hasil_perhitungan';
    protected $primaryKey = 'hasil_id';

    protected $fillable = [
        'penilaian_id',
        'nilai_preferensi',
        'ranking',
        'tanggal_hitung',
    ];

    /**
     * Scope to filter hasil_perhitungan by periode_id through the relation chain:
     * HasilPerhitungan → Penilaian → ObservasiLokasi (which has periode_id)
     */
    public function scopeWherePeriode($query, $periodeId)
    {
        return $query->whereHas('penilaian', function ($q) use ($periodeId) {
            $q->whereHas('observasiLokasi', function ($q2) use ($periodeId) {
                $q2->wherePeriode($periodeId);
            });
        });
    }

    /**
     * Get all distinct periode IDs that have calculation results.
     * Replaces the old pattern: HasilPerhitungan::select('periode_id')->distinct()->pluck()
     */
    public static function getCalculatedPeriodeIds(): array
    {
        $fk = \Illuminate\Support\Facades\Schema::hasColumn('observasi_lokasi', 'periode_id') ? 'periode_id' : 'batch_id';

        return self::query()
            ->join('penilaian', 'hasil_perhitungan.penilaian_id', '=', 'penilaian.penilaian_id')
            ->join('observasi_lokasi', 'penilaian.observasi_lokasi_id', '=', 'observasi_lokasi.id')
            ->select('observasi_lokasi.' . $fk)
            ->distinct()
            ->pluck('observasi_lokasi.' . $fk)
            ->filter()
            ->values()
            ->toArray();
    }


    protected $casts = [
        'nilai_preferensi' => 'float',
        'ranking' => 'integer',
        'tanggal_hitung' => 'datetime',
    ];

    public function isTopRank(): bool
    {
        return $this->ranking === 1;
    }


    public function penilaian(): BelongsTo
    {
        return $this->belongsTo(Penilaian::class, 'penilaian_id', 'penilaian_id');
    }

    /**
     * Access the periode through the relation chain: Penilaian → ObservasiLokasi → Periode.
     * Usage: $hasil->periode (returns Periode model or null)
     */
    public function getPeriodeAttribute()
    {
        return $this->penilaian?->observasiLokasi?->periode;
    }

    /**
     * Access the periode_id through the relation chain: Penilaian → ObservasiLokasi → periode_id.
     * Usage: $hasil->periode_id (returns int or null)
     */
    public function getPeriodeIdAttribute()
    {
        return $this->penilaian?->observasiLokasi?->periode_id;
    }

    /**
     * Backward compatibility: batch_id accessor maps to periode_id via relation chain.
     */
    public function getBatchIdAttribute()
    {
        return $this->periode_id;
    }
}
