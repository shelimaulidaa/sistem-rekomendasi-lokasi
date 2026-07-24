<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HasilPerhitungan extends Model
{
    protected $table = 'hasil_perhitungan';
    protected $primaryKey = 'hasil_id';

    protected $fillable = [
        'periode_id',
        'batch_id',
        'penilaian_id',
        'nilai_preferensi',
        'ranking',
        'tanggal_hitung',
    ];

    public function setBatchIdAttribute($value)
    {
        if (\Illuminate\Support\Facades\Schema::hasColumn($this->getTable(), 'periode_id')) {
            $this->attributes['periode_id'] = $value;
        } else {
            $this->attributes['batch_id'] = $value;
        }
    }

    public function getBatchIdAttribute()
    {
        return $this->attributes['periode_id'] ?? $this->attributes['batch_id'] ?? null;
    }

    public function setPeriodeIdAttribute($value)
    {
        if (\Illuminate\Support\Facades\Schema::hasColumn($this->getTable(), 'periode_id')) {
            $this->attributes['periode_id'] = $value;
        } else {
            $this->attributes['batch_id'] = $value;
        }
    }

    public function getPeriodeIdAttribute()
    {
        return $this->attributes['periode_id'] ?? $this->attributes['batch_id'] ?? null;
    }

    public function scopeWherePeriode($query, $periodeId)
    {
        $fk = \Illuminate\Support\Facades\Schema::hasColumn($this->getTable(), 'periode_id') ? 'periode_id' : 'batch_id';
        return $query->where($fk, $periodeId);
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


    public function batch(): BelongsTo
    {
        $fk = \Illuminate\Support\Facades\Schema::hasColumn('hasil_perhitungan', 'periode_id') ? 'periode_id' : 'batch_id';
        return $this->belongsTo(Periode::class, $fk);
    }

    public function periode(): BelongsTo
    {
        $fk = \Illuminate\Support\Facades\Schema::hasColumn('hasil_perhitungan', 'periode_id') ? 'periode_id' : 'batch_id';
        return $this->belongsTo(Periode::class, $fk);
    }


    public function penilaian(): BelongsTo
    {
        return $this->belongsTo(Penilaian::class, 'penilaian_id', 'penilaian_id');
    }
}
