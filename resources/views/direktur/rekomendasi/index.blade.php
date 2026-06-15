<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Hasil Rekomendasi TOPSIS') }}
            </h2>
            <div class="text-sm text-gray-500">
                Terakhir dihitung: <span class="font-bold text-gray-800">{{ $lastCalculation ? \Carbon\Carbon::parse($lastCalculation)->format('d M Y, H:i') : '-' }}</span>
            </div>
        </div>
    </x-slot>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
        
        <!-- Filter & Actions -->
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 space-y-4 md:space-y-0">
            <form method="GET" action="{{ route('direktur.rekomendasi.index') }}" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full md:w-auto">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama lokasi..." class="w-full sm:w-auto min-h-[44px] border-gray-300 focus:border-primary focus:ring-primary rounded-md shadow-sm text-sm">
                
                <select name="status" class="w-full sm:w-auto min-h-[44px] border-gray-300 focus:border-primary focus:ring-primary rounded-md shadow-sm text-sm">
                    <option value="">Semua Status</option>
                    <option value="sangat_direkomendasikan" {{ request('status') === 'sangat_direkomendasikan' ? 'selected' : '' }}>Sangat Direkomendasikan (Peringkat 1)</option>
                    <option value="direkomendasikan" {{ request('status') === 'direkomendasikan' ? 'selected' : '' }}>Direkomendasikan (Peringkat 2-3)</option>
                    <option value="dipertimbangkan" {{ request('status') === 'dipertimbangkan' ? 'selected' : '' }}>Dipertimbangkan (> Peringkat 3)</option>
                </select>

                <div class="flex items-center gap-2">
                    <button type="submit" class="w-full sm:w-auto min-h-[44px] px-4 py-2 bg-gray-800 text-white text-sm font-medium rounded hover:bg-gray-700 transition">Filter</button>
                    @if(request()->hasAny(['search', 'status']))
                        <a href="{{ route('direktur.rekomendasi.index') }}" class="w-full sm:w-auto min-h-[44px] inline-flex items-center justify-center px-4 py-2 bg-gray-100 text-gray-600 text-sm font-medium rounded hover:bg-gray-200 transition">Reset</a>
                    @endif
                </div>
            </form>

            <div class="flex flex-col sm:flex-row items-stretch gap-3 w-full md:w-auto mt-4 md:mt-0">
                <a href="{{ route('direktur.rekomendasi.export.pdf') }}" target="_blank" class="w-full sm:w-auto justify-center min-h-[44px] flex items-center px-4 py-2 bg-red-50 text-red-600 text-sm font-medium rounded border border-red-200 hover:bg-red-100 transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Export PDF
                </a>
                <a href="{{ route('direktur.rekomendasi.export.excel') }}" class="w-full sm:w-auto justify-center min-h-[44px] flex items-center px-4 py-2 bg-green-50 text-green-600 text-sm font-medium rounded border border-green-200 hover:bg-green-100 transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path></svg>
                    Export Excel
                </a>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto w-full">
            <table class="w-full text-left border-collapse min-w-[800px]">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="p-4 font-semibold text-gray-600 text-sm">Peringkat</th>
                        <th class="p-4 font-semibold text-gray-600 text-sm">Nama Lokasi</th>
                        <th class="p-4 font-semibold text-gray-600 text-sm">Nilai Preferensi (V)</th>
                        <th class="p-4 font-semibold text-gray-600 text-sm">Status Rekomendasi</th>
                        <th class="p-4 font-semibold text-gray-600 text-sm">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($results as $item)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="p-4">
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full {{ $item->ranking === 1 ? 'bg-green-500 text-white font-bold' : 'bg-gray-100 text-gray-700 font-medium' }}">
                                    {{ $item->ranking }}
                                </span>
                            </td>
                            <td class="p-4">
                                <span class="font-medium text-gray-900">{{ $item->penilaian->lokasi->nama_lokasi }}</span>
                                @if($item->ranking === 1)
                                    <svg class="w-4 h-4 inline text-yellow-500 ml-1" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                @endif
                            </td>
                            <td class="p-4 font-mono font-medium text-gray-800">
                                {{ number_format($item->nilai_preferensi, 4) }}
                            </td>
                            <td class="p-4">
                                @if($item->ranking === 1)
                                    <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full">Sangat Direkomendasikan</span>
                                @elseif($item->ranking <= 3)
                                    <span class="px-3 py-1 bg-blue-100 text-blue-700 text-xs font-medium rounded-full">Direkomendasikan</span>
                                @else
                                    <span class="px-3 py-1 bg-gray-100 text-gray-600 text-xs font-medium rounded-full">Dipertimbangkan</span>
                                @endif
                            </td>
                            <td class="p-4 text-center">
                                <a href="{{ route('direktur.rekomendasi.show', $item->hasil_id) }}" class="w-full sm:w-auto inline-flex justify-center items-center px-3 py-2 min-h-[44px] bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200 rounded-md text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    Detail Data
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-4 text-center text-gray-500 py-8">
                                Belum ada data hasil rekomendasi TOPSIS yang dihitung.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-4">
            {{ $results->links() }}
        </div>
    </div>
</x-app-layout>
