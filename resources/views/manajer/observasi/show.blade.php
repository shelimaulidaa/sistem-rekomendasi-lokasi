<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between w-full">
            <div class="flex items-center space-x-3">
                <a href="{{ route('manajer.observasi.index') }}" class="text-gray-400 hover:text-primary transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <h2 class="font-semibold text-xl text-base-dark leading-tight">
                    {{ __('Detail Observasi') }} - <span class="text-primary">{{ $observasi->nama_pemilik ?? '-' }}</span>
                </h2>
            </div>
            <a href="{{ route('manajer.observasi.export-pdf', $observasi) }}" class="inline-flex items-center justify-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-xs font-bold uppercase tracking-wider rounded-md shadow-sm transition-colors min-h-[38px]">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Export PDF
            </a>
        </div>
    </x-slot>

    <div class="max-w-6xl mx-auto py-6 px-4 sm:px-6 lg:px-8" x-data="{ activeTab: 'utama' }">
        
        <!-- Tabs Header -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-6">
            <div class="flex border-b border-gray-100">
                <button @click="activeTab = 'utama'; setTimeout(() => { if(window.observationMap) window.observationMap.invalidateSize(); }, 50);" class="flex-1 py-4 px-6 text-sm font-bold text-center border-b-2 transition-colors outline-none" :class="activeTab === 'utama' ? 'border-primary text-primary bg-primary/5' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50'">
                    <div class="flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.243-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        Informasi Utama & Peta
                    </div>
                </button>
                <button @click="activeTab = 'kelayakan'" class="flex-1 py-4 px-6 text-sm font-bold text-center border-b-2 transition-colors outline-none" :class="activeTab === 'kelayakan' ? 'border-primary text-primary bg-primary/5' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50'">
                    <div class="flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Detail Observasi & Data Penilaian
                    </div>
                </button>
            </div>
        </div>

        <!-- TAB CONTENT: INFORMASI UTAMA & PETA -->
        <div x-show="activeTab === 'utama'" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left: Alamat, Wilayah & Peta -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Main Identity Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-6">
                    
                    <!-- Alamat Lengkap (Informasi Utama) -->
                    <div>
                        <span class="text-[11px] font-bold uppercase tracking-wider text-primary">Alamat Lengkap</span>
                        <div class="bg-gray-50/80 p-5 rounded-xl border border-gray-100 mt-2">
                            <h3 class="text-base sm:text-lg font-bold text-gray-900 leading-snug">{{ $observasi->alamat_lengkap }}</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mt-4 pt-3 border-t border-gray-200/80 text-xs">
                                <div>
                                    <span class="text-gray-400 font-medium block">Kecamatan</span>
                                    <span class="font-bold text-gray-800 text-sm">{{ $observasi->kecamatan }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-400 font-medium block">Kabupaten / Kota</span>
                                    <span class="font-bold text-gray-800 text-sm">{{ $observasi->kabupaten_kota }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-400 font-medium block">Provinsi</span>
                                    <span class="font-bold text-gray-800 text-sm">{{ $observasi->provinsi }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Profil Wilayah (Compact 3 Columns Card) -->
                    @if($observasi->umk || $observasi->pdrb || $observasi->jumlah_penduduk_muslim)
                    <div>
                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Profil Wilayah <span class="text-[10px] font-normal text-gray-400 lowercase">(informasi pendukung)</span></h4>
                        <div class="bg-gray-50/60 rounded-xl p-3.5 border border-gray-100 grid grid-cols-1 sm:grid-cols-3 gap-3 divide-y sm:divide-y-0 sm:divide-x divide-gray-200">
                            <div class="pt-2 sm:pt-0 sm:px-2">
                                <p class="text-[11px] font-medium text-gray-500">UMK Kab/Kota</p>
                                <p class="text-sm font-bold text-gray-800 mt-0.5">{{ $observasi->umk ? 'Rp ' . number_format($observasi->umk, 2, ',', '.') : '-' }}</p>
                            </div>
                            <div class="pt-2 sm:pt-0 sm:px-3">
                                <p class="text-[11px] font-medium text-gray-500">PDRB Per Kapita</p>
                                <p class="text-sm font-bold text-gray-800 mt-0.5">{{ $observasi->pdrb ? 'Rp ' . number_format($observasi->pdrb, 0, ',', '.') : '-' }}</p>
                            </div>
                            <div class="pt-2 sm:pt-0 sm:px-3">
                                <p class="text-[11px] font-medium text-gray-500">Penduduk Muslim</p>
                                <p class="text-sm font-bold text-gray-800 mt-0.5">{{ $observasi->jumlah_penduduk_muslim ? number_format($observasi->jumlah_penduduk_muslim, 0, ',', '.') . ' Jiwa' : '-' }}</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Peta Lokasi (Fokus Utama Visual) -->
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="text-sm font-bold text-base-dark">Koordinat & Peta Lokasi</h4>
                            @if($observasi->latitude && $observasi->longitude)
                                <a href="https://www.google.com/maps?q={{ $observasi->latitude }},{{ $observasi->longitude }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center px-3 py-1.5 bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200 rounded-lg text-xs font-semibold transition-colors">
                                    Buka di Google Maps
                                    <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                </a>
                            @endif
                        </div>
                        
                        @if($observasi->latitude && $observasi->longitude)
                            <div class="relative w-full rounded-xl border border-gray-300 shadow-sm overflow-hidden h-[320px]">
                                <div id="map-readonly-{{ $observasi->id }}" class="w-full h-full z-0 relative bg-gray-50"></div>
                                <button type="button" onclick="recenterMap()" class="absolute bottom-4 right-4 z-[400] bg-white text-gray-700 p-2 rounded-lg shadow-md border border-gray-200 hover:bg-gray-50 hover:text-primary transition-colors focus:outline-none" title="Kembali ke titik lokasi">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.243-4.243a8 8 0 1111.314 0z"></path></svg>
                                </button>
                            </div>
                            <div class="mt-3 flex gap-3 text-xs">
                                <div class="bg-gray-50 px-3 py-1.5 rounded-lg border border-gray-200">
                                    <span class="font-medium text-gray-500">Latitude:</span> <span class="font-mono font-bold text-gray-800">{{ $observasi->latitude }}</span>
                                </div>
                                <div class="bg-gray-50 px-3 py-1.5 rounded-lg border border-gray-200">
                                    <span class="font-medium text-gray-500">Longitude:</span> <span class="font-mono font-bold text-gray-800">{{ $observasi->longitude }}</span>
                                </div>
                            </div>
                        @else
                            <div class="w-full flex flex-col items-center justify-center p-8 bg-gray-50 border border-gray-200 rounded-xl text-center">
                                <div class="w-14 h-14 mb-3 rounded-full bg-gray-100 border border-gray-200 flex items-center justify-center">
                                    <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

            <!-- Right Sidebar: TOPSIS Card (Compact) & Ringkasan Informasi Pemilik -->
            <div class="lg:col-span-1 space-y-5">
                @if($hasilTopsis)
                <!-- Hasil Penilaian Rekomendasi Card (Compact & Balanced) -->
                <div class="bg-gradient-to-br from-primary to-primary-dark rounded-xl p-3.5 text-white shadow-sm">
                    <div class="flex items-center justify-between pb-2 mb-2 border-b border-white/15">
                        <h3 class="text-[10px] font-extrabold uppercase tracking-wider text-white/80">Hasil Penilaian</h3>
                        <span class="px-2 py-0.5 text-[9px] font-bold bg-white/20 rounded-full text-white">Selesai</span>
                    </div>
                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <div>
                            <p class="text-[10px] text-white/75 font-medium">Skor Rekomendasi</p>
                            <p class="text-base sm:text-lg font-extrabold font-mono text-white mt-0.5 leading-none">{{ number_format($hasilTopsis->nilai_preferensi, 4) }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] text-white/75 font-medium">Peringkat</p>
                            <p class="text-base sm:text-lg font-extrabold text-white mt-0.5 leading-none">#{{ $hasilTopsis->ranking }}</p>
                        </div>
                    </div>
                </div>
                @endif
                
                <!-- Informasi Pemilik & Survei (Ringkas & Compact) -->
                <div class="bg-gray-50/90 rounded-xl p-4 border border-gray-200/80 shadow-sm space-y-3 text-xs">
                    <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider pb-2 border-b border-gray-200">Informasi Pemilik & Survei</h3>
                    
                    <div class="flex justify-between items-start py-0.5">
                        <span class="text-gray-500">Nama Pemilik</span>
                        <span class="font-bold text-primary text-right ml-2">{{ $observasi->nama_pemilik ?? '-' }}</span>
                    </div>
                    
                    <div class="flex justify-between items-center py-0.5 border-t border-gray-100 pt-2">
                        <span class="text-gray-500">No. Telepon</span>
                        <span class="font-semibold text-gray-800">{{ $observasi->nomor_telepon_pemilik ?? '-' }}</span>
                    </div>
                    
                    <div class="flex justify-between items-center py-0.5 border-t border-gray-100 pt-2">
                        <span class="text-gray-500">Observer</span>
                        <div class="flex items-center font-bold text-gray-800">
                            <div class="w-5 h-5 rounded-full bg-soft-green text-primary flex items-center justify-center text-[10px] mr-1.5 font-extrabold">
                                {{ substr($observasi->user->name ?? 'S', 0, 1) }}
                            </div>
                            <span>{{ $observasi->user->name ?? '-' }}</span>
                        </div>
                    </div>
                    
                    <div class="flex justify-between items-center py-0.5 border-t border-gray-100 pt-2">
                        <span class="text-gray-500">Tanggal Survei</span>
                        <span class="font-medium text-gray-800">
                            {{ $observasi->tanggal_observasi->format('d M Y') }}
                            @if($observasi->jam_observasi)
                                <span class="text-gray-400">|</span> {{ \Carbon\Carbon::parse($observasi->jam_observasi)->format('H:i') }}
                            @endif
                        </span>
                    </div>
                    
                    @if(!empty($observasi->anggota_pendamping) && count(array_filter($observasi->anggota_pendamping)) > 0)
                    <div class="border-t border-gray-100 pt-2">
                        <span class="text-gray-500 block mb-1">Anggota Pendamping</span>
                        <div class="space-y-1">
                            @foreach($observasi->anggota_pendamping as $pendamping)
                                @if(!empty(trim($pendamping)))
                                    <div class="flex items-center text-gray-700 bg-white px-2 py-1 rounded border border-gray-200 text-[11px] font-medium">
                                        <svg class="w-3 h-3 mr-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                        {{ $pendamping }}
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- TAB CONTENT: OBSERVASI & PENILAIAN LOKASI -->
        <div x-show="activeTab === 'kelayakan'" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-8" style="display: none;">
            
            <!-- SECTION 1: INFORMASI PENDUKUNG -->
            <div>
                <div class="border-b border-gray-100 pb-3 mb-5 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
                    <div>
                        <h3 class="text-lg font-bold text-base-dark">Informasi Pendukung</h3>
                        <p class="text-sm text-gray-500">Data hasil observasi lapangan pendukung operasional.</p>
                    </div>
                    <span class="px-3 py-1 text-xs font-semibold bg-gray-100 text-gray-600 rounded-full border border-gray-200">Hasil Observasi Lapangan</span>
                </div>

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
                        <p class="text-xs text-gray-500 font-medium uppercase tracking-wider mb-1">Akses Parkir</p>
                        <p class="text-sm font-bold text-base-dark">{{ $observasi->area_parkir ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-medium uppercase tracking-wider mb-1">Lebar Jalan</p>
                        <p class="text-sm font-bold text-base-dark">{{ $observasi->lebar_jalan ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-medium uppercase tracking-wider mb-1">Kualitas Ventilasi</p>
                        <p class="text-sm font-bold text-base-dark">{{ $observasi->ventilasi ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-medium uppercase tracking-wider mb-1">Sirkulasi Udara</p>
                        <p class="text-sm font-bold text-base-dark">{{ $observasi->sirkulasi ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <!-- SECTION 2: HASIL PENILAIAN KRITERIA & SPASIAL -->
            <div>
                <div class="border-b border-gray-100 pb-3 mb-5 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
                    <div>
                        <h3 class="text-lg font-bold text-base-dark">Hasil Penilaian Kriteria & Spasial</h3>
                        <p class="text-sm text-gray-500">Nilai dari 5 kriteria utama pemilihan lokasi usaha.</p>
                    </div>
                    <span class="px-3 py-1 text-xs font-semibold bg-gray-100 text-gray-600 rounded-full border border-gray-200">5 Kriteria Utama</span>
                </div>

                <!-- Group of 5 Criteria -->
                <div class="space-y-4">
                    <!-- Row 1: Biaya Sewa & Jarak ke RPH (2 Summary Cards) -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 items-start">
                        <!-- Kriteria 1: Biaya Sewa -->
                        <div class="bg-white rounded-xl border border-gray-200 p-4 sm:p-5 shadow-sm hover:border-primary/50 transition-colors h-auto">
                            <div class="flex items-center justify-between border-b pb-2.5 mb-3 border-gray-100">
                                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">1. Biaya Sewa</span>
                                <div class="p-2 bg-green-50 text-green-600 rounded-lg">
                                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                            </div>
                            <h5 class="text-xl font-extrabold text-primary">Rp {{ number_format($observasi->harga_sewa, 0, ',', '.') }}</h5>
                            <p class="text-xs text-gray-500 mt-1">Biaya sewa per tahun</p>
                        </div>

                        <!-- Kriteria 4: Jarak ke RPH Terdekat -->
                        <div class="bg-white rounded-xl border border-gray-200 p-4 sm:p-5 shadow-sm hover:border-primary/50 transition-colors h-auto">
                            <div class="flex items-center justify-between border-b pb-2.5 mb-3 border-gray-100">
                                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">4. Jarak ke RPH</span>
                                <div class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                </div>
                            </div>
                            @php
                                $rphDistance = ($observasi->jarak_rph !== null && $observasi->jarak_rph !== '') ? $observasi->jarak_rph : ($spatialData['nearest_rph_distance'] ?? null);
                                $rphName = !empty($observasi->nearest_rph_name) ? $observasi->nearest_rph_name : ($spatialData['nearest_rph_name'] ?? 'RPH Terdekat');
                            @endphp
                            <h5 class="text-xl font-extrabold text-primary">{{ $rphDistance !== null ? rtrim(rtrim(number_format((float)$rphDistance, 4, '.', ''), '0'), '.') : '-' }} KM</h5>
                            <p class="text-xs text-gray-600 font-semibold mt-1 truncate" title="{{ $rphName }}">
                                {{ $rphName }}
                            </p>
                        </div>
                    </div>

                    <!-- Row 2: Tingkat Pesaing (Full Width Card) -->
                    @php
                        $compList = $spatialData['competitors_list'] ?? [];
                        $compCount = (int) ($spatialData['competitor_count'] ?? $observasi->jumlah_kompetitor ?? count($compList));
                    @endphp
                    <div x-data="{ showCompetitors: false }" class="bg-white rounded-xl border border-gray-200 p-4 sm:p-5 shadow-sm hover:border-primary/50 transition-colors mb-3">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="flex items-center space-x-3">
                                <div class="p-2 bg-purple-50 text-purple-600 rounded-lg">
                                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                </div>
                                <div>
                                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider block">5. Tingkat Pesaing</span>
                                    <div class="flex items-baseline space-x-2 mt-0.5">
                                        <h5 class="text-xl font-extrabold text-primary">{{ $compCount }} titik</h5>
                                        <span class="text-xs text-gray-500 font-medium">(Radius deteksi ±5 KM)</span>
                                    </div>
                                </div>
                            </div>
                            
                            <button type="button" 
                                    @click="showCompetitors = !showCompetitors" 
                                    class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg text-xs font-bold bg-purple-50 text-purple-700 hover:bg-purple-100 border border-purple-200/80 transition-colors focus:outline-none focus:ring-2 focus:ring-purple-400">
                                <span x-text="showCompetitors ? 'Sembunyikan Daftar Kompetitor' : 'Lihat Daftar Kompetitor'">Lihat Daftar Kompetitor</span>
                                <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': showCompetitors }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                        </div>

                        <!-- Content List (Toggled via button) -->
                        <div x-show="showCompetitors" x-cloak class="mt-4 pt-4 border-t border-gray-100">
                            @if(count($compList) > 0)
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 {{ count($compList) > 2 ? 'max-h-[220px] overflow-y-auto pr-1 [scrollbar-width:thin] [scrollbar-color:#cbd5e1_#f1f5f9]' : '' }}">
                                    @foreach($compList as $comp)
                                        <div class="p-3 bg-gray-50/80 rounded-lg border border-gray-100 text-xs hover:border-gray-200 transition-colors">
                                            <div class="flex justify-between items-start gap-2">
                                                <span class="font-bold text-gray-800 text-xs leading-snug flex-1 truncate" title="{{ $comp['nama'] ?? 'Kompetitor' }}">{{ $comp['nama'] ?? 'Kompetitor' }}</span>
                                                <span class="text-[11px] text-gray-500 font-semibold bg-white border border-gray-200 px-2 py-0.5 rounded-md flex-shrink-0 shadow-2xs">{{ $comp['distance'] ?? '-' }} km</span>
                                            </div>
                                            <div class="mt-2 flex items-center text-xs font-semibold">
                                                @if(isset($comp['rating']) && $comp['rating'] !== null && $comp['rating'] !== '' && (float)$comp['rating'] > 0)
                                                    <span class="inline-flex items-center text-yellow-500 font-bold">
                                                        ★ <span class="ml-1 text-gray-700 font-semibold">{{ number_format((float)$comp['rating'], 1, '.', '') }} / 5</span>
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center text-yellow-400 font-normal">
                                                        ★ <span class="ml-1 text-gray-400 font-normal italic">- / 5</span>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-xs text-gray-400 italic">Tidak ada kompetitor dalam radius 5 km.</p>
                            @endif
                        </div>
                    </div>

                    <!-- Row 3: Indikator Kelayakan Bangunan & Indikator Aksesibilitas Lokasi -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Kriteria 2: Indikator Kelayakan Bangunan -->
                        <div class="bg-gray-50 rounded-xl p-5 border border-gray-200 shadow-sm">
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-5 gap-3 border-b pb-3 border-gray-200">
                                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">2. Kelayakan Bangunan</span>
                                @php
                                    $ltrues = ($observasi->luas_mencukupi ? 1 : 0) + ($observasi->bangunan_layak ? 1 : 0) + ($observasi->ventilasi_baik ? 1 : 0) + ($observasi->air_listrik_memadai ? 1 : 0) + ($observasi->parkir_memadai ? 1 : 0);
                                    $layakScore = max(1, $ltrues);
                                @endphp
                                <div class="flex items-center space-x-2 bg-primary/10 border border-primary/20 px-3 py-1 rounded-full shadow-sm">
                                    <span class="text-[10px] font-bold text-primary uppercase tracking-wider">Skor:</span>
                                    <span class="text-base font-black text-primary leading-none">{{ $layakScore }}</span>
                                    <span class="text-[10px] text-gray-500 font-semibold leading-none">/ 5</span>
                                </div>
                            </div>
                            <ul class="space-y-3">
                                <li class="flex items-center text-sm">
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full mr-3 {{ $observasi->luas_mencukupi ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="{{ $observasi->luas_mencukupi ? 'M5 13l4 4L19 7' : 'M6 18L18 6M6 6l12 12' }}"></path></svg>
                                    </span>
                                    <span class="{{ $observasi->luas_mencukupi ? 'text-gray-800 font-semibold' : 'text-gray-400 line-through' }}">Luas bangunan mencukupi</span>
                                </li>
                                <li class="flex items-center text-sm">
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full mr-3 {{ $observasi->bangunan_layak ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="{{ $observasi->bangunan_layak ? 'M5 13l4 4L19 7' : 'M6 18L18 6M6 6l12 12' }}"></path></svg>
                                    </span>
                                    <span class="{{ $observasi->bangunan_layak ? 'text-gray-800 font-semibold' : 'text-gray-400 line-through' }}">Kondisi fisik bangunan layak</span>
                                </li>
                                <li class="flex items-center text-sm">
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full mr-3 {{ $observasi->ventilasi_baik ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="{{ $observasi->ventilasi_baik ? 'M5 13l4 4L19 7' : 'M6 18L18 6M6 6l12 12' }}"></path></svg>
                                    </span>
                                    <span class="{{ $observasi->ventilasi_baik ? 'text-gray-800 font-semibold' : 'text-gray-400 line-through' }}">Ventilasi & sirkulasi udara memadai</span>
                                </li>
                                <li class="flex items-center text-sm">
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full mr-3 {{ $observasi->air_listrik_memadai ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="{{ $observasi->air_listrik_memadai ? 'M5 13l4 4L19 7' : 'M6 18L18 6M6 6l12 12' }}"></path></svg>
                                    </span>
                                    <span class="{{ $observasi->air_listrik_memadai ? 'text-gray-800 font-semibold' : 'text-gray-400 line-through' }}">Tersedia fasilitas air bersih & listrik memadai</span>
                                </li>
                                <li class="flex items-center text-sm">
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full mr-3 {{ $observasi->parkir_memadai ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="{{ $observasi->parkir_memadai ? 'M5 13l4 4L19 7' : 'M6 18L18 6M6 6l12 12' }}"></path></svg>
                                    </span>
                                    <span class="{{ $observasi->parkir_memadai ? 'text-gray-800 font-semibold' : 'text-gray-400 line-through' }}">Tersedia area parkir yang mendukung</span>
                                </li>
                            </ul>
                        </div>

                        <!-- Kriteria 3: Indikator Aksesibilitas Lokasi -->
                        <div class="bg-gray-50 rounded-xl p-5 border border-gray-200 shadow-sm">
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-5 gap-3 border-b pb-3 border-gray-200">
                                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">3. Aksesibilitas Lokasi</span>
                                @php
                                    $trues = ($observasi->akses_roda4 ? 1 : 0) + ($observasi->jalan_bagus ? 1 : 0) + ($observasi->dekat_fasilitas ? 1 : 0) + ($observasi->mudah_ditemukan ? 1 : 0) + ($observasi->mudah_dijangkau ? 1 : 0);
                                    $aksesScore = max(1, $trues);
                                @endphp
                                <div class="flex items-center space-x-2 bg-primary/10 border border-primary/20 px-3 py-1 rounded-full shadow-sm">
                                    <span class="text-[10px] font-bold text-primary uppercase tracking-wider">Skor:</span>
                                    <span class="text-base font-black text-primary leading-none">{{ $aksesScore }}</span>
                                    <span class="text-[10px] text-gray-500 font-semibold leading-none">/ 5</span>
                                </div>
                            </div>
                            <ul class="space-y-3">
                                <li class="flex items-center text-sm">
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full mr-3 {{ $observasi->dekat_fasilitas ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="{{ $observasi->dekat_fasilitas ? 'M5 13l4 4L19 7' : 'M6 18L18 6M6 6l12 12' }}"></path></svg>
                                    </span>
                                    <span class="{{ $observasi->dekat_fasilitas ? 'text-gray-800 font-semibold' : 'text-gray-400 line-through' }}">Dekat dengan jalan utama</span>
                                </li>
                                <li class="flex items-center text-sm">
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full mr-3 {{ $observasi->akses_roda4 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="{{ $observasi->akses_roda4 ? 'M5 13l4 4L19 7' : 'M6 18L18 6M6 6l12 12' }}"></path></svg>
                                    </span>
                                    <span class="{{ $observasi->akses_roda4 ? 'text-gray-800 font-semibold' : 'text-gray-400 line-through' }}">Dapat dilalui kendaraan operasional (roda 4)</span>
                                </li>
                                <li class="flex items-center text-sm">
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full mr-3 {{ $observasi->jalan_bagus ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="{{ $observasi->jalan_bagus ? 'M5 13l4 4L19 7' : 'M6 18L18 6M6 6l12 12' }}"></path></svg>
                                    </span>
                                    <span class="{{ $observasi->jalan_bagus ? 'text-gray-800 font-semibold' : 'text-gray-400 line-through' }}">Kondisi jalan menuju lokasi baik</span>
                                </li>
                                <li class="flex items-center text-sm">
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full mr-3 {{ $observasi->mudah_ditemukan ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="{{ $observasi->mudah_ditemukan ? 'M5 13l4 4L19 7' : 'M6 18L18 6M6 6l12 12' }}"></path></svg>
                                    </span>
                                    <span class="{{ $observasi->mudah_ditemukan ? 'text-gray-800 font-semibold' : 'text-gray-400 line-through' }}">Mudah ditemukan oleh Google Maps / petunjuk</span>
                                </li>
                                <li class="flex items-center text-sm">
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full mr-3 {{ $observasi->mudah_dijangkau ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="{{ $observasi->mudah_dijangkau ? 'M5 13l4 4L19 7' : 'M6 18L18 6M6 6l12 12' }}"></path></svg>
                                    </span>
                                    <span class="{{ $observasi->mudah_dijangkau ? 'text-gray-800 font-semibold' : 'text-gray-400 line-through' }}">Mudah dijangkau oleh pelanggan</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION 3: DOKUMENTASI OBSERVASI -->
            <div class="pt-6 border-t border-gray-200">
                <div class="border-b border-gray-100 pb-3 mb-5 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
                    <div>
                        <h3 class="text-lg font-bold text-base-dark">Dokumentasi Observasi ({{ $observasi->dokumentasiLokasis->count() }})</h3>
                        <p class="text-sm text-gray-500">Bukti foto hasil survei lapangan (kondisi fisik bangunan, akses jalan, dan lingkungan lokasi).</p>
                    </div>
                    <span class="px-3 py-1 text-xs font-semibold bg-gray-100 text-gray-600 rounded-full border border-gray-200">Bukti Survei Lapangan</span>
                </div>

                <div x-data="{ imgModal: false, imgModalSrc: '' }">
                    @if($observasi->dokumentasiLokasis->count() > 0)
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                            @foreach($observasi->dokumentasiLokasis as $doc)
                                <div class="relative group aspect-square rounded-xl overflow-hidden border border-gray-200 cursor-pointer bg-gray-100 shadow-sm hover:shadow-md transition-all"
                                     @click="imgModalSrc = '{{ asset('storage/' . $doc->foto_path) }}'; imgModal = true;">
                                    <img src="{{ asset('storage/' . $doc->foto_path) }}" loading="lazy" class="object-cover w-full h-full transition-transform duration-300 group-hover:scale-105" alt="Dokumentasi Observasi">
                                    <div class="absolute inset-0 bg-black/0 group-hover:bg-black/30 transition-colors flex items-center justify-center">
                                        <div class="p-2 bg-white/80 rounded-full text-gray-800 opacity-0 group-hover:opacity-100 transition-opacity shadow-sm">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path></svg>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-10 bg-gray-50 border border-gray-200 rounded-xl">
                            <svg class="mx-auto h-12 w-12 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <h4 class="text-sm font-semibold text-gray-700">Tidak Ada Foto Dokumentasi</h4>
                            <p class="text-xs text-gray-400 mt-1">Belum ada foto dokumentasi yang diunggah untuk lokasi observasi ini.</p>
                        </div>
                    @endif

                    <!-- Lightbox Modal -->
                    <div x-show="imgModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/90" @keydown.escape.window="imgModal = false">
                        <div class="relative max-w-5xl w-full max-h-screen flex items-center justify-center" @click.away="imgModal = false">
                            <button @click="imgModal = false" class="absolute -top-12 right-0 text-white hover:text-gray-300 focus:outline-none">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                            <img :src="imgModalSrc" class="max-h-[85vh] max-w-full rounded shadow-2xl object-contain">
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
