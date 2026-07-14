<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-base-dark leading-tight">
                    {{ __('Detail Hasil Observasi') }}
                </h2>
                <p class="text-sm text-base-medium mt-1">Review detail data survei lapangan secara transparan.</p>
            </div>
            <div>
                <a href="{{ route('direktur.observasi.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <div class="space-y-6">
            <!-- Informasi Lokasi -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex items-center gap-2">
                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.243-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                <h3 class="text-lg font-bold text-base-dark">Informasi Lokasi</h3>
            </div>
                <div class="p-4 sm:p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Nama Pemilik</p>
                        <p class="mt-1 text-base font-semibold text-gray-900">{{ $observasi->nama_pemilik }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Nomor Telepon Pemilik</p>
                        <p class="mt-1 text-base font-semibold text-gray-900">{{ $observasi->nomor_telepon_pemilik ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Tanggal Observasi</p>
                        <p class="mt-1 text-base font-medium text-gray-900">{{ $observasi->tanggal_observasi ? \Carbon\Carbon::parse($observasi->tanggal_observasi)->translatedFormat('d F Y') : '-' }}</p>
                    </div>
                    <div class="md:col-span-2">
                        <p class="text-sm font-medium text-gray-500">Alamat Lengkap</p>
                        <p class="mt-1 text-base text-gray-900">{{ $observasi->alamat }}</p>
                        <p class="text-sm text-gray-600 mt-1">
                            Kec. {{ $observasi->kecamatan }}, Kab. {{ $observasi->kabupaten }}, {{ $observasi->provinsi }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Observer (Penginput)</p>
                        <p class="mt-1 text-sm text-gray-900 flex items-center">
                            <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            {{ $observasi->user->name ?? 'Sistem' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Data Observasi -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    <h3 class="text-lg font-bold text-base-dark">Data Pengukuran</h3>
                </div>
                    <div class="p-4 sm:p-6">
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-6">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Jenis/Tipe Bangunan</dt>
                            <dd class="mt-1 text-base font-semibold text-gray-900 capitalize">{{ $observasi->jenis_bangunan }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Luas Tanah</dt>
                            <dd class="mt-1 text-base font-semibold text-gray-900">{{ $observasi->luas_tanah }} <span class="text-sm font-normal text-gray-500">m²</span></dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Luas Bangunan</dt>
                            <dd class="mt-1 text-base font-semibold text-gray-900">{{ $observasi->luas_bangunan }} <span class="text-sm font-normal text-gray-500">m²</span></dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Jumlah Ruangan</dt>
                            <dd class="mt-1 text-base font-semibold text-gray-900">{{ $observasi->jumlah_ruangan }} <span class="text-sm font-normal text-gray-500">ruangan</span></dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Jumlah WC</dt>
                            <dd class="mt-1 text-base font-semibold text-gray-900">{{ $observasi->jumlah_wc }} <span class="text-sm font-normal text-gray-500">unit</span></dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Sumber Listrik</dt>
                            <dd class="mt-1 text-base font-semibold text-gray-900">{{ $observasi->listrik ? 'Tersedia' : 'Tidak Tersedia' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Sumber Air</dt>
                            <dd class="mt-1 text-base font-semibold text-gray-900 capitalize">{{ $observasi->sumber_air ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Biaya Sewa</dt>
                            <dd class="mt-1 text-base font-semibold text-gray-900">Rp {{ number_format($observasi->harga_sewa, 0, ',', '.') }}</dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Jumlah Kompetitor</dt>
                            <dd class="mt-1 text-base font-semibold text-gray-900">{{ $observasi->jumlah_kompetitor }} <span class="text-sm font-normal text-gray-500">lokasi</span></dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Jarak ke RPH</dt>
                            <dd class="mt-1 text-base font-semibold text-gray-900">{{ number_format($observasi->jarak_rph, 1) }} <span class="text-sm font-normal text-gray-500">km</span></dd>
                        </div>
                    </dl>
                    @if($observasi->catatan)
                        <div class="mt-6 pt-6 border-t border-gray-100">
                            <dt class="text-sm font-medium text-gray-500 mb-2">Catatan Tambahan</dt>
                            <dd class="text-sm text-gray-700 bg-gray-50 rounded-lg p-3 border border-gray-200">
                                {{ $observasi->catatan }}
                            </dd>
                        </div>
                    @endif

                    <!-- Read-only Map Integration -->
                    <div class="mt-8 pt-6 border-t border-gray-100">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-bold text-base-dark flex items-center">
                                <svg class="w-5 h-5 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.243-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                Peta Lokasi Aktual
                            </h3>
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
            </div>

            <!-- Checklist Kelayakan -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                    <h3 class="text-lg font-bold text-base-dark">Indikator & Kelayakan</h3>
                </div>
                    <div class="p-4 sm:p-6">
                        <ul class="space-y-4">
                        @php
                            $checklists = [
                                ['label' => 'Jalan Dapat Diakses Roda 4', 'value' => $observasi->akses_roda4],
                                ['label' => 'Kondisi Jalan Bagus (Sudah diaspal/cor)', 'value' => $observasi->jalan_bagus],
                                ['label' => 'Dekat Fasilitas Umum', 'value' => $observasi->dekat_fasilitas],
                                ['label' => 'Struktur Bangunan Layak', 'value' => $observasi->bangunan_layak],
                                ['label' => 'Ventilasi Udara Baik', 'value' => $observasi->ventilasi_baik],
                                ['label' => 'Instalasi Air & Listrik Memadai', 'value' => $observasi->air_listrik_memadai],
                            ];
                        @endphp

                        @foreach($checklists as $item)
                        <li class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                            <span class="text-sm font-medium text-gray-700">{{ $item['label'] }}</span>
                            @if($item['value'])
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-800">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                    Ya
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-800">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                                    Tidak
                                </span>
                            @endif
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        <!-- Dokumentasi Lapangan -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex items-center gap-2">
                <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                <h3 class="text-lg font-bold text-base-dark">Dokumentasi Lapangan</h3>
            </div>
                <div class="p-4 sm:p-6">
                    @if($observasi->dokumentasiLokasis->count() > 0)
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                        @foreach($observasi->dokumentasiLokasis as $doc)
                            <div class="relative group aspect-w-4 aspect-h-3 rounded-lg overflow-hidden bg-gray-100 border border-gray-200">
                                <img src="{{ Storage::url($doc->file_path) }}" alt="{{ $doc->keterangan }}" class="object-cover w-full h-full transition-transform duration-300 group-hover:scale-105">
                                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition-all flex items-end">
                                    <div class="p-3 w-full bg-gradient-to-t from-black/80 to-transparent opacity-0 group-hover:opacity-100 transition-opacity">
                                        <p class="text-white text-xs font-medium truncate">{{ $doc->keterangan ?? 'Tidak ada keterangan' }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">
                        <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <p class="text-base font-medium">Tidak ada foto dokumentasi</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Hasil Penilaian TOPSIS (jika ada) -->
        @if($hasilTopsis)
            @php
                $isTop1 = $hasilTopsis->ranking === 1;
                $isTop3 = $hasilTopsis->ranking > 1 && $hasilTopsis->ranking <= 3;
            @endphp
            <div class="bg-blue-50 rounded-xl shadow-sm border border-blue-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-blue-100 bg-blue-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path></svg>
                        <h3 class="text-lg font-bold text-blue-900">Hasil Keputusan Sistem (TOPSIS)</h3>
                    </div>
                </div>
                    <div class="p-4 sm:p-6">
                        <div class="flex flex-col sm:flex-row items-center gap-6 justify-around">
                        <div class="text-center">
                            <p class="text-sm font-medium text-blue-600 mb-1">Peringkat Akhir</p>
                            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full font-bold text-2xl shadow-sm {{ $isTop1 ? 'bg-green-500 text-white' : 'bg-white text-blue-800' }}">
                                #{{ $hasilTopsis->ranking }}
                            </div>
                        </div>
                        <div class="text-center">
                            <p class="text-sm font-medium text-blue-600 mb-1">Nilai Preferensi (V)</p>
                            <p class="text-3xl font-bold text-blue-900 font-mono">{{ number_format($hasilTopsis->nilai_preferensi, 4) }}</p>
                        </div>
                        <div class="text-center">
                            <p class="text-sm font-medium text-blue-600 mb-2">Status Rekomendasi</p>
                            @if($isTop1)
                                <span class="inline-flex items-center px-4 py-1.5 rounded-full text-sm font-bold bg-green-100 text-green-800 border border-green-200 shadow-sm uppercase tracking-wider">
                                    Sangat Direkomendasikan
                                </span>
                            @elseif($isTop3)
                                <span class="inline-flex items-center px-4 py-1.5 rounded-full text-sm font-bold bg-blue-100 text-blue-800 border border-blue-200 uppercase tracking-wider">
                                    Direkomendasikan
                                </span>
                            @else
                                <span class="inline-flex items-center px-4 py-1.5 rounded-full text-sm font-bold bg-white text-gray-700 border border-gray-200 uppercase tracking-wider">
                                    Dipertimbangkan
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endif
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
