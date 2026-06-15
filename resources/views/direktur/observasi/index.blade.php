<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-base-dark leading-tight">
                    {{ __('Hasil Observasi Lapangan') }}
                </h2>
                <p class="text-sm text-base-medium mt-1">Pantau seluruh data hasil survei lapangan secara transparan.</p>
            </div>
        </div>
    </x-slot>

    <!-- Main Content -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        
        <!-- Toolbar & Search -->
        <div class="p-4 sm:p-6 border-b border-gray-100 bg-gray-50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <h3 class="text-lg font-bold text-base-dark">Daftar Observasi</h3>
            
            <form action="{{ route('direktur.observasi.index') }}" method="GET" class="w-full sm:w-auto">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" name="search" value="{{ $search }}" 
                        class="block w-full sm:w-64 pl-10 pr-3 py-2 min-h-[44px] border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-primary focus:border-primary sm:text-sm transition duration-150 ease-in-out" 
                        placeholder="Cari nama lokasi...">
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-full">
                <thead>
                    <tr class="bg-white border-b border-gray-200">
                        <th class="p-4 font-semibold text-sm text-gray-600">Nama Lokasi</th>
                        <th class="p-4 font-semibold text-sm text-gray-600">Observer</th>
                        <th class="p-4 font-semibold text-sm text-gray-600">Tanggal</th>
                        <th class="p-4 font-semibold text-sm text-gray-600">Jenis Bangunan</th>
                        <th class="p-4 font-semibold text-sm text-gray-600 text-center">Status Penilaian</th>
                        <th class="p-4 font-semibold text-sm text-gray-600 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($observasis as $item)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="p-4">
                                <div class="font-bold text-gray-900">{{ $item->lokasi->nama_lokasi }}</div>
                                <div class="text-xs text-gray-500 mt-1 truncate max-w-[200px]" title="{{ $item->lokasi->alamat }}">
                                    {{ $item->lokasi->alamat }}
                                </div>
                            </td>
                            <td class="p-4 text-sm text-gray-700">
                                {{ $item->user->name ?? 'Sistem' }}
                            </td>
                            <td class="p-4 text-sm text-gray-700">
                                {{ $item->tanggal_observasi ? \Carbon\Carbon::parse($item->tanggal_observasi)->translatedFormat('d M Y') : '-' }}
                            </td>
                            <td class="p-4 text-sm text-gray-700">
                                <span class="capitalize">{{ $item->jenis_bangunan }}</span>
                            </td>
                            <td class="p-4 text-center">
                                @if($item->penilaians->count() > 0)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                        Sudah Dinilai
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200">
                                        Belum Dinilai
                                    </span>
                                @endif
                            </td>
                            <td class="p-4 text-center">
                                <a href="{{ route('direktur.observasi.show', $item->observasi_id) }}" 
                                   class="w-full sm:w-auto inline-flex justify-center items-center px-3 py-2 min-h-[44px] bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200 rounded-md text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <p class="text-base font-medium">Belum ada data observasi.</p>
                                    <p class="text-sm mt-1">Data observasi akan muncul setelah Manajer melakukan input ke sistem.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($observasis->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 bg-white">
            {{ $observasis->links() }}
        </div>
        @endif
    </div>
</x-app-layout>
