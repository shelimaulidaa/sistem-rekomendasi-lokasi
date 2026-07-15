<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            <a href="{{ route('direktur.observasi.index') }}" class="text-gray-400 hover:text-primary transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h2 class="font-semibold text-xl text-base-dark leading-tight">
                {{ __('Detail Observasi') }} - <span class="text-primary">{{ $observasi->nama_pemilik ?? '-' }}</span>
            </h2>
        </div>
    </x-slot>

    <div class="max-w-6xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Left Column: Data Details -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- Tabs: Kondisi Bangunan & Lokasi -->
                <div x-data="{ activeTab: 'kondisi' }" class="bg-white overflow-hidden shadow-sm border border-gray-100 sm:rounded-xl">
                    <div class="border-b border-gray-100 flex">
                        <button @click="activeTab = 'kondisi'" class="flex-1 py-4 px-6 text-sm font-bold text-center border-b-2 transition-colors outline-none" :class="activeTab === 'kondisi' ? 'border-primary text-primary bg-primary/5' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50'">
                            <div class="flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                Detail Fisik Bangunan
                            </div>
                        </button>
                        <button @click="activeTab = 'lokasi'; setTimeout(() => { if(window.observationMap) window.observationMap.invalidateSize(); }, 50);" class="flex-1 py-4 px-6 text-sm font-bold text-center border-b-2 transition-colors outline-none" :class="activeTab === 'lokasi' ? 'border-primary text-primary bg-primary/5' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50'">
                            <div class="flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.243-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                Informasi Lokasi & Peta
                            </div>
                        </button>
                    </div>

                    <!-- TAB KONDISI BANGUNAN -->
                    <div x-show="activeTab === 'kondisi'" class="p-4 sm:p-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-y-5 gap-x-6">
                            <div>
                                <p class="text-xs text-gray-500 font-medium uppercase tracking-wider mb-1">Luas Bangunan</p>
                                <p class="text-sm font-bold text-base-dark">{{ floatval($observasi->luas_bangunan) }} m²</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 font-medium uppercase tracking-wider mb-1">Luas Tanah</p>
                                <p class="text-sm font-bold text-base-dark">{{ floatval($observasi->luas_tanah) }} m²</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 font-medium uppercase tracking-wider mb-1">Jumlah Lantai</p>
                                <p class="text-sm font-bold text-base-dark">{{ $observasi->jumlah_lantai ?? '-' }} Lantai</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 font-medium uppercase tracking-wider mb-1">Ruang Operasional</p>
                                <p class="text-sm font-bold text-base-dark">{{ $observasi->jumlah_ruangan }} Ruang</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 font-medium uppercase tracking-wider mb-1">Kamar Mandi / WC</p>
                                <p class="text-sm font-bold text-base-dark">{{ $observasi->jumlah_wc }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 font-medium uppercase tracking-wider mb-1">Jenis Bangunan</p>
                                <p class="text-sm font-bold text-base-dark">{{ $observasi->jenis_bangunan }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 font-medium uppercase tracking-wider mb-1">Kondisi Bangunan</p>
                                <p class="text-sm font-bold text-base-dark">{{ $observasi->kondisi_bangunan ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 font-medium uppercase tracking-wider mb-1">Sumber Air Bersih</p>
                                <p class="text-sm font-bold text-base-dark">{{ $observasi->sumber_air }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 font-medium uppercase tracking-wider mb-1">Daya Listrik</p>
                                <p class="text-sm font-bold text-base-dark">{{ $observasi->daya_listrik ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 font-medium uppercase tracking-wider mb-1">Area Parkir</p>
                                <p class="text-sm font-bold text-base-dark">{{ $observasi->area_parkir ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 font-medium uppercase tracking-wider mb-1">Lebar Jalan Depan</p>
                                <p class="text-sm font-bold text-base-dark">{{ $observasi->lebar_jalan ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 font-medium uppercase tracking-wider mb-1">Ventilasi</p>
                                <p class="text-sm font-bold text-base-dark">{{ $observasi->ventilasi ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 font-medium uppercase tracking-wider mb-1">Sirkulasi Udara</p>
                                <p class="text-sm font-bold text-base-dark">{{ $observasi->sirkulasi ?? '-' }}</p>
                            </div>
                        </div>

                        @if($observasi->catatan)
                        <div class="mt-8 pt-4 border-t border-gray-100">
                            <p class="text-xs text-gray-500 font-medium uppercase tracking-wider mb-2">Catatan Tambahan</p>
                            <p class="text-sm text-gray-700 bg-gray-50 p-4 rounded-lg border border-gray-100 italic">{{ $observasi->catatan }}</p>
                        </div>
                        @endif
                    </div>

                    <!-- TAB LOKASI -->
                    <div x-show="activeTab === 'lokasi'" class="p-4 sm:p-6" style="display: none;">
                        <div class="mb-6">
                            <h4 class="text-sm font-bold text-base-dark mb-3">Alamat Lengkap</h4>
                            <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                                <p class="text-sm text-gray-800 mb-2">{{ $observasi->alamat_lengkap }}</p>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-4 pt-4 border-t border-gray-200">
                                    <div>
                                        <p class="text-xs text-gray-500 font-medium">Kecamatan</p>
                                        <p class="text-sm font-semibold text-gray-800">{{ $observasi->kecamatan }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 font-medium">Kabupaten/Kota</p>
                                        <p class="text-sm font-semibold text-gray-800">{{ $observasi->kabupaten_kota }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 font-medium">Provinsi</p>
                                        <p class="text-sm font-semibold text-gray-800">{{ $observasi->provinsi }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($observasi->umk || $observasi->pdrb || $observasi->jumlah_penduduk_muslim)
                        <div class="mb-6">
                            <h4 class="text-sm font-bold text-base-dark mb-3">Statistik Wilayah</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div class="bg-gray-50 rounded-lg p-4 border border-gray-100 flex flex-col">
                                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">UMK Kabupaten / Kota</span>
                                    <span class="text-lg font-bold text-gray-800">{{ $observasi->umk ? 'Rp ' . number_format($observasi->umk, 2, ',', '.') : '-' }}</span>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-4 border border-gray-100 flex flex-col">
                                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">PDRB Per Kapita</span>
                                    <span class="text-lg font-bold text-gray-800">{{ $observasi->pdrb ? 'Rp ' . number_format($observasi->pdrb, 0, ',', '.') : '-' }}</span>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-4 border border-gray-100 flex flex-col">
                                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Jumlah Penduduk Muslim</span>
                                    <span class="text-lg font-bold text-gray-800">{{ $observasi->jumlah_penduduk_muslim ? number_format($observasi->jumlah_penduduk_muslim, 0, ',', '.') . ' Jiwa' : '-' }}</span>
                                </div>
                            </div>
                        </div>
                        @endif

                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-sm font-bold text-base-dark">Koordinat & Peta</h4>
                            @if($observasi->latitude && $observasi->longitude)
                                <a href="https://www.google.com/maps?q={{ $observasi->latitude }},{{ $observasi->longitude }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center px-3 py-1.5 bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200 rounded-md text-xs font-semibold transition-colors">
                                    Buka di Google Maps
                                    <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                </a>
                            @endif
                        </div>
                        
                        @if($observasi->latitude && $observasi->longitude)
                            <div class="relative w-full rounded-lg border border-gray-300 shadow-sm overflow-hidden h-[300px]">
                                <div id="map-readonly-{{ $observasi->id }}" class="w-full h-full z-0 relative bg-gray-50"></div>
                                <button type="button" onclick="recenterMap()" class="absolute bottom-4 right-4 z-[400] bg-white text-gray-700 p-2 rounded-md shadow-md border border-gray-200 hover:bg-gray-50 hover:text-primary transition-colors focus:outline-none" title="Kembali ke titik lokasi">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.243-4.243a8 8 0 1111.314 0z"></path></svg>
                                </button>
                            </div>
                            <div class="mt-3 flex gap-4 text-sm">
                                <div class="bg-gray-50 px-3 py-2 rounded-md border border-gray-200">
                                    <span class="font-medium text-gray-500">Latitude:</span> <span class="font-mono text-gray-800">{{ $observasi->latitude }}</span>
                                </div>
                                <div class="bg-gray-50 px-3 py-2 rounded-md border border-gray-200">
                                    <span class="font-medium text-gray-500">Longitude:</span> <span class="font-mono text-gray-800">{{ $observasi->longitude }}</span>
                                </div>
                            </div>
                        @else
                            <div class="w-full flex flex-col items-center justify-center p-8 bg-gray-50 border border-gray-200 rounded-lg text-center">
                                <div class="w-16 h-16 mb-4 rounded-full bg-gray-100 border border-gray-200 flex items-center justify-center">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.243-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <line x1="4" y1="4" x2="20" y2="20" stroke-width="2" stroke-linecap="round"></line>
                                    </svg>
                                </div>
                                <h4 class="text-sm font-semibold text-gray-800 mb-1">Koordinat Lokasi Belum Tersedia</h4>
                                <p class="text-xs text-gray-500 max-w-sm">Peta tidak dapat ditampilkan karena titik koordinat latitude dan longitude belum diinputkan untuk observasi ini.</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Hasil Penilaian (TOPSIS View) -->
                <div class="bg-white overflow-hidden shadow-sm border border-gray-100 sm:rounded-xl">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-base-dark">Data Penilaian TOPSIS</h3>
                        @if($observasi->penilaians->count() > 0)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Tersinkronisasi
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                Belum Disinkron
                            </span>
                        @endif
                    </div>
                    <div class="p-0 overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kriteria / Indikator</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Nilai Observasi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">Harga Sewa / Tahun</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-base-dark text-right">Rp {{ number_format($observasi->harga_sewa, 0, ',', '.') }}</td>
                                </tr>
                                
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">Jumlah Kompetitor</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-base-dark text-right">{{ $observasi->jumlah_kompetitor }} titik</td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">Jarak ke RPH</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-base-dark text-right">{{ $observasi->jarak_rph }} km</td>
                                </tr>
                                
                                <!-- Aksesibilitas (Calculated) -->
                                <tr class="bg-gray-50">
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-semibold text-gray-700 mb-1">Skor Aksesibilitas (Sistem)</div>
                                        <ul class="text-xs text-gray-500 space-y-1 ml-4 list-disc">
                                            <li class="{{ $observasi->akses_roda4 ? 'text-primary' : 'text-gray-400 line-through' }}">Akses Roda 4</li>
                                            <li class="{{ $observasi->jalan_bagus ? 'text-primary' : 'text-gray-400 line-through' }}">Jalan Bagus</li>
                                            <li class="{{ $observasi->dekat_fasilitas ? 'text-primary' : 'text-gray-400 line-through' }}">Dekat Fasilitas</li>
                                        </ul>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right align-top pt-5">
                                        @php
                                            $aksesScore = 0;
                                            $trues = ($observasi->akses_roda4 ? 1 : 0) + ($observasi->jalan_bagus ? 1 : 0) + ($observasi->dekat_fasilitas ? 1 : 0);
                                            $aksesScore = match($trues) { 3 => 5, 2 => 3, 1 => 1, default => 0 };
                                        @endphp
                                        <span class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-primary text-white font-bold text-sm">
                                            {{ $aksesScore }}
                                        </span>
                                    </td>
                                </tr>

                                <!-- Kelayakan (Calculated) -->
                                <tr class="bg-gray-50">
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-semibold text-gray-700 mb-1">Skor Kelayakan Bangunan (Sistem)</div>
                                        <ul class="text-xs text-gray-500 space-y-1 ml-4 list-disc">
                                            <li class="{{ $observasi->bangunan_layak ? 'text-primary' : 'text-gray-400 line-through' }}">Struktur Layak</li>
                                            <li class="{{ $observasi->ventilasi_baik ? 'text-primary' : 'text-gray-400 line-through' }}">Ventilasi Baik</li>
                                            <li class="{{ $observasi->air_listrik_memadai ? 'text-primary' : 'text-gray-400 line-through' }}">Air/Listrik Memadai</li>
                                        </ul>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right align-top pt-5">
                                        @php
                                            $layakScore = 0;
                                            $ltrues = ($observasi->bangunan_layak ? 1 : 0) + ($observasi->ventilasi_baik ? 1 : 0) + ($observasi->air_listrik_memadai ? 1 : 0);
                                            $layakScore = match($ltrues) { 3 => 5, 2 => 3, 1 => 1, default => 0 };
                                        @endphp
                                        <span class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-primary text-white font-bold text-sm">
                                            {{ $layakScore }}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Right Column: Metadata & Photos -->
            <div class="lg:col-span-1 space-y-6">
                
                <div class="bg-gray-50 rounded-xl p-5 border border-gray-100 shadow-sm">
                    <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-4">Metadata</h3>
                    <div class="space-y-4">
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Nama Pemilik</p>
                            <p class="text-sm font-semibold text-primary">
                                {{ $observasi->nama_pemilik ?? '-' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1">No. Telp Pemilik</p>
                            <p class="text-sm font-medium text-base-dark">
                                {{ $observasi->nomor_telepon_pemilik ?? '-' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Observer (Pemeriksa)</p>
                            <div class="flex items-center text-sm font-semibold text-base-dark">
                                <div class="w-6 h-6 rounded-full bg-soft-green text-primary flex items-center justify-center text-xs mr-2">
                                    {{ substr($observasi->user->name ?? 'S', 0, 1) }}
                                </div>
                                {{ $observasi->user->name ?? '-' }}
                            </div>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Tanggal & Jam Survei</p>
                            <p class="text-sm font-medium text-base-dark">
                                {{ $observasi->tanggal_observasi->format('d M Y') }}
                                @if($observasi->jam_observasi)
                                    | {{ \Carbon\Carbon::parse($observasi->jam_observasi)->format('H:i') }}
                                @endif
                            </p>
                        </div>
                        @if(!empty($observasi->anggota_pendamping) && count(array_filter($observasi->anggota_pendamping)) > 0)
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Anggota Pendamping</p>
                            <div class="space-y-1.5 mt-1">
                                @foreach($observasi->anggota_pendamping as $pendamping)
                                    @if(!empty(trim($pendamping)))
                                        <div class="flex items-center text-sm font-medium text-gray-700 bg-gray-50 px-2.5 py-1.5 rounded-md border border-gray-100">
                                            <svg class="w-3.5 h-3.5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                            {{ $pendamping }}
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Image Gallery -->
                <div class="bg-white overflow-hidden shadow-sm border border-gray-100 sm:rounded-xl">
                    <div class="px-5 py-4 border-b border-gray-100 bg-gray-50">
                        <h3 class="text-sm font-bold text-base-dark">Dokumentasi ({{ $observasi->dokumentasiLokasis->count() }})</h3>
                    </div>
                    <div class="p-5" x-data="{ imgModal: false, imgModalSrc: '' }">
                        @if($observasi->dokumentasiLokasis->count() > 0)
                            <div class="grid grid-cols-2 gap-3">
                                @foreach($observasi->dokumentasiLokasis as $doc)
                                    <div class="relative group aspect-square rounded-lg overflow-hidden border border-gray-200 cursor-pointer bg-gray-100"
                                         @click="imgModalSrc = '{{ asset('storage/' . $doc->foto_path) }}'; imgModal = true;">
                                        <img src="{{ asset('storage/' . $doc->foto_path) }}" loading="lazy" class="object-cover w-full h-full transition-transform duration-300 group-hover:scale-105" alt="Dokumentasi Observasi">
                                        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors flex items-center justify-center">
                                            <svg class="w-6 h-6 text-white opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path></svg>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-6">
                                <svg class="mx-auto h-10 w-10 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                <p class="text-xs text-gray-500">Tidak ada foto dokumentasi.</p>
                            </div>
                        @endif

                        <!-- Lightbox Modal -->
                        <div x-show="imgModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/90" @keydown.escape.window="imgModal = false">
                            <div class="relative max-w-4xl w-full max-h-screen flex items-center justify-center" @click.away="imgModal = false">
                                <button @click="imgModal = false" class="absolute -top-12 right-0 text-white hover:text-gray-300">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                                <img :src="imgModalSrc" class="max-h-[85vh] max-w-full rounded shadow-2xl object-contain">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    @push('styles')
    @if($observasi->latitude && $observasi->longitude)
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <style>
        [id^="map-"] { min-height: 300px; }
    </style>
    @endif
    @endpush

    @push('scripts')
    @if($observasi->latitude && $observasi->longitude)
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
        let observationMap;
        
        document.addEventListener('DOMContentLoaded', function() {
            const mapContainer = document.getElementById('map-readonly-{{ $observasi->id }}');
            if (!mapContainer || mapContainer._leaflet_id) return;
            
            observationMap = L.map(mapContainer, {
                zoomControl: true
            }).setView([{{ $observasi->latitude }}, {{ $observasi->longitude }}], 15);
            
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(observationMap);
            
            const greenIcon = new L.Icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png',
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                iconSize: [25, 41], 
                iconAnchor: [12, 41], 
                popupAnchor: [1, -34], 
                shadowSize: [41, 41]
            });
            
            L.marker([{{ $observasi->latitude }}, {{ $observasi->longitude }}], { icon: greenIcon }).addTo(observationMap);
            
            setTimeout(() => observationMap.invalidateSize(), 200);
        });

        function recenterMap() {
            if (observationMap) {
                observationMap.setView([{{ $observasi->latitude }}, {{ $observasi->longitude }}], 15, {
                    animate: true,
                    duration: 0.5
                });
            }
        }
    </script>
    @endif
    @endpush
</x-app-layout>
