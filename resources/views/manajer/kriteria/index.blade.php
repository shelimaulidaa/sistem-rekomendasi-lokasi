<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h3 class="text-lg font-bold text-base-dark">Kelola Kriteria</h3>
                <p class="text-sm text-base-medium mt-1">Konfigurasi bobot kriteria penilaian. (Struktur kriteria dikunci).</p>
            </div>
            <div class="flex items-center space-x-3 w-full sm:w-auto">
                <div class="bg-gray-50 px-4 py-3 sm:py-2 rounded-lg border border-gray-100 flex items-center justify-center sm:justify-start shadow-sm w-full sm:w-auto">
                    <span class="text-sm font-semibold text-gray-600 mr-2">Total Bobot:</span>
                    <span class="text-lg font-bold {{ $totalBobot == 100 ? 'text-green-600' : 'text-amber-500' }}">{{ rtrim(rtrim(number_format((float)$totalBobot, 4, '.', ''), '0'), '.') }}%</span>
                </div>
            </div>
        </div>
    </x-slot>

    <!-- Toast Notification -->
    <x-alert />


    <!-- Warning for Total Bobot -->
    <div class="mb-6 p-4 rounded-xl flex flex-col sm:flex-row items-start sm:items-center border shadow-sm {{ $totalBobot == 100 ? 'bg-soft-green border-green-200 text-primary' : 'bg-yellow-50 border-yellow-200 text-yellow-800' }}">
        <div class="flex items-center justify-center w-12 h-12 rounded-full mb-3 sm:mb-0 sm:mr-4 {{ $totalBobot == 100 ? 'bg-white text-primary' : 'bg-white text-yellow-600' }}">
            @if($totalBobot == 100)
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            @else
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            @endif
        </div>
        <div>
            <div class="text-xl font-bold">{{ rtrim(rtrim(number_format((float)$totalBobot, 4, '.', ''), '0'), '.') }}%</div>
            <div class="text-sm mt-0.5">
                @if($totalBobot == 100)
                    Total bobot sudah mencapai 100%. Anda siap melakukan proses penilaian lokasi.
                @else
                    Total bobot saat ini {{ rtrim(rtrim(number_format((float)$totalBobot, 4, '.', ''), '0'), '.') }}%. Harus tepat 100% agar penilaian akurat. Sisa: {{ rtrim(rtrim(number_format((float)max(0, 100 - $totalBobot), 4, '.', ''), '0'), '.') }}%.
                @endif
            </div>
        </div>
    </div>


    <div class="bg-white shadow-sm border border-gray-100 sm:rounded-xl mb-8">
        <div class="p-4 sm:p-6">
            
            <!-- Search Bar -->
            <div class="mb-6 flex justify-end">
                <form method="GET" action="{{ route('manajer.kriteria.index') }}" class="flex w-full sm:w-1/2 lg:w-1/3">
                    <div class="relative w-full">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" name="search" value="{{ $search }}" placeholder="Cari kriteria..." class="w-full pl-10 pr-3 py-2 min-h-[44px] border border-gray-300 rounded-l-md focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary sm:text-sm transition-colors">
                    </div>
                    <button type="submit" class="px-4 py-2 min-h-[44px] bg-gray-50 border border-l-0 border-gray-300 text-base-dark rounded-r-md hover:bg-gray-100 transition-colors text-sm font-medium">
                        Cari
                    </button>
                </form>
            </div>

            <!-- Mobile Card View -->
            <div class="block sm:hidden space-y-4">
                @forelse ($kriterias as $kriteria)
                <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm flex flex-col gap-3">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                        <h4 class="font-bold text-base-dark text-base">{{ $kriteria->kode_kriteria }} - {{ $kriteria->nama_kriteria }}</h4>
                        <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-md {{ $kriteria->atribut == 'benefit' ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-red-50 text-red-700 border border-red-200' }}">
                            {{ ucfirst($kriteria->atribut) }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between text-sm py-1">
                        <span class="text-gray-500">Bobot</span>
                        <span class="font-bold text-base-dark">{{ rtrim(rtrim(number_format((float)$kriteria->bobot, 4, '.', ''), '0'), '.') }}%</span>
                    </div>
                    <div class="mt-2 pt-3 border-t border-gray-100">
                        <a href="{{ route('manajer.kriteria.edit', $kriteria) }}" class="w-full inline-flex items-center justify-center px-3 py-2 bg-gray-50 border border-gray-300 rounded-md text-sm font-medium text-primary hover:bg-gray-100 min-h-[44px]">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                            Edit
                        </a>
                    </div>
                </div>
                @empty
                <div class="text-center p-8 bg-gray-50 rounded-xl border border-gray-200 text-gray-500">
                    <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    <p>Tidak ada data kriteria.</p>
                </div>
                @endforelse
            </div>

            <!-- Desktop Table View -->
            <div class="hidden sm:block overflow-x-auto w-full border border-gray-100 rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-base-medium uppercase tracking-wider">Kode</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-base-medium uppercase tracking-wider">Nama Kriteria</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-base-medium uppercase tracking-wider">Atribut</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-base-medium uppercase tracking-wider">Bobot</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-semibold text-base-medium uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse ($kriterias as $kriteria)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-base-dark">{{ $kriteria->kode_kriteria }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $kriteria->nama_kriteria }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-md {{ $kriteria->atribut == 'benefit' ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-red-50 text-red-700 border border-red-200' }}">
                                    {{ ucfirst($kriteria->atribut) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-base-dark">{{ rtrim(rtrim(number_format((float)$kriteria->bobot, 4, '.', ''), '0'), '.') }}%</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('manajer.kriteria.edit', $kriteria) }}" class="inline-flex items-center text-primary hover:text-primary-dark">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                    Edit
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 whitespace-nowrap text-sm text-center text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                <p>Tidak ada data kriteria.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4">
                {{ $kriterias->links() }}
            </div>

        </div>
    </div>
</x-app-layout>
