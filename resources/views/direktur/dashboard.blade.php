<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <!-- Metrics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Card 1 -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 flex items-center space-x-4">
            <div class="p-3 bg-green-100 rounded-lg text-green-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Total Alternatif</p>
                <p class="text-2xl font-bold text-gray-900">{{ $totalObservasi }}</p>
            </div>
        </div>

        <!-- Card 2 -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 flex items-center space-x-4">
            <div class="p-3 bg-blue-100 rounded-lg text-blue-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Total Observasi</p>
                <p class="text-2xl font-bold text-gray-900">{{ $totalObservasi }}</p>
            </div>
        </div>

        <!-- Card 3 -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 flex items-center space-x-4">
            <div class="p-3 bg-purple-100 rounded-lg text-purple-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Ranking Terbaik</p>
                <p class="text-lg font-bold text-gray-900 truncate" style="max-width: 150px;">{{ $lokasiTerbaik->penilaian->observasiLokasi->nama_pemilik ?? '-' }}</p>
            </div>
        </div>

        <!-- Card 4 -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 flex items-center space-x-4">
            <div class="p-3 bg-yellow-100 rounded-lg text-yellow-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Nilai Preferensi</p>
                <p class="text-2xl font-bold text-gray-900">{{ $lokasiTerbaik ? number_format($lokasiTerbaik->nilai_preferensi, 4) : '0' }}</p>
            </div>
        </div>
    </div>

    <!-- Charts and Top 3 -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Bar Chart -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Top 5 Lokasi Alternatif</h3>
            <canvas id="rankingChart" height="100"></canvas>
        </div>

        <!-- Top 3 Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Rekomendasi Utama</h3>
            
            @if($top3->isEmpty())
                <p class="text-gray-500 text-sm italic">Belum ada data perhitungan.</p>
            @else
                <div class="space-y-4">
                    @foreach($top3 as $index => $item)
                        <div class="flex items-center justify-between p-4 {{ $index === 0 ? 'bg-green-50 border border-green-100' : 'bg-gray-50' }} rounded-lg">
                            <div class="flex items-center space-x-4">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold {{ $index === 0 ? 'bg-green-500 text-white' : 'bg-gray-300 text-gray-700' }}">
                                    {{ $item->ranking }}
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-900">{{ $item->penilaian->observasiLokasi->nama_pemilik }}</p>
                                    <p class="text-xs text-gray-500">V: {{ number_format($item->nilai_preferensi, 4) }}</p>
                                </div>
                            </div>
                            @if($index === 0)
                                <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full">#1 Choice</span>
                            @endif
                        </div>
                    @endforeach
                </div>
                <div class="mt-6">
                    <a href="{{ route('direktur.rekomendasi.index') }}" class="block w-full text-center px-4 py-2 bg-primary text-white font-medium rounded hover:bg-green-700 transition">
                        Lihat Seluruh Peringkat
                    </a>
                </div>
            @endif
            <div class="mt-4 text-xs text-gray-400 text-center">
                Terakhir dihitung: {{ $lastCalculation ? \Carbon\Carbon::parse($lastCalculation)->format('d M Y, H:i') : '-' }}
            </div>
        </div>

    </div>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('rankingChart').getContext('2d');
            const rankingChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($chartLabels) !!},
                    datasets: [{
                        label: 'Nilai Preferensi',
                        data: {!! json_encode($chartData) !!},
                        backgroundColor: 'rgba(34, 197, 94, 0.5)', // tailwind green-500 with opacity
                        borderColor: 'rgba(34, 197, 94, 1)',
                        borderWidth: 1,
                        borderRadius: 4,
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 1
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        });
    </script>
</x-app-layout>
