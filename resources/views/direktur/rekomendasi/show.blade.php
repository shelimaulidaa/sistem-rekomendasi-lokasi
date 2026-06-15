<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-4">
            <a href="{{ route('direktur.rekomendasi.index') }}" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detail Analisis TOPSIS: ') }} <span class="text-primary">{{ $hasil->penilaian->lokasi->nama_lokasi }}</span>
            </h2>
        </div>
    </x-slot>

    <!-- Top Summary Cards -->
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 text-center">
            <p class="text-sm font-medium text-gray-500 mb-1">Ranking</p>
            <div class="text-4xl font-bold text-gray-900 flex items-center justify-center">
                #{{ $hasil->ranking }}
                @if($hasil->ranking === 1)
                    <svg class="w-8 h-8 text-yellow-500 ml-2" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                @endif
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 text-center">
            <p class="text-sm font-medium text-gray-500 mb-1">Nilai Preferensi (V)</p>
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
                    <p class="text-sm text-gray-500 font-medium">Nama Lokasi</p>
                    <p class="text-gray-900 font-semibold">{{ $hasil->penilaian->lokasi->nama_lokasi }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 font-medium">Alamat Lengkap</p>
                    <p class="text-gray-900">{{ $hasil->penilaian->lokasi->alamat }}</p>
                </div>
                @if($hasil->penilaian->observasiLokasi)
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-gray-50 p-3 rounded-lg">
                        <p class="text-xs text-gray-500">Kepadatan Penduduk</p>
                        <p class="text-sm font-bold text-gray-900">{{ number_format($hasil->penilaian->observasiLokasi->kepadatan_penduduk, 0, ',', '.') }} Jiwa/Km²</p>
                    </div>
                    <div class="bg-gray-50 p-3 rounded-lg">
                        <p class="text-xs text-gray-500">Jarak RPH</p>
                        <p class="text-sm font-bold text-gray-900">{{ number_format($hasil->penilaian->observasiLokasi->jarak_rph, 2, ',', '.') }} Km</p>
                    </div>
                    <div class="bg-gray-50 p-3 rounded-lg">
                        <p class="text-xs text-gray-500">Biaya Sewa</p>
                        <p class="text-sm font-bold text-gray-900">Rp {{ number_format($hasil->penilaian->observasiLokasi->biaya_sewa, 0, ',', '.') }}</p>
                    </div>
                    <div class="bg-gray-50 p-3 rounded-lg">
                        <p class="text-xs text-gray-500">Jumlah Kompetitor</p>
                        <p class="text-sm font-bold text-gray-900">{{ $hasil->penilaian->observasiLokasi->jumlah_kompetitor }} Outlet</p>
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
            <h3 class="text-lg font-bold text-gray-800">Transparansi Matriks TOPSIS</h3>
            <span class="text-xs text-gray-500 bg-white px-2 py-1 rounded border">Validasi Thesis</span>
        </div>
        <div class="p-0 overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 border-b border-gray-200">
                        <th class="p-4 font-semibold text-gray-600 text-sm w-1/4">Kriteria</th>
                        <th class="p-4 font-semibold text-gray-600 text-sm text-center">Nilai Mentah (X)</th>
                        <th class="p-4 font-semibold text-gray-600 text-sm text-center">Nilai Ternormalisasi (R)</th>
                        <th class="p-4 font-semibold text-gray-600 text-sm text-center">Matriks Bobot (Y)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($kriterias as $k)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="p-4">
                            <span class="font-bold text-gray-800">{{ $k->kode_kriteria }}</span> - {{ $k->nama_kriteria }}
                            <div class="text-xs text-gray-500 mt-1">Bobot: {{ $k->bobot }} | Atribut: {{ ucfirst($k->atribut) }}</div>
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
