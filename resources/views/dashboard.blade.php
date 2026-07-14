<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <!-- Welcome & System Status -->
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
        <div>
            <h3 class="text-xl font-bold text-base-dark">Selamat Datang, {{ Auth::user()->name }}!</h3>
            <p class="text-base-medium text-sm mt-1">
                Anda login sebagai <span class="font-semibold text-primary">{{ ucfirst(Auth::user()->roles->first()?->name ?? 'User') }}</span>.
            </p>
        </div>
        
        <!-- System Status Board -->
        <div class="flex items-center px-4 py-3 rounded-lg shadow-sm border {{ $statusType === 'success' ? 'bg-green-50 border-green-100 text-green-700' : ($statusType === 'warning' ? 'bg-yellow-50 border-yellow-100 text-yellow-700' : 'bg-red-50 border-red-100 text-red-700') }}">
            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                @if($statusType === 'success')
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                @elseif($statusType === 'warning')
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                @else
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                @endif
            </svg>
            <span class="text-sm font-medium">{{ $statusMessage }}</span>
        </div>
    </div>

    <!-- Quick Actions -->
    <!-- <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-8">
        <div class="px-4 md:px-6 py-4 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
            <h3 class="text-lg font-bold text-base-dark">Aksi Cepat</h3>
        </div>
        <div class="p-4 md:p-6 grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            
            @can('manage observasi')
            <a href="{{ route('manajer.observasi.create') }}" class="flex flex-col items-center text-center justify-center p-4 md:p-6 bg-white rounded-xl border border-gray-100 shadow-sm hover:shadow-md hover:bg-soft-green hover:border-primary transition-all duration-200 group cursor-pointer">
                <div class="w-12 h-12 bg-gray-50 rounded-full flex items-center justify-center text-gray-500 group-hover:bg-white group-hover:text-primary mb-3 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                </div>
                <span class="text-sm font-medium text-base-dark group-hover:text-primary transition-colors">Tambah Observasi</span>
            </a>
            @endcan
            
            @can('manage observasi')
            <a href="{{ route('manajer.observasi.index') }}" class="flex flex-col items-center text-center justify-center p-4 md:p-6 bg-white rounded-xl border border-gray-100 shadow-sm hover:shadow-md hover:bg-soft-green hover:border-primary transition-all duration-200 group cursor-pointer">
                <div class="w-12 h-12 bg-gray-50 rounded-full flex items-center justify-center text-gray-500 group-hover:bg-white group-hover:text-primary mb-3 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                </div>
                <span class="text-sm font-medium text-base-dark group-hover:text-primary transition-colors">Input Observasi</span>
            </a>
            @endcan

            @can('manage penilaian')
            <a href="{{ route('manajer.penilaian.index') }}" class="flex flex-col items-center text-center justify-center p-4 md:p-6 bg-white rounded-xl border border-gray-100 shadow-sm hover:shadow-md hover:bg-soft-green hover:border-primary transition-all duration-200 group cursor-pointer">
                <div class="w-12 h-12 bg-gray-50 rounded-full flex items-center justify-center text-gray-500 group-hover:bg-white group-hover:text-primary mb-3 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z"></path></svg>
                </div>
                <span class="text-sm font-medium text-base-dark group-hover:text-primary transition-colors">Penilaian Matrix</span>
            </a>
            @endcan

