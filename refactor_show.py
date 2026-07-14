import re

with open('resources/views/manajer/observasi/show.blade.php', 'r') as f:
    content = f.read()

# We need to replace the "Informasi Bangunan" block (lines 20-105 approx)
info_bangunan_pattern = re.compile(r'(<!-- Info Utama -->\s*<div class="bg-white overflow-hidden shadow-sm border border-gray-100 sm:rounded-xl">.*?)(<!-- Hasil Penilaian \(TOPSIS View\) -->)', re.DOTALL)

new_tabs_block = """<!-- Tabs: Kondisi Bangunan & Lokasi -->
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
                                <p class="text-sm font-bold text-base-dark">{{ $observasi->luas_bangunan }} m²</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 font-medium uppercase tracking-wider mb-1">Luas Tanah</p>
                                <p class="text-sm font-bold text-base-dark">{{ $observasi->luas_tanah }} m²</p>
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

                <!-- Hasil Penilaian (TOPSIS View) -->"""

new_content = info_bangunan_pattern.sub(new_tabs_block, content)

with open('resources/views/manajer/observasi/show.blade.php', 'w') as f:
    f.write(new_content)

print("Updated show.blade.php")
