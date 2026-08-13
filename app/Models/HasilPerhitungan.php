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
     * Scope untuk menyaring hasil_perhitungan berdasarkan periode_id melalui relasi:
     * HasilPerhitungan → Penilaian → ObservasiLokasi (yang memiliki periode_id)
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
     * Mengambil seluruh ID periode unik yang telah memiliki hasil perhitungan.
     */
    public static function getCalculatedPeriodeIds(): array
    {
        return self::query()
            ->join('penilaian', 'hasil_perhitungan.penilaian_id', '=', 'penilaian.penilaian_id')
            ->join('observasi_lokasi', 'penilaian.observasi_lokasi_id', '=', 'observasi_lokasi.id')
            ->select('observasi_lokasi.periode_id')
            ->distinct()
            ->pluck('observasi_lokasi.periode_id')
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
     * Mengakses periode melalui rantai relasi: Penilaian → ObservasiLokasi → Periode.
     */
    public function getPeriodeAttribute()
    {
        return $this->penilaian?->observasiLokasi?->periode;
    }

    /**
     * Mengakses periode_id melalui rantai relasi: Penilaian → ObservasiLokasi → periode_id.
     */
    public function getPeriodeIdAttribute()
    {
        return $this->penilaian?->observasiLokasi?->periode_id;
    }
}
