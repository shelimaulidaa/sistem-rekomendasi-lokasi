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
        'periode_id',
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
        'nearest_rph_name',
        'jumlah_kompetitor',
        'akses_roda4',
        'jalan_bagus',
        'dekat_fasilitas',
        'mudah_ditemukan',
        'mudah_dijangkau',
        'bangunan_layak',
        'ventilasi_baik',
        'air_listrik_memadai',
        'luas_mencukupi',
        'parkir_memadai',
        'akses_jalan_utama',
        'akses_kendaraan_operasional',
        'kondisi_jalan_baik',
        'mudah_ditemukan_google_maps',
        'mudah_dijangkau_pelanggan',
        'luas_bangunan_mencukupi',
        'kondisi_bangunan_baik',
        'ventilasi_sirkulasi_memadai',
        'air_listrik_tersedia',
        'area_parkir_memadai',
        'catatan',
        'tanggal_observasi',
        'latitude',
        'longitude',
        'umk',
        'pdrb',
        'jumlah_penduduk_muslim',
        'jam_observasi',
        'anggota_pendamping',
    ];

    public function scopeWherePeriode($query, $periodeId)
    {
        return $query->where('periode_id', $periodeId);
    }

    public function scopeWhereNotInPeriode($query, array $periodeIds)
    {
        return $query->whereNotIn('observasi_lokasi.periode_id', $periodeIds);
    }

    public function scopeWhereInPeriode($query, array $periodeIds)
    {
        return $query->whereIn('observasi_lokasi.periode_id', $periodeIds);
    }




    protected $casts = [
        'harga_sewa' => 'float',
        'jarak_rph' => 'float',
        'jumlah_kompetitor' => 'integer',
        'latitude' => 'float',
        'longitude' => 'float',
        'akses_jalan_utama' => 'boolean',
        'akses_kendaraan_operasional' => 'boolean',
        'kondisi_jalan_baik' => 'boolean',
        'mudah_ditemukan_google_maps' => 'boolean',
        'mudah_dijangkau_pelanggan' => 'boolean',
        'luas_bangunan_mencukupi' => 'boolean',
        'kondisi_bangunan_baik' => 'boolean',
        'ventilasi_sirkulasi_memadai' => 'boolean',
        'air_listrik_tersedia' => 'boolean',
        'area_parkir_memadai' => 'boolean',
        'akses_roda4' => 'boolean',
        'jalan_bagus' => 'boolean',
        'dekat_fasilitas' => 'boolean',
        'mudah_ditemukan' => 'boolean',
        'mudah_dijangkau' => 'boolean',
        'bangunan_layak' => 'boolean',
        'ventilasi_baik' => 'boolean',
        'air_listrik_memadai' => 'boolean',
        'luas_mencukupi' => 'boolean',
        'parkir_memadai' => 'boolean',
        'tanggal_observasi' => 'date',
        'anggota_pendamping' => 'array',
    ];

    public function getAksesRoda4Attribute(): bool
    {
        return !empty($this->attributes['akses_jalan_utama']) || !empty($this->attributes['akses_roda4']);
    }
    public function getJalanBagusAttribute(): bool
    {
        return !empty($this->attributes['kondisi_jalan_baik']) || !empty($this->attributes['jalan_bagus']);
    }
    public function getDekatFasilitasAttribute(): bool
    {
        return !empty($this->attributes['akses_kendaraan_operasional']) || !empty($this->attributes['dekat_fasilitas']);
    }
    public function getMudahDitemukanAttribute(): bool
    {
        return !empty($this->attributes['mudah_ditemukan_google_maps']) || !empty($this->attributes['mudah_ditemukan']);
    }
    public function getMudahDijangkauAttribute(): bool
    {
        return !empty($this->attributes['mudah_dijangkau_pelanggan']) || !empty($this->attributes['mudah_dijangkau']);
    }
    public function getBangunanLayakAttribute(): bool
    {
        return !empty($this->attributes['kondisi_bangunan_baik']) || !empty($this->attributes['bangunan_layak']);
    }
    public function getVentilasiBaikAttribute(): bool
    {
        return !empty($this->attributes['ventilasi_sirkulasi_memadai']) || !empty($this->attributes['ventilasi_baik']);
    }
    public function getAirListrikMemadaiAttribute(): bool
    {
        return !empty($this->attributes['air_listrik_tersedia']) || !empty($this->attributes['air_listrik_memadai']);
    }
    public function getLuasMencukupiAttribute(): bool
    {
        return !empty($this->attributes['luas_bangunan_mencukupi']) || !empty($this->attributes['luas_mencukupi']);
    }
    public function getParkirMemadaiAttribute(): bool
    {
        return !empty($this->attributes['area_parkir_memadai']) || !empty($this->attributes['parkir_memadai']);
    }

    public function periode(): BelongsTo
    {
        return $this->belongsTo(Periode::class, 'periode_id');
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

    public function hasilPerhitungan(): \Illuminate\Database\Eloquent\Relations\HasOneThrough
    {
        return $this->hasOneThrough(
            HasilPerhitungan::class,
            Penilaian::class,
            'observasi_lokasi_id',
            'penilaian_id',
            'id',
            'penilaian_id'
        );
    }

    public function getAksesibilitasScoreAttribute(): int
    {
        $trues = 0;
        if ($this->akses_jalan_utama || $this->akses_roda4) $trues++;
        if ($this->kondisi_jalan_baik || $this->jalan_bagus) $trues++;
        if ($this->akses_kendaraan_operasional || $this->dekat_fasilitas) $trues++;
        if ($this->mudah_ditemukan_google_maps || $this->mudah_ditemukan) $trues++;
        if ($this->mudah_dijangkau_pelanggan || $this->mudah_dijangkau) $trues++;
        return max(1, $trues);
    }

    public function getKelayakanScoreAttribute(): int
    {
        $trues = 0;
        if ($this->luas_bangunan_mencukupi || $this->luas_mencukupi) $trues++;
        if ($this->kondisi_bangunan_baik || $this->bangunan_layak) $trues++;
        if ($this->ventilasi_sirkulasi_memadai || $this->ventilasi_baik) $trues++;
        if ($this->air_listrik_tersedia || $this->air_listrik_memadai) $trues++;
        if ($this->area_parkir_memadai || $this->parkir_memadai) $trues++;
        return max(1, $trues);
    }

    public function getCatatanAttribute($value): ?string
    {
        if (empty($value)) return null;
        $clean = trim(preg_replace('/<!--COMPETITORS_DATA:.*?-->/s', '', $value));
        return $clean !== '' ? $clean : null;
    }

    public function getSpatialDataAttribute(): array
    {
        $lat = (float) ($this->latitude ?? 0);
        $lng = (float) ($this->longitude ?? 0);
        $compCount = (int) ($this->jumlah_kompetitor ?? 0);

        $radius = (float) config('spatial.competitor_radius', 5);

        $competitorsList = [];

        // 1. Periksa apakah daftar kompetitor kustom tersemat pada kolom catatan
        $rawCatatan = $this->attributes['catatan'] ?? '';
        if (preg_match('/<!--COMPETITORS_DATA:(.*?)-->/s', $rawCatatan, $matches)) {
            $decoded = json_decode($matches[1], true);
            if (is_array($decoded) && count($decoded) > 0) {
                $competitorsList = $decoded;
            }
        }

        // 2. Jika tidak ada daftar tersemat, ambil data dari dataset spasial
        if (empty($competitorsList) && $lat != 0 && $lng != 0 && $compCount > 0) {
            $spatialService = app(\App\Services\SpatialAnalysisService::class);
            $competitorsList = $spatialService->getCompetitors($lat, $lng, $radius);
            
            // Jika tidak ditemukan dalam radius 5km, perluas pencarian untuk menemukan kompetitor terdekat
            if (empty($competitorsList)) {
                $competitorsList = $spatialService->getCompetitors($lat, $lng, 50);
            }
            
            $competitorsList = array_slice($competitorsList, 0, $compCount);
        }

        // 3. Alternatif: jika jumlah > 0 tetapi daftar masih kosong, ambil data kompetitor dari DB
        if (empty($competitorsList) && $compCount > 0) {
            $dbCompetitors = \App\Models\Competitor::take($compCount)->get();
            foreach ($dbCompetitors as $index => $item) {
                $competitorsList[] = [
                    'id' => $item->id ?? ($index + 1),
                    'nama' => $item->nama ?? ('Aqiqah ' . ($index + 1)),
                    'distance' => 1.5,
                    'rating' => $item->rating ?? 5.0,
                ];
            }
        }

        $ratings = array_filter(array_column($competitorsList, 'rating'));
        $avgRating = count($ratings) > 0 ? round(array_sum($ratings) / count($ratings), 1) : null;

        $nearestRphDistance = null;
        if ($this->jarak_rph !== null && $this->jarak_rph !== '') {
            $nearestRphDistance = rtrim(rtrim(number_format((float)$this->jarak_rph, 4, '.', ''), '0'), '.');
        }

        return [
            'nearest_rph_name' => !empty($this->nearest_rph_name) ? $this->nearest_rph_name : 'Belum ditentukan',
            'nearest_rph_distance' => $nearestRphDistance,
            'competitor_count' => count($competitorsList) ?: $compCount,
            'competitors_avg_rating' => $avgRating,
            'competitors_list' => $competitorsList,
            'search_radius' => $radius,
        ];
    }

    public function isCompleteForCalculation(): bool
    {
        $periodeId = $this->periode_id;
        if (!$periodeId) {
            return false;
        }

        $activeKriteriaKeys = \App\Models\Kriteria::where('periode_id', $periodeId)
            ->whereNotNull('kunci_observasi')
            ->pluck('kunci_observasi')
            ->toArray();

        if (in_array('jarak_rph', $activeKriteriaKeys) && $this->jarak_rph === null) {
            return false;
        }
        if (in_array('biaya_sewa', $activeKriteriaKeys) && $this->harga_sewa === null) {
            return false;
        }
        if (in_array('jumlah_kompetitor', $activeKriteriaKeys) && $this->jumlah_kompetitor === null) {
            return false;
        }
        if (in_array('aksesibilitas', $activeKriteriaKeys) && $this->aksesibilitas_score === null) {
            return false;
        }
        if (in_array('kelayakan_bangunan', $activeKriteriaKeys) && $this->kelayakan_score === null) {
            return false;
        }

        return true;
    }
}


