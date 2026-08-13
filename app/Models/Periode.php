<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Schema;

class Periode extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'periodes';

    protected $fillable = [
        'nama_periode',
        'tanggal_mulai',
        'tanggal_selesai',
        'status',
    ];

    const STATUS_DRAFT = 'Draft';
    const STATUS_AKTIF = 'Aktif';
    const STATUS_SELESAI = 'Selesai';
    const STATUS_DIARSIPKAN = 'Diarsipkan';

    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function isSelesai(): bool
    {
        return $this->status === self::STATUS_SELESAI;
    }

    public function isDiarsipkan(): bool
    {
        return $this->status === self::STATUS_DIARSIPKAN;
    }

    public function isCalculated(): bool
    {
        return HasilPerhitungan::wherePeriode($this->id)->exists();
    }

    public static function isPeriodeCalculated($periodeId): bool
    {
        return $periodeId ? HasilPerhitungan::wherePeriode($periodeId)->exists() : false;
    }



    /**
     * Mengambil data observasi lokasi untuk periode ini.
     */
    public function observasiLokasi()
    {
        return $this->hasMany(ObservasiLokasi::class, 'periode_id');
    }

    public function observasiLokasis()
    {
        return $this->hasMany(ObservasiLokasi::class, 'periode_id');
    }

    /**
     * Mengambil hasil perhitungan TOPSIS untuk periode ini.
     */
    public function hasilPerhitungan()
    {
        return HasilPerhitungan::whereHas('penilaian.observasiLokasi', function ($q) {
            $q->wherePeriode($this->id);
        });
    }

    public function hasilPerhitungans()
    {
        return $this->hasilPerhitungan();
    }

    /**
     * Mengambil data kriteria untuk periode ini.
     */
    public function kriterias(): HasMany
    {
        return $this->hasMany(Kriteria::class, 'periode_id');
    }

    public function kriteria(): HasMany
    {
        return $this->hasMany(Kriteria::class, 'periode_id');
    }
}
