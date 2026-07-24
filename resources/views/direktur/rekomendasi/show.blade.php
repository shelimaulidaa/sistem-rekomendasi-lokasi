<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-4">
            <a href="{{ route('direktur.rekomendasi.index') }}" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detail Analisis Rekomendasi: ') }} <span class="text-primary">{{ $hasil->penilaian->observasiLokasi->nama_pemilik }}</span>
            </h2>
        </div>
    </x-slot>

    <!-- Top Summary Cards -->
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 text-center">
            <p class="text-sm font-medium text-gray-500 mb-1">Peringkat</p>
            <div class="text-4xl font-bold text-gray-900 flex items-center justify-center">
                #{{ $hasil->ranking }}
                @if($hasil->ranking === 1)
                    <svg class="w-8 h-8 text-yellow-500 ml-2" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                @endif
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 text-center">
            <p class="text-sm font-medium text-gray-500 mb-1">Skor Rekomendasi</p>
            <div class="text-4xl font-bold font-mono text-primary">{{ number_format($hasil->nilai_preferensi, 4) }}</div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 text-center flex flex-col justify-center">
            <p class="text-sm font-medium text-gray-500 mb-2">Status Rekomendasi</p>
            <div>
                @if($hasil->ranking === 1)
                    <span class="px-4 py-2 bg-green-100 text-green-700 text-sm font-bold rounded-full">Sangat Direkomendasikan</span>
                @elseif($hasil->ranking <= 3)
                    <span class="px-4 py-2 bg-blue-100 text-blue-700 text-sm font-medium rounded-full">Direkomendasikan</span>
                @else
                    <span class="px-4 py-2 bg-gray-100 text-gray-600 text-sm font-medium rounded-full">Dipertimbangkan</span>
                @endif
            </div>
        </div>
    </div>

    <div class="w-full gap-8 mb-8">
        <!-- Radar Chart -->
        <!-- <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col">
            <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Visualisasi Matriks Keputusan</h3>
            <div class="flex-1 relative w-full flex justify-center items-center" style="min-height: 300px;">
                <canvas id="radarChart"></canvas>
            </div>
            <p class="text-xs text-gray-500 text-center mt-4">
                Grafik radar menunjukkan perbandingan nilai mentah vs nilai bobot normalisasi untuk setiap kriteria.
            </p>
        </div> -->

        <!-- Detail Lokasi -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Informasi Alternatif</h3>
            <div class="space-y-4">
                <div>
                    <p class="text-sm text-gray-500 font-medium">Nama Pemilik</p>
                    <p class="text-gray-900 font-semibold">{{ $hasil->penilaian->observasiLokasi->nama_pemilik }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 font-medium">Alamat Lengkap</p>
                    <p class="text-gray-900">{{ $hasil->penilaian->observasiLokasi->alamat_lengkap }}</p>
                </div>
                @if($hasil->penilaian->observasiLokasi)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                    <!-- Biaya Sewa -->
                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-100 col-span-1 md:col-span-2">
                        <p class="text-xs text-gray-500 font-bold uppercase tracking-wider">Biaya Sewa / Tahun</p>
                        <p class="text-lg font-black text-green-700 mt-1">Rp {{ number_format($hasil->penilaian->observasiLokasi->harga_sewa, 0, ',', '.') }}</p>
                    </div>

                    <!-- RPH Card -->
                    <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
                        <div class="flex items-center justify-between border-b pb-3 mb-4">
                            <div>
                                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Rumah Potong Hewan (RPH) Terdekat</span>
                                <h5 class="text-base font-extrabold text-gray-800 mt-1">{{ $spatialData['nearest_rph_name'] ?? '-' }}</h5>
                            </div>
                            <div class="p-2 bg-green-50 text-green-700 rounded-lg">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                            </div>
                        </div>
                        
                        @if(isset($spatialData['rph_list']) && count($spatialData['rph_list']) > 0)
                            @php $nearest = $spatialData['rph_list'][0]; @endphp
                            <div class="space-y-2 text-xs">
                                <div class="flex justify-between items-center bg-gray-50 p-2 rounded border">
                                    <span class="font-medium text-gray-600">Jarak ke Lokasi</span>
                                    <span class="font-bold text-gray-900 bg-white px-2 py-0.5 rounded border">{{ $nearest['distance'] }} km</span>
                                </div>
                                @if(!empty($nearest['alamat']))
                                    <div class="bg-gray-50 p-2.5 rounded border">
                                        <span class="font-semibold text-gray-500 block mb-0.5">Alamat RPH:</span>
                                        <p class="text-gray-700 leading-relaxed font-medium">{{ $nearest['alamat'] }}</p>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="space-y-2 text-xs">
                                <div class="flex justify-between items-center bg-gray-50 p-2 rounded border">
                                    <span class="font-medium text-gray-600">Jarak (Input Manual)</span>
                                    <span class="font-bold text-gray-900 bg-white px-2 py-0.5 rounded border">{{ rtrim(rtrim(number_format((float)$hasil->penilaian->observasiLokasi->jarak_rph, 4, '.', ''), '0'), '.') }} KM</span>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Competitors Card -->
                    <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
                        <div class="flex items-center justify-between border-b pb-3 mb-4">
                            <div>
                                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Jumlah Kompetitor</span>
                                <h5 class="text-lg font-extrabold text-primary mt-1">
                                    {{ $hasil->penilaian->observasiLokasi->jumlah_kompetitor }} titik
                                    @if(isset($spatialData['competitors_avg_rating']) && $spatialData['competitors_avg_rating'] > 0)
                                        <span class="text-xs font-bold text-yellow-600 bg-yellow-50 border border-yellow-200 rounded px-1.5 py-0.5 ml-1">
                                            ★ {{ $spatialData['competitors_avg_rating'] }} / 5
                                        </span>
                                    @endif
                                </h5>
                            </div>
                            <div class="p-2 bg-primary/10 rounded-lg text-primary">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            </div>
                        </div>
                        
                        @if(isset($spatialData['competitors_list']) && count($spatialData['competitors_list']) > 0)
                            <div class="space-y-3 max-h-[160px] overflow-y-auto pr-1">
                                @foreach($spatialData['competitors_list'] as $comp)
                                    <div class="p-2.5 bg-gray-50 rounded-lg border border-gray-100 text-xs">
                                        <div class="flex justify-between items-start">
                                            <span class="font-bold text-gray-800 text-[11px]">{{ $comp['nama'] }}</span>
                                            <span class="text-[10px] text-gray-500 font-semibold bg-white border px-1.5 py-0.5 rounded flex-shrink-0 ml-2">{{ $comp['distance'] }} km</span>
                                        </div>
                                        @if(!empty($comp['alamat']))
                                            <p class="text-gray-500 mt-1 text-[11px] leading-relaxed truncate" title="{{ $comp['alamat'] }}">{{ $comp['alamat'] }}</p>
                                        @endif
                                        @if(!empty($comp['rating']))
                                            <div class="flex items-center text-yellow-500 mt-1 gap-1">
                                                <svg class="w-3 h-3 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                                <span class="font-bold text-[10px] text-gray-700">{{ $comp['rating'] }} / 5</span>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-xs text-gray-500 italic">Tidak ditemukan kompetitor terdekat dalam radius pencarian.</p>
                        @endif
                    </div>
                </div>
                @endif
                <div class="text-xs text-gray-400 mt-4 pt-4 border-t">
                    Kalkulasi dihitung pada: {{ \Carbon\Carbon::parse($hasil->tanggal_hitung)->format('d F Y, H:i:s') }}
                </div>
            </div>
        </div>
    </div>

    <!-- Matrix Tables -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-8">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
            <h3 class="text-lg font-bold text-gray-800">Rincian Skor Penilaian Kriteria</h3>
            <span class="text-xs text-gray-500 bg-white px-2 py-1 rounded border">Rincian Kriteria</span>
        </div>
        <div class="p-0 overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 border-b border-gray-200">
                        <th class="p-4 font-semibold text-gray-600 text-sm w-1/4">Kriteria</th>
                        <th class="p-4 font-semibold text-gray-600 text-sm text-center">Skor Awal</th>
                        <th class="p-4 font-semibold text-gray-600 text-sm text-center">Skor Standar</th>
                        <th class="p-4 font-semibold text-gray-600 text-sm text-center">Skor Akhir Kriteria</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($kriterias as $k)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="p-4">
                            <span class="font-bold text-gray-800">{{ $k->kode_kriteria }}</span> - {{ $k->nama_kriteria }}
                            <div class="text-xs text-gray-500 mt-1">Bobot: {{ $k->bobot }}%</div>
                        </td>
                        <td class="p-4 text-center font-mono font-medium text-gray-900">
                            {{ $rawMatrix[$k->kriteria_id] ?? 0 }}
                        </td>
                        <td class="p-4 text-center font-mono text-gray-700">
                            {{ number_format($normalizedMatrix[$k->kriteria_id] ?? 0, 6) }}
                        </td>
                        <td class="p-4 text-center font-mono text-primary font-medium">
                            {{ number_format($weightedMatrix[$k->kriteria_id] ?? 0, 6) }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    </div>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('radarChart').getContext('2d');
            const radarChart = new Chart(ctx, {
                type: 'radar',
                data: {
                    labels: {!! json_encode($chartLabels) !!},
                    datasets: [
                        {
                            label: 'Matriks Ternormalisasi Terbobot (Y)',
                            data: {!! json_encode($chartDataWeighted) !!},
                            backgroundColor: 'rgba(34, 197, 94, 0.2)', // green
                            borderColor: 'rgba(34, 197, 94, 1)',
                            pointBackgroundColor: 'rgba(34, 197, 94, 1)',
                            borderWidth: 2,
                        },
                        {
                            label: 'Nilai Mentah Matriks (X)',
                            data: {!! json_encode($chartDataRaw) !!},
                            backgroundColor: 'rgba(59, 130, 246, 0.1)', // blue
                            borderColor: 'rgba(59, 130, 246, 0.5)',
                            pointBackgroundColor: 'rgba(59, 130, 246, 0.5)',
                            borderWidth: 1,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        r: {
                            beginAtZero: true
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                boxWidth: 8
                            }
                        }
                    }
                }
            });
        });
    </script>
</x-app-layout>
