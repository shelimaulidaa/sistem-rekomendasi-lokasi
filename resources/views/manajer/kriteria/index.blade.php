<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h3 class="text-lg font-bold text-base-dark">Kelola Kriteria</h3>
                <p class="text-sm text-base-medium mt-1">Konfigurasi bobot kriteria penilaian per periode.</p>
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

    <!-- Periode Selection & Lock Status Bar -->
    <div class="bg-white p-4 sm:p-6 rounded-xl border border-gray-100 shadow-sm mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <form method="GET" action="{{ route('manajer.kriteria.index') }}" class="flex flex-col sm:flex-row items-start sm:items-center gap-3 w-full md:w-auto">
            <label for="periode_id" class="text-sm font-semibold text-gray-700 whitespace-nowrap">Pilih Periode:</label>
            <select id="periode_id" name="periode_id" onchange="this.form.submit()" class="w-full sm:w-auto rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary text-sm py-2 px-3 min-h-[44px]">
                @foreach ($periodes as $p)
                    <option value="{{ $p->id }}" {{ $p->id == $periodeId ? 'selected' : '' }}>
                        {{ $p->nama_periode }} ({{ $p->status }})
                    </option>
                @endforeach
            </select>
        </form>

        <div class="flex flex-wrap items-center gap-3 w-full md:w-auto justify-between md:justify-end">
            <!-- Status Periode Badge -->
            @if ($chosenPeriode)
                <div class="flex items-center space-x-2">
                    <span class="text-sm text-gray-500">Status Periode:</span>
                    @if ($chosenPeriode->status === 'Draft')
                        <span class="px-3 py-1 bg-amber-50 text-amber-700 border border-amber-200 text-xs font-bold rounded-full">Draft</span>
                    @elseif($chosenPeriode->status === 'Selesai')
                        <span class="px-3 py-1 bg-green-50 text-green-700 border border-green-200 text-xs font-bold rounded-full">Selesai (Read-Only)</span>
                    @else
                        <span class="px-3 py-1 bg-gray-50 text-gray-700 border border-gray-200 text-xs font-bold rounded-full">{{ $chosenPeriode->status }}</span>
                    @endif
                </div>
            @endif

            <!-- Lock Status Badge / Add Button -->
            @if ($canManage)
                <a href="{{ route('manajer.kriteria.create', ['periode_id' => $periodeId]) }}" class="inline-flex items-center px-4 py-2 bg-primary border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-dark focus:outline-none transition min-h-[44px] shadow-sm">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Tambah Kriteria Baru
                </a>
            @else
                <span class="inline-flex items-center px-3 py-1.5 bg-gray-100 text-gray-700 border border-gray-300 text-xs font-bold rounded-md shadow-sm">
                    <svg class="w-4 h-4 mr-1.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    Kriteria Terkunci
                </span>
            @endif
        </div>
    </div>

    <!-- Restriction Reason Alert when Locked -->
    @if (!$canManage && $restrictionReason)
        <div class="mb-6 p-4 rounded-xl flex items-start border shadow-sm bg-gray-50 border-gray-200 text-gray-700">
            <svg class="w-5 h-5 text-gray-500 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
            <div class="text-sm">
                <span class="font-bold text-gray-800">Perhatian:</span> {{ $restrictionReason }}
            </div>
        </div>
    @endif

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
                    <input type="hidden" name="periode_id" value="{{ $periodeId }}">
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
                        <h4 class="font-bold text-base-dark text-base">{{ $kriteria->nama_kriteria }}</h4>
                        <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-md {{ $kriteria->atribut == 'benefit' ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-red-50 text-red-700 border border-red-200' }}">
                            {{ ucfirst($kriteria->atribut) }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between text-sm py-1">
                        <span class="text-gray-500">Bobot</span>
                        <span class="font-bold text-base-dark">{{ rtrim(rtrim(number_format((float)$kriteria->bobot, 4, '.', ''), '0'), '.') }}%</span>
                    </div>
                    <div class="mt-2 pt-3 border-t border-gray-100 flex items-center justify-end gap-2">
                        @if ($canManage)
                            <a href="{{ route('manajer.kriteria.edit', $kriteria) }}" title="Edit" class="inline-flex items-center justify-center p-2.5 bg-amber-50 border border-amber-200 rounded-lg text-amber-600 hover:bg-amber-100 min-h-[44px] min-w-[44px] transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                            </a>
                            <form method="POST" action="{{ route('manajer.kriteria.destroy', $kriteria) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kriteria ini?')" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" title="Hapus" class="inline-flex items-center justify-center p-2.5 bg-red-50 border border-red-200 rounded-lg text-red-600 hover:bg-red-100 min-h-[44px] min-w-[44px] transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                        @else
                            <span class="w-full text-center text-xs text-gray-400 py-2 border border-dashed border-gray-200 rounded-md bg-gray-50 flex items-center justify-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                Terkunci (Read-Only)
                            </span>
                        @endif
                    </div>
                </div>
                @empty
                <div class="text-center p-8 bg-gray-50 rounded-xl border border-gray-200 text-gray-500">
                    <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    <p>Tidak ada data kriteria untuk periode ini.</p>
                </div>
                @endforelse
            </div>

            <!-- Desktop Table View -->
            <div class="hidden sm:block overflow-x-auto w-full border border-gray-100 rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-base-medium uppercase tracking-wider">Nama Kriteria</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-base-medium uppercase tracking-wider">Atribut</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-base-medium uppercase tracking-wider">Bobot</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-semibold text-base-medium uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse ($kriterias as $kriteria)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $kriteria->nama_kriteria }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-md {{ $kriteria->atribut == 'benefit' ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-red-50 text-red-700 border border-red-200' }}">
                                    {{ ucfirst($kriteria->atribut) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-base-dark">{{ rtrim(rtrim(number_format((float)$kriteria->bobot, 4, '.', ''), '0'), '.') }}%</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                @if ($canManage)
                                    <div class="flex justify-end items-center gap-2">
                                        <a href="{{ route('manajer.kriteria.edit', $kriteria) }}" title="Edit" class="p-2 bg-amber-50 border border-amber-200 text-amber-600 hover:bg-amber-100 rounded-lg transition-colors inline-flex items-center justify-center">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                        </a>
                                        <form method="POST" action="{{ route('manajer.kriteria.destroy', $kriteria) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kriteria ini?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Hapus" class="p-2 bg-red-50 border border-red-200 text-red-600 hover:bg-red-100 rounded-lg transition-colors inline-flex items-center justify-center">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400 italic inline-flex items-center">
                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                        Terkunci
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 whitespace-nowrap text-sm text-center text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                <p>Tidak ada data kriteria untuk periode ini.</p>
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
