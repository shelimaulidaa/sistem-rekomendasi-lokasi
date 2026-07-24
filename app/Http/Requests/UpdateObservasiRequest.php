<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateObservasiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manage observasi');
    }

    protected function prepareForValidation()
    {
        $fieldsToNormalize = ['luas_tanah', 'luas_bangunan', 'harga_sewa', 'jarak_rph'];
        
        $normalizedData = [];
        foreach ($fieldsToNormalize as $field) {
            if ($this->has($field)) {
                // Replace comma with dot for decimal values
                $normalizedData[$field] = str_replace(',', '.', $this->input($field));
            }
        }
        
        if (!empty($normalizedData)) {
            $this->merge($normalizedData);
        }
    }

    public function rules(): array
    {
        return [
            'nama_pemilik' => ['required', 'string', 'max:255'],
            'nomor_telepon_pemilik' => ['required', 'string', 'max:50'],
            'alamat_lengkap' => ['required', 'string'],
            'provinsi' => ['required', 'string', 'max:100'],
            'kabupaten_kota' => ['required', 'string', 'max:100'],
            'kecamatan' => ['required', 'string', 'max:100'],
            'province_id' => ['nullable', 'string', 'max:10'],
            'regency_id' => ['nullable', 'string', 'max:10'],
            'district_id' => ['nullable', 'string', 'max:10'],
            'tanggal_observasi' => ['required', 'date'],
            'jam_observasi' => ['nullable', 'date_format:H:i'],
            'anggota_pendamping' => ['nullable', 'array'],
            'anggota_pendamping.*' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            
            // Detail Bangunan & Wilayah
            'jenis_bangunan' => ['required', 'string', 'max:100'],
            'kondisi_bangunan' => ['required', 'string', 'max:100'],
            'luas_tanah' => ['required', 'numeric', 'min:0'],
            'luas_bangunan' => ['required', 'numeric', 'min:0'],
            'jumlah_lantai' => ['required', 'integer', 'min:1'],
            'jumlah_ruangan' => ['required', 'integer', 'min:0'],
            'jumlah_wc' => ['required', 'integer', 'min:0'],
            'sumber_air' => ['required', 'string', 'max:100'],
            'daya_listrik' => ['required', 'string', 'max:50'],
            'area_parkir' => ['required', 'string', 'max:100'],
            'lebar_jalan' => ['required', 'string', 'max:100'],
            'ventilasi' => ['required', 'string', 'max:100'],
            'sirkulasi' => ['required', 'string', 'max:100'],
            // Input Nilai (Topsis)
            'harga_sewa' => ['required', 'numeric', 'min:0'],
            'jumlah_kompetitor' => ['required', 'integer', 'min:0'],
            'jarak_rph' => ['required', 'numeric', 'min:0'], // In KM
            
            // Indikator Aksesibilitas
            'akses_jalan_utama' => ['nullable', 'boolean'],
            'akses_kendaraan_operasional' => ['nullable', 'boolean'],
            'kondisi_jalan_baik' => ['nullable', 'boolean'],
            'mudah_ditemukan_google_maps' => ['nullable', 'boolean'],
            'mudah_dijangkau_pelanggan' => ['nullable', 'boolean'],
            
            // Indikator Kelayakan Bangunan
            'luas_bangunan_mencukupi' => ['nullable', 'boolean'],
            'kondisi_bangunan_baik' => ['nullable', 'boolean'],
            'ventilasi_sirkulasi_memadai' => ['nullable', 'boolean'],
            'air_listrik_tersedia' => ['nullable', 'boolean'],
            'area_parkir_memadai' => ['nullable', 'boolean'],
            
            'umk' => ['nullable', 'numeric'],
            'pdrb' => ['nullable', 'numeric'],
            'jumlah_penduduk_muslim' => ['nullable', 'numeric'],
            
            'catatan' => ['nullable', 'string'],
            
            // Delete Photos
            'delete_photos' => ['nullable', 'array'],
            'delete_photos.*' => ['integer', 'exists:dokumentasi_lokasi,foto_id'],

            // Photos
            'photos' => ['nullable', 'array', 'max:10'],
            'photos.*' => ['image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
        ];
    }
}
