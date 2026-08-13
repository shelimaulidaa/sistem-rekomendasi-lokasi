<x-app-layout>
    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-base-dark">Hasil Observasi & Penilaian</h2>
            <p class="text-sm text-base-medium mt-1">Daftar lokasi observasi dari periode yang telah selesai dinilai dan diberikan rekomendasi.</p>
        </div>
        @if($activePeriodeId)
        <a href="{{ route('manajer.hasil.export.pdf', ['periode_id' => $activePeriodeId]) }}" class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2 min-h-[44px] bg-red-600 border border-transparent rounded-md font-medium text-xs text-white uppercase tracking-widest hover:bg-red-700 transition ease-in-out duration-150">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            Export PDF Hasil
        </a>
        @endif
    </div>

    <x-alert />

    <div class="bg-white shadow-sm border border-gray-100 sm:rounded-xl mb-8">
        <div class="p-4 sm:p-6">
            
            <!-- Toolbar & Filter -->
            <div class="mb-6 flex flex-col sm:flex-row justify-between items-stretch sm:items-center gap-3">
                <div class="flex items-center space-x-2">
                    <span class="text-xs uppercase font-bold text-gray-500">Filter Periode:</span>
                </div>
                
                <form method="GET" action="{{ route('manajer.hasil.index') }}" class="flex flex-col sm:flex-row w-full sm:w-auto gap-3">
                    <select name="periode_id" onchange="this.form.submit()" class="block w-full sm:w-56 rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm py-2 px-3 h-[44px] bg-white font-semibold">
                        @forelse($periodes as $periode)
                            <option value="{{ $periode->id }}" {{ $activePeriodeId == $periode->id ? 'selected' : '' }}>
                                {{ $periode->nama_periode }} (Selesai)
                            </option>
                        @empty
                            <option value="">-- Belum Ada Periode Dihitung --</option>
                        @endforelse
                    </select>
                    
                    <div class="flex w-full sm:w-auto">
                        <div class="relative w-full sm:w-64">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                            <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama lokasi / pemilik..." class="w-full pl-10 pr-3 py-2 min-h-[44px] border border-gray-300 rounded-l-md focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary sm:text-sm transition-colors">
                        </div>
                        <button type="submit" class="px-4 py-2 min-h-[44px] bg-gray-50 border border-l-0 border-gray-300 text-base-dark rounded-r-md hover:bg-gray-100 transition-colors text-sm font-medium">
                            Cari
                        </button>
                    </div>
                </form>
            </div>

            <!-- Table View -->
            <div class="overflow-x-auto w-full border border-gray-100 rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-base-medium uppercase tracking-wider w-24">Peringkat</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-base-medium uppercase tracking-wider">Lokasi / Alamat</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-base-medium uppercase tracking-wider">Pemilik</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-base-medium uppercase tracking-wider">Skor Rekomendasi</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-base-medium uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-semibold text-base-medium uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse ($observasis as $observasi)
                        @php
                            $rank = $observasi->hasilPerhitungan?->ranking;
                            $pref = $observasi->hasilPerhitungan?->nilai_preferensi;
                            
                            $rankBadge = match(true) {
                                $rank === 1 => 'bg-emerald-500 text-white font-black',
                                $rank <= 3 => 'bg-amber-500 text-white font-bold',
                                default => 'bg-gray-600 text-white font-bold',
                            };
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($rank)
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full text-xs {{ $rankBadge }} shadow-sm">
                                        #{{ $rank }}
                                    </span>
                                @else
                                    <span class="text-xs text-gray-400 font-medium">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-bold text-base-dark">{{ head(explode(',', $observasi->alamat_lengkap)) }}</div>
                                <div class="text-xs text-gray-400 mt-1 truncate max-w-xs" title="{{ $observasi->alamat_lengkap }}">{{ $observasi->alamat_lengkap }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ $observasi->nama_pemilik }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($pref !== null)
                                    <span class="font-mono font-bold text-emerald-600 text-base">
                                        {{ number_format($pref, 4) }}
                                    </span>
                                @else
                                    <span class="text-xs text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                    ✓ Selesai Dinilai
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('manajer.observasi.show', [$observasi, 'ref' => 'hasil']) }}" title="Detail" class="p-2 bg-blue-50 border border-blue-200 text-blue-600 hover:bg-blue-100 rounded-lg transition-colors inline-flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                <p class="text-base font-bold text-gray-700">Belum ada data observasi yang dihitung.</p>
                                <p class="text-sm text-gray-400 mt-1 mb-4">Lakukan proses rekomendasi terlebih dahulu pada menu Observasi Belum Dihitung.</p>

                                <a href="{{ route('manajer.observasi.index') }}" class="inline-flex items-center px-4 py-2 bg-primary text-white text-xs font-bold rounded-lg hover:bg-green-700 transition-colors shadow-sm">
                                    Ke Menu Observasi Belum Dihitung &rarr;
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</x-app-layout>
