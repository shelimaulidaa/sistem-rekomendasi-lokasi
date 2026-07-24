<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-base-dark leading-tight">
                    {{ __('Riwayat Penilaian') }}
                </h2>
                <p class="text-sm text-base-medium mt-1">Laporan hasil akhir rekomendasi pemilihan lokasi cabang.</p>
            </div>
            <div class="w-full sm:w-auto">
                <a href="{{ route('manajer.history.export.pdf', ['batch_id' => $activeBatchId]) }}" class="w-full sm:w-auto justify-center inline-flex items-center px-4 py-2 min-h-[44px] bg-primary border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition ease-in-out duration-150 shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Export PDF
                </a>
            </div>
        </div>
    </x-slot>

    <!-- Periode Filter -->
    <div class="mb-6 flex flex-col sm:flex-row justify-end items-center">
        <form action="{{ route('manajer.history.index') }}" method="GET" class="w-full sm:w-auto flex items-center gap-2">
            <label for="batch_id" class="text-sm font-medium text-gray-700 whitespace-nowrap">Pilih Periode:</label>
            <select name="batch_id" onchange="this.form.submit()" class="block w-full sm:w-64 rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm py-2 px-3 h-[42px] bg-white">
                <option value="">-- Semua Periode --</option>
                @foreach($batches as $batch)
                    <option value="{{ $batch->id }}" {{ $activeBatchId == $batch->id ? 'selected' : '' }}>
                        {{ $batch->nama_batch }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    <!-- Executive Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-6">
        <!-- Lokasi Terbaik -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center justify-between">
            <div class="min-w-0 mr-4">
                <p class="text-sm font-medium text-base-medium">Lokasi Terbaik (#1)</p>
                <p class="text-lg sm:text-xl font-bold text-green-700 mt-1 truncate" title="{{ $lokasiTerbaik->penilaian->observasiLokasi->nama_pemilik ?? '-' }}">
                    {{ Str::words($lokasiTerbaik->penilaian->observasiLokasi->nama_pemilik ?? '-', 3) }}
                </p>
            </div>
            <div class="w-12 h-12 bg-green-50 text-green-600 rounded-xl flex flex-shrink-0 items-center justify-center">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 2a.75.75 0 01.673.418l2.25 4.562 5.035.732a.75.75 0 01.415 1.279l-3.643 3.551.86 5.018a.75.75 0 01-1.088.791L10 16.052l-4.502 2.368a.75.75 0 01-1.088-.79l.86-5.018-3.643-3.55a.75.75 0 01.415-1.28l5.035-.732 2.25-4.562A.75.75 0 0110 2z" clip-rule="evenodd"></path></svg>
            </div>
        </div>

        <!-- Nilai Preferensi -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-base-medium">Skor Rekomendasi</p>
                <p class="text-xl sm:text-2xl font-bold text-base-dark mt-1">
                    {{ $lokasiTerbaik ? number_format($lokasiTerbaik->nilai_preferensi, 4) : '-' }}
                </p>
            </div>
            <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
            </div>
        </div>

        <!-- Total Alternatif -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-base-medium">Total Alternatif</p>
                <p class="text-xl sm:text-2xl font-bold text-base-dark mt-1">{{ $totalAlternatif }} <span class="text-sm text-gray-400 font-normal">Lokasi</span></p>
            </div>
            <div class="w-12 h-12 bg-purple-50 text-purple-600 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 01-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
            </div>
        </div>

        <!-- Last Calculation -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-base-medium">Kalkulasi Terakhir</p>
                <p class="text-sm sm:text-base font-bold text-base-dark mt-1">{{ $lastCalculation }}</p>
            </div>
            <div class="w-12 h-12 bg-gray-50 text-gray-500 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>
    </div>

    <!-- Main Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-8">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
            <h3 class="text-lg font-bold text-base-dark">Tabel Peringkat Rekomendasi</h3>
        </div>
        
        <div class="overflow-x-auto w-full">
            <table class="w-full text-left border-collapse min-w-[600px]">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="p-4 font-semibold text-sm text-gray-600 w-16 sm:w-24 text-center">Peringkat</th>
                        <th class="p-4 font-semibold text-sm text-gray-600">Lokasi / Alamat</th>
                        <th class="p-4 font-semibold text-sm text-gray-600 w-40 sm:w-48 text-right">Skor Rekomendasi</th>
                        <th class="p-4 font-semibold text-sm text-gray-600 w-48 sm:w-56 text-center">Status Rekomendasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($hasil as $index => $item)
                        @php
                            $isTop1 = $item->ranking === 1;
                            $isTop3 = $item->ranking > 1 && $item->ranking <= 3;
                        @endphp
                        <tr class="{{ $isTop1 ? 'bg-green-50' : 'hover:bg-gray-50' }} transition-colors">
                            <td class="p-4">
                                <div class="flex items-center justify-center w-8 h-8 mx-auto rounded-full font-bold text-sm {{ $isTop1 ? 'bg-green-500 text-white shadow-md' : ($isTop3 ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600') }}">
                                    {{ $item->ranking }}
                                </div>
                            </td>
                            <td class="p-4">
                                <div class="flex items-start sm:items-center space-x-3">
                                    @if($isTop1)
                                        <svg class="w-5 h-5 text-yellow-500 flex-shrink-0 mt-0.5 sm:mt-0" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                    @endif
                                    <div>
                                        <p class="font-bold {{ $isTop1 ? 'text-green-800 text-base' : 'text-gray-900 text-sm' }}">{{ head(explode(',', $item->penilaian->observasiLokasi->alamat_lengkap)) }}</p>
                                        <p class="text-xs text-gray-500 mt-1">Pemilik: {{ $item->penilaian->observasiLokasi->nama_pemilik }}</p>
                                        <div class="flex gap-2 text-[10px] mt-1.5">
                                            <span class="text-gray-600 bg-gray-50 border px-1.5 py-0.5 rounded">Jarak RPH: <strong>{{ rtrim(rtrim(number_format((float)$item->penilaian->observasiLokasi->jarak_rph, 4, '.', ''), '0'), '.') }} KM</strong></span>
                                            <span class="text-gray-600 bg-gray-50 border px-1.5 py-0.5 rounded">Kompetitor: <strong>{{ $item->penilaian->observasiLokasi->jumlah_kompetitor }} titik</strong></span>
                                        </div>
                                        <p class="text-xs text-gray-400 mt-1.5 truncate max-w-[150px] sm:max-w-xs" title="{{ $item->penilaian->observasiLokasi->alamat_lengkap }}">{{ $item->penilaian->observasiLokasi->alamat_lengkap }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="p-4 text-right">
                                <span class="font-mono {{ $isTop1 ? 'text-green-700 font-bold text-lg' : 'text-gray-700 font-medium' }}">
                                    {{ number_format($item->nilai_preferensi, 4) }}
                                </span>
                            </td>
                            <td class="p-4 text-center">
                                @if($isTop1)
                                    <span class="inline-flex items-center px-2.5 sm:px-3 py-1 rounded-full text-[10px] sm:text-xs font-bold bg-green-100 text-green-800 border border-green-200 shadow-sm uppercase tracking-wider">
                                        Sangat Direkomendasikan
                                    </span>
                                @elseif($isTop3)
                                    <span class="inline-flex items-center px-2.5 sm:px-3 py-1 rounded-full text-[10px] sm:text-xs font-bold bg-blue-100 text-blue-800 border border-blue-200 uppercase tracking-wider">
                                        Direkomendasikan
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 sm:px-3 py-1 rounded-full text-[10px] sm:text-xs font-bold bg-gray-100 text-gray-700 border border-gray-200 uppercase tracking-wider">
                                        Dipertimbangkan
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-8 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    <p class="text-base font-medium">Belum ada data hasil penilaian.</p>
                                    <p class="text-sm mt-1">Silakan lakukan proses penilaian lokasi terlebih dahulu.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
