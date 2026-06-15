<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lokasi extends Model
{
    use SoftDeletes;

    protected $table = 'lokasi';
    protected $primaryKey = 'lokasi_id';

    protected $fillable = [
        'created_by',
        'nama_lokasi',
        'alamat',
        'provinsi',
        'kabupaten',
        'kecamatan',
        'province_id',
        'regency_id',
        'district_id',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function observasiLokasis(): HasMany
    {
        return $this->hasMany(ObservasiLokasi::class, 'lokasi_id', 'lokasi_id');
    }

    public function penilaians(): HasMany
    {
        return $this->hasMany(Penilaian::class, 'lokasi_id', 'lokasi_id');
    }
}
