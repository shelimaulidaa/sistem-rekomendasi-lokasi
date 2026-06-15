<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-base-dark leading-tight">
            {{ __('Buat Observasi Lokasi Baru') }}
        </h2>
    </x-slot>

    <div class="max-w-5xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <!-- Error Summaries -->
        @if ($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-lg shadow-sm">
            <div class="flex items-center mb-2">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span class="font-bold text-sm">Ada kesalahan dalam pengisian form:</span>
            </div>
            <ul class="list-disc list-inside text-sm ml-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('manajer.observasi.store') }}" enctype="multipart/form-data"
              x-data="{
                  isLoadingBps: false,
                  bpsError: '',
                  kepadatanRaw: '{{ old('kepadatan_penduduk') }}',
                  hargaSewaRaw: '{{ old('harga_sewa') }}',
                  jarakRphDisplay: '{{ old('jarak_rph') }}',
                  
                  formatThousand(val) {
                      if (!val && val !== 0 && val !== '0') return '';
                      let numberString = val.toString().replace(/[^0-9]/g, '');
                      return numberString.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                  },
                  
                  async fetchBpsData(lokasiId) {
                      if (!lokasiId) return;
                      this.isLoadingBps = true;
                      this.bpsError = '';
                      try {
                          let response = await fetch('/api/wilayah/kepadatan-by-lokasi/' + lokasiId);
                          let result = await response.json();
                          if (result.success && result.data) {
                              this.kepadatanRaw = Math.round(result.data.kepadatan).toString();
                              document.getElementById('tahun_bps').value = result.data.tahun;
                              document.getElementById('kode_wilayah_bps').value = result.data.kode_bps;
                          } else {
                              this.bpsError = result.message || 'Gagal mengambil data BPS.';
                          }
                      } catch (e) {
                          this.bpsError = 'Gagal menghubungi server BPS.';
                      } finally {
                          this.isLoadingBps = false;
                      }
                  }
              }"
              x-init="fetchBpsData('{{ $lokasi->lokasi_id }}')">
            @csrf

            <!-- Section 1: Data Utama -->
            <div class="bg-white overflow-hidden shadow-sm border border-gray-100 sm:rounded-xl mb-6">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.243-4.243a8 8 0 1111.314 0z"></path></svg>
                    <h3 class="text-lg font-bold text-base-dark">Informasi Utama</h3>
                </div>
                <div class="p-4 sm:p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-base-dark mb-1">Lokasi (Alternatif)</label>
                        <input type="hidden" name="lokasi_id" value="{{ $lokasi->lokasi_id }}">
                        <div class="bg-gray-50 p-4 rounded-md border border-gray-200 h-full shadow-inner">
                            <div class="font-bold text-base-dark text-lg">{{ $lokasi->nama_lokasi }}</div>
                            <div class="text-sm text-gray-600 mt-1 flex items-start">
                                <svg class="w-4 h-4 mr-1 mt-0.5 flex-shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.243-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                <span>{{ $lokasi->alamat }}</span>
                            </div>
                            <div class="text-sm text-gray-500 mt-1 pl-5">
                                {{ ucwords(strtolower($lokasi->kecamatan)) }}, {{ ucwords(strtolower($lokasi->kabupaten)) }}
                            </div>
                        </div>
                    </div>
                    <div>
                        <label for="tanggal_observasi" class="block text-sm font-medium text-base-dark mb-1">Tanggal Observasi</label>
                        <input id="tanggal_observasi" type="text" onfocus="(this.type='date')" onblur="(this.type='text')" name="tanggal_observasi" value="{{ old('tanggal_observasi', date('Y-m-d')) }}" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm py-2 px-3 min-h-[44px]">
                    </div>
                </div>
            </div>

            <!-- Section 2: Koordinat Peta Observasi Aktual -->
            <div class="bg-white overflow-hidden shadow-sm border border-gray-100 sm:rounded-xl mb-6">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.243-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    <h3 class="text-lg font-bold text-base-dark">Koordinat Peta Observasi Aktual</h3>
                </div>
                <div class="p-4 sm:p-6">
                    <div x-data="locationMap()" class="space-y-4">
                        <div class="flex flex-col md:flex-row items-center justify-between gap-3 md:gap-2">
                            <div>
                                <h3 class="text-sm font-bold text-base-dark flex items-center">
                                    Pilih Titik Lokasi
                                    <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200">
                                        Opsional
                                    </span>
                                </h3>
                                <p class="text-xs text-base-medium mt-1">Anda dapat memilih titik lokasi aktual pada peta saat observasi lapangan dilakukan.</p>
                            </div>
                            <div class="flex flex-wrap items-center gap-2">
                                <button type="button" @click="getCurrentLocation" class="w-full sm:w-auto inline-flex justify-center items-center px-3 py-2 min-h-[44px] bg-white border border-gray-300 rounded-md text-xs font-semibold text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition-colors">
                                    <svg class="w-4 h-4 mr-1.5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.243-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    Gunakan Lokasi Saat Ini
                                </button>
                            </div>
                        </div>
                        
                        <!-- Map Container -->
                        <div class="relative w-full rounded-lg border border-gray-300 shadow-sm overflow-hidden h-[300px] md:h-[350px]" id="map-container" wire:ignore>
                            <div id="map" class="w-full h-full z-0 relative"></div>
                        </div>

                        <p class="text-xs text-amber-600 font-medium">💡 Helper: Koordinat akan terisi otomatis setelah Anda memilih lokasi pada peta atau menggunakan lokasi saat ini.</p>

                        <!-- Coordinate Inputs -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="latitude" class="block text-sm font-medium text-gray-700">Latitude</label>
                                <input id="latitude" type="text" name="latitude" x-model="lat" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm py-2 px-3 min-h-[44px] bg-gray-50 text-gray-500" readonly placeholder="Opsional">
                            </div>
                            <div>
                                <label for="longitude" class="block text-sm font-medium text-gray-700">Longitude</label>
                                <input id="longitude" type="text" name="longitude" x-model="lng" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm py-2 px-3 min-h-[44px] bg-gray-50 text-gray-500" readonly placeholder="Opsional">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 2: Input Kriteria TOPSIS Numerik -->
            <div class="bg-white overflow-hidden shadow-sm border border-gray-100 sm:rounded-xl mb-6">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                    <h3 class="text-lg font-bold text-base-dark">Data Penilaian Kriteria Numerik</h3>
                </div>
                <div class="p-4 sm:p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div>
                        <label for="kepadatan_penduduk" class="block text-sm font-medium text-base-dark mb-1 flex items-center justify-between">
                            <span>Kepadatan Penduduk</span>
                            <span x-show="isLoadingBps" class="text-xs text-primary flex items-center" x-cloak>
                                <svg class="animate-spin -ml-1 mr-1 h-3 w-3 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                Mencari Data...
                            </span>
                        </label>
                        <div class="relative">
                            <input type="hidden" name="kepadatan_penduduk" :value="kepadatanRaw">
                            <input id="kepadatan_penduduk_display" type="text" inputmode="numeric" required 
                                :value="formatThousand(kepadatanRaw)" 
                                @input="kepadatanRaw = $event.target.value.replace(/[^0-9]/g, '')"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm py-2 px-3 min-h-[44px]" :class="{'bg-gray-100': isLoadingBps}" :readonly="isLoadingBps">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">jiwa/km²</span>
                            </div>
                        </div>
                        <!-- <p class="text-xs text-gray-500 mt-1 font-medium">Contoh: 1.945 jiwa/km² atau 12.567 jiwa/km²</p> -->
                        <p x-show="bpsError" x-text="bpsError" class="text-xs text-red-500 mt-1" x-cloak></p>
                        <input type="hidden" id="tahun_bps" name="tahun_bps" value="{{ old('tahun_bps') }}">
                        <input type="hidden" id="kode_wilayah_bps" name="kode_wilayah_bps" value="{{ old('kode_wilayah_bps') }}">
                    </div>
                    <div>
                        <label for="harga_sewa" class="block text-sm font-medium text-base-dark mb-1">Harga Sewa / Tahun</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">Rp</span>
                            </div>
                            <input type="hidden" name="harga_sewa" :value="hargaSewaRaw">
                            <input id="harga_sewa_display" type="text" inputmode="numeric" required 
                                :value="formatThousand(hargaSewaRaw)"
                                @input="hargaSewaRaw = $event.target.value.replace(/[^0-9]/g, '')"
                                class="w-full pl-9 rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm py-2 px-3 min-h-[44px]">
                        </div>
                        <!-- <p class="text-xs text-gray-500 mt-1 font-medium">Contoh: 12.000.000/tahun atau 120.000.000/tahun</p> -->
                    </div>
                    <div>
                        <label for="jumlah_kompetitor" class="block text-sm font-medium text-base-dark mb-1">Jumlah Kompetitor</label>
                        <div class="relative">
                            <input id="jumlah_kompetitor" type="number" name="jumlah_kompetitor" value="{{ old('jumlah_kompetitor') }}" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm py-2 px-3 min-h-[44px]">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">titik</span>
                            </div>
                        </div>
                        <!-- <p class="text-xs text-gray-500 mt-1 font-medium">Contoh: 3 atau 10 kompetitor</p> -->
                    </div>
                    <div>
                        <label for="jarak_rph" class="block text-sm font-medium text-base-dark mb-1">Jarak RPH</label>
                        <div class="relative">
                            <input type="hidden" name="jarak_rph" :value="jarakRphDisplay ? jarakRphDisplay.toString().replace(/,/g, '.') : ''">
                            <input id="jarak_rph_display" type="text" inputmode="decimal" x-model="jarakRphDisplay" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm py-2 px-3 min-h-[44px]">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">km</span>
                            </div>
                        </div>
                        <!-- <p class="text-xs text-gray-500 mt-1 font-medium">Contoh: 2.5 km atau 1,7 km</p> -->
                    </div>
                </div>
            </div>

            <!-- Section 3: Indikator Aksesibilitas & Kelayakan -->
            <div class="bg-white overflow-hidden shadow-sm border border-gray-100 sm:rounded-xl mb-6">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    <h3 class="text-lg font-bold text-base-dark">Indikator Aksesibilitas & Kelayakan Bangunan</h3>
                </div>
                <div class="p-4 sm:p-6 grid grid-cols-1 md:grid-cols-2 gap-8">
                    
                    <!-- Aksesibilitas -->
                    <div>
                        <h4 class="text-sm font-bold text-gray-700 mb-3 uppercase tracking-wider">Aksesibilitas</h4>
                        <div class="space-y-3">
                            <label class="flex items-center cursor-pointer p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                                <input type="checkbox" name="akses_roda4" value="1" {{ old('akses_roda4') ? 'checked' : '' }} class="w-5 h-5 text-primary border-gray-300 rounded focus:ring-primary">
                                <span class="ml-3 text-sm text-base-dark font-medium">Bisa diakses kendaraan roda 4</span>
                            </label>
                            <label class="flex items-center cursor-pointer p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                                <input type="checkbox" name="jalan_bagus" value="1" {{ old('jalan_bagus') ? 'checked' : '' }} class="w-5 h-5 text-primary border-gray-300 rounded focus:ring-primary">
                                <span class="ml-3 text-sm text-base-dark font-medium">Kondisi jalan bagus / tidak berlubang</span>
                            </label>
                            <label class="flex items-center cursor-pointer p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                                <input type="checkbox" name="dekat_fasilitas" value="1" {{ old('dekat_fasilitas') ? 'checked' : '' }} class="w-5 h-5 text-primary border-gray-300 rounded focus:ring-primary">
                                <span class="ml-3 text-sm text-base-dark font-medium">Dekat dengan fasilitas umum (pasar/jalan utama)</span>
                            </label>
                        </div>
                    </div>

                    <!-- Kelayakan Bangunan -->
                    <div>
                        <h4 class="text-sm font-bold text-gray-700 mb-3 uppercase tracking-wider">Kelayakan Bangunan</h4>
                        <div class="space-y-3">
                            <label class="flex items-center cursor-pointer p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                                <input type="checkbox" name="bangunan_layak" value="1" {{ old('bangunan_layak') ? 'checked' : '' }} class="w-5 h-5 text-primary border-gray-300 rounded focus:ring-primary">
                                <span class="ml-3 text-sm text-base-dark font-medium">Struktur bangunan kokoh & layak pakai</span>
                            </label>
                            <label class="flex items-center cursor-pointer p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                                <input type="checkbox" name="ventilasi_baik" value="1" {{ old('ventilasi_baik') ? 'checked' : '' }} class="w-5 h-5 text-primary border-gray-300 rounded focus:ring-primary">
                                <span class="ml-3 text-sm text-base-dark font-medium">Ventilasi / Sirkulasi udara baik</span>
                            </label>
                            <label class="flex items-center cursor-pointer p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                                <input type="checkbox" name="air_listrik_memadai" value="1" {{ old('air_listrik_memadai') ? 'checked' : '' }} class="w-5 h-5 text-primary border-gray-300 rounded focus:ring-primary">
                                <span class="ml-3 text-sm text-base-dark font-medium">Jaringan air & listrik instalasi memadai</span>
                            </label>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Section 4: Detail Fisik Bangunan -->
            <div class="bg-white overflow-hidden shadow-sm border border-gray-100 sm:rounded-xl mb-6">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    <h3 class="text-lg font-bold text-base-dark">Detail Fisik Bangunan & Catatan</h3>
                </div>
                <div class="p-4 sm:p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-6 mb-6">
                        <div class="col-span-1 lg:col-span-2">
                            <label class="block text-sm font-medium text-base-dark mb-1">Jenis Bangunan</label>
                            <input type="text" name="jenis_bangunan" value="{{ old('jenis_bangunan', 'Ruko') }}" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm py-2 px-3 min-h-[44px]">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-base-dark mb-1">Luas Tanah (m²)</label>
                            <input type="text" inputmode="decimal" name="luas_tanah" value="{{ old('luas_tanah') }}" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm py-2 px-3 min-h-[44px]">
                            <p class="text-xs text-gray-400 mt-1">Gunakan titik atau koma untuk angka desimal</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-base-dark mb-1">Luas Bangunan (m²)</label>
                            <input type="text" inputmode="decimal" name="luas_bangunan" value="{{ old('luas_bangunan') }}" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm py-2 px-3 min-h-[44px]">
                            <p class="text-xs text-gray-400 mt-1">Gunakan titik atau koma untuk angka desimal</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-base-dark mb-1">Jumlah Ruangan</label>
                            <input type="number" name="jumlah_ruangan" value="{{ old('jumlah_ruangan') }}" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm py-2 px-3 min-h-[44px]">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-base-dark mb-1">Jumlah WC</label>
                            <input type="number" name="jumlah_wc" value="{{ old('jumlah_wc') }}" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm py-2 px-3 min-h-[44px]">
                        </div>
                        <div class="col-span-1 lg:col-span-2">
                            <label class="block text-sm font-medium text-base-dark mb-1">Sumber Air</label>
                            <input type="text" name="sumber_air" value="{{ old('sumber_air', 'PDAM') }}" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm py-2 px-3 min-h-[44px]">
                        </div>
                        <div class="col-span-1 lg:col-span-2 flex items-center pt-6">
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" name="listrik" value="1" {{ old('listrik', true) ? 'checked' : '' }} class="w-5 h-5 text-primary border-gray-300 rounded focus:ring-primary">
                                <span class="ml-3 text-sm text-base-dark font-medium">Tersedia Listrik (PLN)</span>
                            </label>
                        </div>
                    </div>
                    
                    <hr class="border-gray-100 my-6">

                    <div>
                        <label for="catatan" class="block text-sm font-medium text-base-dark mb-1">Catatan Tambahan (Opsional)</label>
                        <textarea id="catatan" name="catatan" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm transition-colors py-2 px-3 min-h-[44px]">{{ old('catatan') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Section 5: Dokumentasi Foto (Alpine JS Uploader) -->
            <div class="bg-white overflow-hidden shadow-sm border border-gray-100 sm:rounded-xl mb-6">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <h3 class="text-lg font-bold text-base-dark">Dokumentasi Lokasi (Maks. 10 Foto)</h3>
                </div>
                
                <div class="p-4 sm:p-6" x-data="imageUploader()">
                    
                    <!-- Drag & Drop Zone -->
                    <div 
                        @dragover.prevent="dragover = true"
                        @dragleave.prevent="dragover = false"
                        @drop.prevent="dropFiles($event)"
                        :class="dragover ? 'border-primary bg-soft-green' : 'border-gray-300 bg-gray-50 hover:bg-gray-100'"
                        class="w-full flex justify-center px-6 pt-5 pb-6 border-2 border-dashed rounded-xl transition-colors cursor-pointer mb-6"
                        @click="$refs.fileInput.click()"
                    >
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600 justify-center">
                                <span class="relative cursor-pointer rounded-md font-medium text-primary hover:text-primary-dark focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-primary">
                                    <span>Upload foto</span>
                                    <!-- Keep the input hidden but functional -->
                                    <input type="file" name="photos[]" multiple accept="image/jpeg,image/png,image/webp,image/jpg" class="sr-only" x-ref="fileInput" @change="addFiles($event.target.files)">
                                </span>
                                <p class="pl-1">atau seret dan lepas kesini</p>
                            </div>
                            <p class="text-xs text-gray-500">PNG, JPG, WEBP hingga 20MB total</p>
                        </div>
                    </div>

                    <!-- Size Warning -->
                    <template x-if="totalSizeWarning">
                        <div class="mb-4 text-sm text-red-600 flex items-center bg-red-50 p-3 rounded border border-red-100">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            Total ukuran file melebihi batas 20MB. Harap kurangi foto.
                        </div>
                    </template>
                    <template x-if="maxFilesWarning">
                        <div class="mb-4 text-sm text-red-600 flex items-center bg-red-50 p-3 rounded border border-red-100">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            Maksimal 10 foto yang diperbolehkan.
                        </div>
                    </template>

                    <!-- Image Previews -->
                    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4">
                        <template x-for="(image, index) in images" :key="index">
                            <div class="relative group rounded-lg overflow-hidden border border-gray-200 aspect-square bg-gray-100">
                                <img :src="image.url" class="object-cover w-full h-full">
                                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/40 transition-all flex flex-col items-center justify-center opacity-0 group-hover:opacity-100">
                                    <button type="button" @click="removeImage(index)" class="bg-red-500 text-white p-2 rounded-full hover:bg-red-600 transform transition hover:scale-110">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                                <!-- Gradient bottom for text readability -->
                                <div class="absolute bottom-0 inset-x-0 bg-gradient-to-t from-black/60 to-transparent p-2">
                                    <p class="text-[10px] text-white truncate" x-text="image.name"></p>
                                    <p class="text-[10px] text-gray-300" x-text="(image.file.size / 1024 / 1024).toFixed(2) + ' MB'"></p>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Progress state (only shows during form submission visually via Alpine if needed, but standard HTTP POST is fine for now) -->

                </div>
            </div>

            <!-- Submit Actions -->
            <div class="flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-end mb-10 pt-5 border-t border-gray-200 gap-3 sm:gap-4">
                <a href="{{ route('manajer.observasi.index') }}" class="w-full sm:w-auto text-center inline-flex items-center justify-center text-sm font-medium text-gray-500 hover:text-base-dark transition-colors py-2 min-h-[44px]">
                    Batal
                </a>
                <button type="submit" class="w-full sm:w-auto justify-center inline-flex items-center px-6 py-3 min-h-[44px] bg-primary border border-transparent rounded-lg font-bold text-sm text-white hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition-all shadow-md hover:shadow-lg">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Simpan & Proses Penilaian
                </button>
            </div>
        </form>
    </div>

    <!-- Alpine Image Uploader Logic -->
    <script>
        function imageUploader() {
            return {
                dragover: false,
                images: [],
                totalSizeWarning: false,
                maxFilesWarning: false,
                
                dropFiles(e) {
                    this.dragover = false;
                    this.addFiles(e.dataTransfer.files);
                },
                
                addFiles(files) {
                    let totalSize = this.images.reduce((acc, img) => acc + img.file.size, 0);
                    
                    Array.from(files).forEach(file => {
                        // Check if it's an image
                        if (!file.type.match('image.*')) return;
                        
                        // Check individual file size (Max 2MB to match PHP ini settings)
                        if (file.size > (2 * 1024 * 1024)) {
                            alert('File ' + file.name + ' terlalu besar! Maksimal ukuran per foto adalah 2MB.');
                            return;
                        }

                        // Check Max Files
                        if (this.images.length >= 10) {
                            this.maxFilesWarning = true;
                            return;
                        } else {
                            this.maxFilesWarning = false;
                        }

                        // Check Total Size (8MB = 8 * 1024 * 1024 to match post_max_size)
                        if ((totalSize + file.size) > (8 * 1024 * 1024)) {
                            this.totalSizeWarning = true;
                            return;
                        } else {
                            this.totalSizeWarning = false;
                        }

                        totalSize += file.size;

                        // Create Preview URL
                        let url = URL.createObjectURL(file);
                        this.images.push({
                            file: file,
                            url: url,
                            name: file.name
                        });
                    });

                    this.syncFileInput();
                },

                removeImage(index) {
                    URL.revokeObjectURL(this.images[index].url);
                    this.images.splice(index, 1);
                    this.totalSizeWarning = false;
                    this.maxFilesWarning = false;
                    this.syncFileInput();
                },

                syncFileInput() {
                    // Because we can't directly manipulate FileList easily for the native input, 
                    // we use DataTransfer to re-create the FileList based on our Alpine state.
                    const dt = new DataTransfer();
                    this.images.forEach(img => {
                        dt.items.add(img.file);
                    });
                    this.$refs.fileInput.files = dt.files;
                }
            }
        }
    </script>

    @push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <style>
        #map { min-height: 300px; }
    </style>
    @endpush

    @push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('locationMap', () => ({
                map: null,
                marker: null,
                lat: '{{ old('latitude') }}',
                lng: '{{ old('longitude') }}',
                greenIcon: null,

                init() {
                    let defaultLat = this.lat ? parseFloat(this.lat) : -0.789;
                    let defaultLng = this.lng ? parseFloat(this.lng) : 113.921;
                    let defaultZoom = this.lat ? 13 : 5;

                    this.map = L.map('map').setView([defaultLat, defaultLng], defaultZoom);

                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; OpenStreetMap contributors'
                    }).addTo(this.map);

                    this.greenIcon = new L.Icon({
                        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png',
                        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                        iconSize: [25, 41],
                        iconAnchor: [12, 41],
                        popupAnchor: [1, -34],
                        shadowSize: [41, 41]
                    });

                    if (this.lat && this.lng) {
                        this.marker = L.marker([this.lat, this.lng], { draggable: true, icon: this.greenIcon }).addTo(this.map);
                        this.setupMarkerEvents();
                    }

                    this.map.on('click', (e) => {
                        const coords = e.latlng;
                        this.updateMarker(coords.lat, coords.lng);
                    });
                },

                updateMarker(lat, lng) {
                    this.lat = lat.toFixed(6);
                    this.lng = lng.toFixed(6);
                    if (this.marker) {
                        this.marker.setLatLng([lat, lng]);
                    } else {
                        this.marker = L.marker([lat, lng], { draggable: true, icon: this.greenIcon }).addTo(this.map);
                        this.setupMarkerEvents();
                    }
                },

                setupMarkerEvents() {
                    this.marker.on('dragend', (e) => {
                        const coords = e.target.getLatLng();
                        this.lat = coords.lat.toFixed(6);
                        this.lng = coords.lng.toFixed(6);
                    });
                },

                getCurrentLocation() {
                    if ("geolocation" in navigator) {
                        navigator.geolocation.getCurrentPosition((position) => {
                            const lat = position.coords.latitude;
                            const lng = position.coords.longitude;
                            this.map.setView([lat, lng], 15);
                            this.updateMarker(lat, lng);
                        }, (error) => {
                            alert("Tidak dapat mengambil lokasi Anda. Pastikan Anda telah memberikan izin (allow) pada browser, lalu coba lagi. Jika tetap gagal, silakan cari lokasi di peta secara manual.");
                        });
                    } else {
                        alert("Browser Anda tidak mendukung fitur Geolocation.");
                    }
                }
            }));
        });
    </script>
    @endpush
</x-app-layout>
