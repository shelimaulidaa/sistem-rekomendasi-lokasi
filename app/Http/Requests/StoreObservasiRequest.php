<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreObservasiRequest extends FormRequest
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
            'batch_id' => ['required', 'exists:batches,id'],
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
            'akses_roda4' => ['nullable', 'boolean'],
            'jalan_bagus' => ['nullable', 'boolean'],
            'dekat_fasilitas' => ['nullable', 'boolean'],
            
            // Indikator Kelayakan Bangunan
            'bangunan_layak' => ['nullable', 'boolean'],
            'ventilasi_baik' => ['nullable', 'boolean'],
            'air_listrik_memadai' => ['nullable', 'boolean'],
            
            'umk' => ['nullable', 'numeric'],
            'pdrb' => ['nullable', 'numeric'],
            'jumlah_penduduk_muslim' => ['nullable', 'numeric'],
            
            'catatan' => ['nullable', 'string'],
            
            // Photos (Max 10 files, total 20MB limit handled by php.ini, max 2MB per file here)
            'photos' => ['nullable', 'array', 'max:10'],
            'photos.*' => ['image', 'mimes:jpeg,png,jpg,webp', 'max:5120'], // 5MB per file max, will be compressed
        ];
    }
}
