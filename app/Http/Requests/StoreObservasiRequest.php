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
                $val = $this->input($field);
                if ($val !== null && $val !== '') {
                    $normalizedData[$field] = str_replace(',', '.', $val);
                } else {
                    $normalizedData[$field] = null;
                }
            }
        }
        
        if ($this->has('jam_observasi') && $this->input('jam_observasi')) {
            $jam = trim($this->input('jam_observasi'));
            if (strlen($jam) > 5) {
                $normalizedData['jam_observasi'] = substr($jam, 0, 5);
            }
        }

        // Alternatif nama wilayah saat mengedit observasi yang ada
        $observasiParam = $this->route('observasi');
        $observasi = $observasiParam instanceof \App\Models\ObservasiLokasi 
            ? $observasiParam 
            : ($observasiParam ? \App\Models\ObservasiLokasi::find($observasiParam) : null);

        if ($observasi) {
            if (!$this->input('provinsi') && $observasi->provinsi) {
                $normalizedData['provinsi'] = $observasi->provinsi;
            }
            if (!$this->input('kabupaten_kota') && $observasi->kabupaten_kota) {
                $normalizedData['kabupaten_kota'] = $observasi->kabupaten_kota;
            }
            if (!$this->input('kecamatan') && $observasi->kecamatan) {
                $normalizedData['kecamatan'] = $observasi->kecamatan;
            }
            if (($this->input('latitude') === null || $this->input('latitude') === '') && $observasi->latitude !== null) {
                $normalizedData['latitude'] = $observasi->latitude;
            }
            if (($this->input('longitude') === null || $this->input('longitude') === '') && $observasi->longitude !== null) {
                $normalizedData['longitude'] = $observasi->longitude;
            }
        }
        
        if (!empty($normalizedData)) {
            $this->merge($normalizedData);
        }
    }

    public function rules(): array
    {
        return [
            'periode_id' => ['nullable', 'exists:periodes,id'],

            'nama_pemilik' => ['nullable', 'string', 'max:255'],
            'nomor_telepon_pemilik' => ['nullable', 'string', 'regex:/^[0-9]+$/', 'max:50'],
            'alamat_lengkap' => ['required', 'string'],
            'provinsi' => ['required', 'string', 'max:100'],
            'kabupaten_kota' => ['required', 'string', 'max:100'],
            'kecamatan' => ['required', 'string', 'max:100'],
            'province_id' => ['nullable', 'string', 'max:10'],
            'regency_id' => ['nullable', 'string', 'max:10'],
            'district_id' => ['nullable', 'string', 'max:10'],
            'tanggal_observasi' => ['required', 'date'],
            'jam_observasi' => ['nullable', 'date_format:H:i,H:i:s'],
            'anggota_pendamping' => ['nullable', 'array'],
            'anggota_pendamping.*' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            
            // Detail Bangunan & Wilayah
            'jenis_bangunan' => ['nullable', 'string', 'max:100'],
            'kondisi_bangunan' => ['nullable', 'string', 'max:100'],
            'luas_tanah' => ['nullable', 'numeric', 'min:0'],
            'luas_bangunan' => ['nullable', 'numeric', 'min:0'],
            'jumlah_lantai' => ['nullable', 'integer', 'min:1'],
            'jumlah_ruangan' => ['nullable', 'integer', 'min:0'],
            'jumlah_wc' => ['nullable', 'integer', 'min:0'],
            'sumber_air' => ['nullable', 'string', 'max:100'],
            'daya_listrik' => ['nullable', 'string', 'max:50'],
            'area_parkir' => ['nullable', 'string', 'max:100'],
            'lebar_jalan' => ['nullable', 'string', 'max:100'],
            'ventilasi' => ['nullable', 'string', 'max:100'],
            'sirkulasi' => ['nullable', 'string', 'max:100'],
            // Input Nilai (Topsis)
            'harga_sewa' => ['nullable', 'numeric', 'min:0'],
            'jumlah_kompetitor' => ['nullable', 'integer', 'min:0'],
            'jarak_rph' => ['nullable', 'numeric', 'min:0'], // In KM
            'nearest_rph_name' => ['nullable', 'string', 'max:255'],
            'competitors_data' => ['nullable'],
            
            // Indikator Aksesibilitas
            'akses_jalan_utama' => ['nullable', 'boolean'],
            'akses_kendaraan_operasional' => ['nullable', 'boolean'],
            'kondisi_jalan_baik' => ['nullable', 'boolean'],
            'mudah_ditemukan_google_maps' => ['nullable', 'boolean'],
            'mudah_dijangkau_pelanggan' => ['nullable', 'boolean'],
            'akses_roda4' => ['nullable', 'boolean'],
            'jalan_bagus' => ['nullable', 'boolean'],
            'dekat_fasilitas' => ['nullable', 'boolean'],
            'mudah_ditemukan' => ['nullable', 'boolean'],
            'mudah_dijangkau' => ['nullable', 'boolean'],
            
            // Indikator Kelayakan Bangunan
            'luas_bangunan_mencukupi' => ['nullable', 'boolean'],
            'kondisi_bangunan_baik' => ['nullable', 'boolean'],
            'ventilasi_sirkulasi_memadai' => ['nullable', 'boolean'],
            'air_listrik_tersedia' => ['nullable', 'boolean'],
            'area_parkir_memadai' => ['nullable', 'boolean'],
            'bangunan_layak' => ['nullable', 'boolean'],
            'ventilasi_baik' => ['nullable', 'boolean'],
            'air_listrik_memadai' => ['nullable', 'boolean'],
            'luas_mencukupi' => ['nullable', 'boolean'],
            'parkir_memadai' => ['nullable', 'boolean'],
            
            'umk' => ['nullable', 'numeric'],
            'pdrb' => ['nullable', 'numeric'],
            'jumlah_penduduk_muslim' => ['nullable', 'numeric'],
            
            'catatan' => ['nullable', 'string'],
            
            // Nilai Kriteria Dinamis (Tahap 4)
            'kriteria_values' => ['nullable', 'array'],
            'kriteria_values.*' => ['nullable', 'numeric'],
            
            // Foto (Maksimal 10 file, batas 2MB per file, akan dikompresi)
            'photos' => ['nullable', 'array', 'max:10'],
            'photos.*' => ['image', 'mimes:jpeg,png,jpg,webp', 'max:2048'], // Maksimal 2MB per file
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $periodeId = $this->input('periode_id');
            if (!$periodeId) {
                $validator->errors()->add('periode_id', 'The periode_id field is required.');
                return;
            }

            if ($periodeId) {
                $periode = \App\Models\Periode::find($periodeId);
                if ($periode && ($periode->isSelesai() || $periode->isDiarsipkan())) {
                    $validator->errors()->add('periode_id', "Data observasi lokasi tidak dapat ditambahkan pada periode yang sudah selesai atau diarsipkan.");
                }

                $kriterias = \App\Models\Kriteria::where('periode_id', $periodeId)->orderBy('urutan')->get();
                $kriteriaValues = $this->input('kriteria_values', []);

                foreach ($kriterias as $kriteria) {
                    $val = $kriteriaValues[$kriteria->kriteria_id] ?? null;
                    if ($val === null || $val === '') {
                        if (!empty($kriteria->kunci_observasi)) {
                            if (in_array($kriteria->kunci_observasi, ['biaya_sewa', 'aksesibilitas', 'kelayakan_bangunan'])) {
                                continue;
                            }
                            $sourceVal = match ($kriteria->kunci_observasi) {
                                'jumlah_kompetitor' => $this->input('jumlah_kompetitor'),
                                'jarak_rph' => $this->input('jarak_rph'),
                                default => null,
                            };
                            if ($sourceVal !== null && $sourceVal !== '') {
                                continue;
                            }
                        }

                        $labelTipe = strtolower($kriteria->jenis_input) === 'scoring' ? 'Skala Likert (Scoring)' : 'Angka (Numeric)';
                        $validator->errors()->add(
                            "kriteria_values.{$kriteria->kriteria_id}",
                            "Kriteria '{$kriteria->kode_kriteria} - {$kriteria->nama_kriteria}' ({$labelTipe}) wajib diisi."
                        );
                    }
                }
            }
        });
    }

    public function attributes(): array
    {
        return [
            'nomor_telepon_pemilik' => 'Nomor Telepon Pemilik',
            'luas_tanah' => 'Luas Tanah',
            'luas_bangunan' => 'Luas Bangunan',
            'jumlah_lantai' => 'Jumlah Lantai',
            'jumlah_ruangan' => 'Jumlah Ruangan',
            'jumlah_wc' => 'Jumlah WC',
            'harga_sewa' => 'Harga Sewa',
            'jumlah_kompetitor' => 'Jumlah Kompetitor',
            'jarak_rph' => 'Jarak RPH',
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
            'umk' => 'UMK',
            'pdrb' => 'PDRB',
            'jumlah_penduduk_muslim' => 'Jumlah Penduduk Muslim',
        ];
    }

    public function messages(): array
    {
        return [
            'numeric' => ':attribute hanya boleh diisi angka.',
            'integer' => ':attribute hanya boleh diisi angka.',
            'regex' => ':attribute hanya boleh diisi angka.',

            'nomor_telepon_pemilik.regex' => 'Nomor Telepon Pemilik hanya boleh diisi angka.',
            'nomor_telepon_pemilik.numeric' => 'Nomor Telepon Pemilik hanya boleh diisi angka.',
            'nomor_telepon_pemilik.integer' => 'Nomor Telepon Pemilik hanya boleh diisi angka.',
            'luas_tanah.numeric' => 'Luas Tanah hanya boleh diisi angka.',
            'luas_tanah.integer' => 'Luas Tanah hanya boleh diisi angka.',
            'luas_bangunan.numeric' => 'Luas Bangunan hanya boleh diisi angka.',
            'luas_bangunan.integer' => 'Luas Bangunan hanya boleh diisi angka.',
            'jumlah_lantai.integer' => 'Jumlah Lantai hanya boleh diisi angka.',
            'jumlah_lantai.numeric' => 'Jumlah Lantai hanya boleh diisi angka.',
            'jumlah_ruangan.integer' => 'Jumlah Ruangan hanya boleh diisi angka.',
            'jumlah_ruangan.numeric' => 'Jumlah Ruangan hanya boleh diisi angka.',
            'jumlah_wc.integer' => 'Jumlah WC hanya boleh diisi angka.',
            'jumlah_wc.numeric' => 'Jumlah WC hanya boleh diisi angka.',
            'harga_sewa.numeric' => 'Harga Sewa hanya boleh diisi angka.',
            'harga_sewa.integer' => 'Harga Sewa hanya boleh diisi angka.',
            'jumlah_kompetitor.integer' => 'Jumlah Kompetitor hanya boleh diisi angka.',
            'jumlah_kompetitor.numeric' => 'Jumlah Kompetitor hanya boleh diisi angka.',
            'jarak_rph.numeric' => 'Jarak RPH hanya boleh diisi angka.',
            'jarak_rph.integer' => 'Jarak RPH hanya boleh diisi angka.',
            'latitude.numeric' => 'Latitude hanya boleh diisi angka.',
            'longitude.numeric' => 'Longitude hanya boleh diisi angka.',
            'umk.numeric' => 'UMK hanya boleh diisi angka.',
            'pdrb.numeric' => 'PDRB hanya boleh diisi angka.',
            'jumlah_penduduk_muslim.numeric' => 'Jumlah Penduduk Muslim hanya boleh diisi angka.',
            'kriteria_values.*.numeric' => 'Nilai kriteria hanya boleh diisi angka.',
            'kriteria_values.*.integer' => 'Nilai kriteria hanya boleh diisi angka.',
        ];
    }
}