@can('view hasil')
            <a href="{{ route('manajer.history.index') }}" class="col-span-2 lg:col-span-1 flex flex-col items-center text-center justify-center p-4 md:p-6 bg-white rounded-xl border border-gray-100 shadow-sm hover:shadow-md hover:bg-soft-green hover:border-primary transition-all duration-200 group cursor-pointer">
                <div class="w-12 h-12 bg-gray-50 rounded-full flex items-center justify-center text-gray-500 group-hover:bg-white group-hover:text-primary mb-3 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                <span class="text-sm font-medium text-base-dark group-hover:text-primary transition-colors">Riwayat Penilaian</span>
            </a>
            @endcan
        </div>
    </div> -->

    <!-- Strategic Analytics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6 mb-8">
        <!-- Card 1 -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-base-medium">Total Observasi</p>
                <p class="text-2xl font-bold text-base-dark">{{ $totalObservasi }} Observasi</p>
            </div>
            <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
            </div>
        </div>
        
        <!-- Card 2 -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-base-medium">Observasi Sudah Dinilai</p>
                <p class="text-2xl font-bold text-green-600">{{ $sudahDiproses }} <span class="text-sm text-gray-400 font-normal">siap dihitung</span></p>
            </div>
            <div class="w-12 h-12 bg-green-50 text-green-600 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
            </div>
        </div>

        <!-- Card 3 -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-base-medium">Observasi Belum Dinilai</p>
                <p class="text-2xl font-bold {{ $belumDiproses > 0 ? 'text-red-500' : 'text-gray-900' }}">{{ $belumDiproses }} <span class="text-sm text-gray-400 font-normal">pending</span></p>
            </div>
            <div class="w-12 h-12 {{ $belumDiproses > 0 ? 'bg-red-50 text-red-600' : 'bg-gray-50 text-gray-400' }} rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>

        <!-- Card 4 -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center justify-between">
            <div class="flex-1 min-w-0 mr-4">
                <p class="text-sm font-medium text-base-medium">Rekomendasi Terbaik (#1)</p>
                <p class="text-xl font-bold text-base-dark truncate" title="{{ $lokasiTerbaik->penilaian->observasiLokasi->nama_pemilik ?? '-' }}">
                    {{ $lokasiTerbaik->penilaian->observasiLokasi->nama_pemilik ?? '-' }}
                </p>
            </div>
            <div class="w-12 h-12 bg-yellow-50 text-yellow-600 rounded-xl flex flex-shrink-0 items-center justify-center">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
            </div>
        </div>
        
        <!-- Card 5 -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-base-medium">Kriteria (Benefit / Cost)</p>
                <p class="text-2xl font-bold text-base-dark">{{ $kriteriaBenefit }} <span class="text-sm text-gray-400 font-normal">Benefit</span> / {{ $kriteriaCost }} <span class="text-sm text-gray-400 font-normal">Cost</span></p>
            </div>
            <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"></path></svg>
            </div>
        </div>

        <!-- Card 6 -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-base-medium">Kalkulasi TOPSIS Terakhir</p>
                <p class="text-base font-bold text-gray-800 mt-1">{{ $lastCalculation ? \Carbon\Carbon::parse($lastCalculation)->format('d M Y, H:i') : 'Belum Ada' }}</p>
            </div>
            <div class="w-12 h-12 bg-gray-50 text-gray-500 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>
    </div>

    <!-- Main Strategic Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 md:gap-8 mb-8">
        
        <!-- Bar Chart -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 p-4 md:p-6 flex flex-col">
            <div class="mb-4">
                <h3 class="text-lg font-bold text-gray-800">Perbandingan Preferensi TOPSIS (Top 5)</h3>
                <p class="text-sm text-gray-500">Visualisasi nilai V tertinggi dari seluruh alternatif aktif</p>
            </div>
            <div class="flex-1 relative w-full" style="min-height: 250px;">
                <canvas id="rankingChart"></canvas>
            </div>
        </div>

        <!-- Strategic Summary / Top 3 -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 flex flex-col overflow-hidden">
            <div class="p-4 md:p-6 border-b border-gray-100 bg-gray-50">
                <h3 class="text-lg font-bold text-gray-800">Ringkasan Hasil TOPSIS</h3>
                <p class="text-sm text-gray-500">3 Rekomendasi Utama</p>
            </div>
            
            <div class="p-4 md:p-6 flex-1 overflow-y-auto">
                @if($top3->isEmpty())
                    <div class="text-center py-8 text-gray-500">
                        <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <p>Belum ada data perhitungan hasil.</p>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($top3 as $index => $item)
                            <div class="flex flex-col p-3 {{ $index === 0 ? 'bg-green-50 border border-green-200' : 'bg-gray-50 border border-gray-100' }} rounded-lg transition-all hover:shadow-sm">
                                <div class="flex items-center space-x-3 mb-2">
                                    <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm {{ $index === 0 ? 'bg-green-500 text-white shadow-sm' : 'bg-gray-300 text-gray-700' }}">
                                        {{ $item->ranking }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-bold text-gray-900 truncate" title="{{ $item->penilaian->observasiLokasi->nama_pemilik }}">{{ $item->penilaian->observasiLokasi->nama_pemilik }}</p>
                                        <p class="text-xs text-gray-500 font-mono">V: {{ number_format($item->nilai_preferensi, 4) }}</p>
                                    </div>
                                </div>
                                <div class="ml-11">
                                    @if($index === 0)
                                        <span class="inline-block px-2 py-1 bg-green-100 text-green-700 text-[10px] uppercase font-bold rounded">Sangat Direkomendasikan</span>
                                    @else
                                        <span class="inline-block px-2 py-1 bg-blue-100 text-blue-700 text-[10px] uppercase font-bold rounded">Direkomendasikan</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-4 pt-4 border-t border-gray-100 text-center">
                        <a href="{{ route('manajer.history.index') }}" class="text-sm font-medium text-primary hover:text-green-700 hover:underline inline-flex items-center">
                            Lihat Riwayat Penilaian
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('rankingChart');
            if (ctx) {
                new Chart(ctx.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: {!! json_encode($chartLabels) !!},
                        datasets: [{
                            label: 'Nilai Preferensi (V)',
                            data: {!! json_encode($chartData) !!},
                            backgroundColor: 'rgba(34, 197, 94, 0.6)', 
                            borderColor: 'rgba(34, 197, 94, 1)',
                            borderWidth: 1,
                            borderRadius: 6,
                            hoverBackgroundColor: 'rgba(21, 128, 61, 0.8)',
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                suggestedMax: 1,
                                grid: {
                                    color: '#f3f4f6',
                                    drawBorder: false,
                                }
                            },
                            x: {
                                grid: {
                                    display: false,
                                    drawBorder: false,
                                },
                                ticks: {
                                    maxRotation: 45,
                                    minRotation: 0,
                                    autoSkip: false
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: 'rgba(17, 24, 39, 0.9)',
                                padding: 12,
                                titleFont: { size: 13, family: 'Arial' },
                                bodyFont: { size: 12, family: 'Arial' },
                                cornerRadius: 8,
                                displayColors: false
                            }
                        }
                    }
                });
            }
        });
    </script>
</x-app-layout>
