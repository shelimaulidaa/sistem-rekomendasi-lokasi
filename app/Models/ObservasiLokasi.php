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

    protected $fillable = [
        'batch_id',
        'user_id',
        'nama_pemilik',
        'nomor_telepon_pemilik',
        'alamat_lengkap',
        'provinsi',
        'kabupaten_kota',
        'kecamatan',
        'province_id',
        'regency_id',
        'district_id',
        'jenis_bangunan',
        'kondisi_bangunan',
        'luas_tanah',
        'luas_bangunan',
        'jumlah_lantai',
        'jumlah_ruangan',
        'jumlah_wc',
        'sumber_air',
        'daya_listrik',
        'area_parkir',
        'lebar_jalan',
        'ventilasi',
        'sirkulasi',
        'harga_sewa',
        'jarak_rph',
        'jumlah_kompetitor',
        'akses_roda4',
        'jalan_bagus',
        'dekat_fasilitas',
        'bangunan_layak',
        'ventilasi_baik',
        'air_listrik_memadai',
        'catatan',
        'tanggal_observasi',
        'latitude',
        'longitude',
        'umk',
        'pdrb',
        'jumlah_penduduk_muslim',
    ];

    protected $casts = [
        'akses_roda4' => 'boolean',
        'jalan_bagus' => 'boolean',
        'dekat_fasilitas' => 'boolean',
        'bangunan_layak' => 'boolean',
        'ventilasi_baik' => 'boolean',
        'air_listrik_memadai' => 'boolean',
        'tanggal_observasi' => 'date',
    ];

    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class, 'batch_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function dokumentasiLokasis(): HasMany
    {
        return $this->hasMany(DokumentasiLokasi::class, 'observasi_lokasi_id');
    }

    public function penilaians(): HasMany
    {
        return $this->hasMany(Penilaian::class, 'observasi_lokasi_id');
    }
}
