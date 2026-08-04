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

    public function getTable()
    {
        return Schema::hasTable('periodes') ? 'periodes' : 'batches';
    }

    protected $fillable = [
        'nama_periode',
        'nama_batch',
        'tanggal_mulai',
        'tanggal_selesai',
        'status',
    ];

    public function setNamaBatchAttribute($value)
    {
        if (Schema::hasColumn($this->getTable(), 'nama_periode')) {
            $this->attributes['nama_periode'] = $value;
        } else {
            $this->attributes['nama_batch'] = $value;
        }
    }

    public function getNamaBatchAttribute()
    {
        return $this->attributes['nama_periode'] ?? $this->attributes['nama_batch'] ?? null;
    }

    public function setNamaPeriodeAttribute($value)
    {
        if (Schema::hasColumn($this->getTable(), 'nama_periode')) {
            $this->attributes['nama_periode'] = $value;
        } else {
            $this->attributes['nama_batch'] = $value;
        }
    }

    public function getNamaPeriodeAttribute()
    {
        return $this->attributes['nama_periode'] ?? $this->attributes['nama_batch'] ?? null;
    }

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

    public static function isBatchCalculated($periodeId): bool
    {
        return $periodeId ? HasilPerhitungan::wherePeriode($periodeId)->exists() : false;
    }



    /**
     * Get the observations for this periode.
     */
    public function observasiLokasi()
    {
        $fk = Schema::hasColumn('observasi_lokasi', 'periode_id') ? 'periode_id' : 'batch_id';
        return $this->hasMany(ObservasiLokasi::class, $fk);
    }

    public function observasiLokasis()
    {
        $fk = Schema::hasColumn('observasi_lokasi', 'periode_id') ? 'periode_id' : 'batch_id';
        return $this->hasMany(ObservasiLokasi::class, $fk);
    }

    /**
     * Get the calculation results for this periode.
     * Traverses: Periode → ObservasiLokasi → Penilaian → HasilPerhitungan
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
     * Get the criterias for this periode.
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
