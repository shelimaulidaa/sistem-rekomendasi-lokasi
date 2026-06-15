<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ObservasiLokasi extends Model
{
    use SoftDeletes;

    protected $table = 'observasi_lokasi';
    protected $primaryKey = 'observasi_id';

    protected $fillable = [
        'lokasi_id',
        'user_id',
        'jenis_bangunan',
        'luas_tanah',
        'luas_bangunan',
        'jumlah_ruangan',
        'jumlah_wc',
        'listrik',
        'sumber_air',
        'harga_sewa',
        'jarak_rph',
        'jumlah_kompetitor',
        'akses_roda4',
        'jalan_bagus',
        'dekat_fasilitas',
        'bangunan_layak',
        'ventilasi_baik',
        'air_listrik_memadai',
        'kepadatan_penduduk',
        'tahun_bps',
        'kode_wilayah_bps',
        'catatan',
        'tanggal_observasi',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'listrik' => 'boolean',
        'akses_roda4' => 'boolean',
        'jalan_bagus' => 'boolean',
        'dekat_fasilitas' => 'boolean',
        'bangunan_layak' => 'boolean',
        'ventilasi_baik' => 'boolean',
        'air_listrik_memadai' => 'boolean',
        'tanggal_observasi' => 'date',
    ];

    public function lokasi(): BelongsTo
    {
        return $this->belongsTo(Lokasi::class, 'lokasi_id', 'lokasi_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function dokumentasiLokasis(): HasMany
    {
        return $this->hasMany(DokumentasiLokasi::class, 'observasi_id', 'observasi_id');
    }

    public function penilaians(): HasMany
    {
        return $this->hasMany(Penilaian::class, 'observasi_id', 'observasi_id');
    }
}
