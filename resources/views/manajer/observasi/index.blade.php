<x-app-layout>
    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-base-dark">Observasi Lokasi</h2>
            <p class="text-sm text-base-medium mt-1">Daftar lokasi observasi pada periode aktif yang siap untuk dinilai dan diberikan rekomendasi.</p>
        </div>
        
        <div class="flex flex-wrap items-center gap-3 w-full sm:w-auto">
            @if($batchId && !$allCalculated)
            <!-- Form Proses Rekomendasi -->
            <form action="{{ route('manajer.observasi.calculate') }}" method="POST" class="w-full sm:w-auto">
                @csrf
                <input type="hidden" name="batch_id" value="{{ $batchId }}">
                <button type="submit" 
                    class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2 min-h-[44px] bg-emerald-600 border border-transparent rounded-md font-bold text-xs text-white uppercase tracking-widest hover:bg-emerald-700 focus:bg-emerald-700 active:bg-emerald-800 transition ease-in-out duration-150 shadow-sm disabled:opacity-50 disabled:cursor-not-allowed"
                    {{ !$isComplete ? 'disabled' : '' }}
                    title="{{ !$isComplete ? 'Data kriteria atau observasi pada periode ini belum lengkap!' : 'Proses dan hitung rekomendasi lokasi pada periode ini' }}">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                    Proses Rekomendasi
                </button>
            </form>

            <!-- Button Tambah Lokasi -->
            <a href="{{ route('manajer.observasi.create', ['batch_id' => $batchId]) }}" class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2 min-h-[44px] bg-primary border border-transparent rounded-md font-medium text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-800 transition ease-in-out duration-150 shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah Lokasi
            </a>
            @endif
        </div>
    </div>

    <x-alert />


    @if(!$isComplete && $batchId && count($observasis) > 0)
    <div class="mb-6 bg-amber-50 border-l-4 border-amber-400 p-4 rounded-r-lg flex items-start text-amber-800 shadow-sm">
        <svg class="w-5 h-5 mr-3 text-amber-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
        <div class="text-xs">
            <strong class="font-bold text-sm block">Perhatian Data Belum Lengkap:</strong>
            Beberapa lokasi atau kriteria pada periode ini belum memiliki data penilaian lengkap. Pastikan seluruh lokasi telah diisi secara lengkap agar tombol <b>Proses Rekomendasi</b> dapat diaktifkan.
        </div>
    </div>
    @endif



    <div class="bg-white shadow-sm border border-gray-100 sm:rounded-xl mb-8">
        <div class="p-4 sm:p-6">
            
            <!-- Search & Filter -->
            <div class="mb-6 flex flex-col sm:flex-row justify-between items-stretch sm:items-center gap-3">
                <div class="flex items-center space-x-2">
                    <span class="text-xs uppercase font-bold text-gray-500">Periode Belum Dihitung:</span>
                </div>

                <form method="GET" action="{{ route('manajer.observasi.index') }}" class="flex flex-col sm:flex-row w-full sm:w-auto gap-3">
                    <select name="batch_id" onchange="this.form.submit()" class="block w-full sm:w-56 rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm py-2 px-3 h-[44px] bg-white font-semibold">
                        @forelse($batches as $batch)
                            <option value="{{ $batch->id }}" {{ $batchId == $batch->id ? 'selected' : '' }}>
                                {{ $batch->nama_batch }} (Draft)
                            </option>
                        @empty
                            <option value="">-- Tidak ada periode draft --</option>
                        @endforelse
                    </select>
                    
                    <div class="flex w-full sm:w-auto">
                        <div class="relative w-full sm:w-64">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                            <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama lokasi..." class="w-full pl-10 pr-3 py-2 min-h-[44px] border border-gray-300 rounded-l-md focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary sm:text-sm transition-colors">
                        </div>
                        <button type="submit" class="px-4 py-2 min-h-[44px] bg-gray-50 border border-l-0 border-gray-300 text-base-dark rounded-r-md hover:bg-gray-100 transition-colors text-sm font-medium">
                            Cari
                        </button>
                    </div>
                </form>
            </div>

            <!-- Mobile Card View -->
            <div class="block sm:hidden space-y-4">
                @forelse ($observasis as $observasi)
                <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm flex flex-col gap-3">
                    <div class="border-b border-gray-100 pb-3">
                        <h4 class="font-bold text-base-dark text-lg">{{ head(explode(',', $observasi->alamat_lengkap)) }}</h4>
                        <p class="text-sm text-gray-500 mt-1">Pemilik: {{ $observasi->nama_pemilik }}</p>
                        <p class="text-xs text-gray-400 mt-1">{{ \Illuminate\Support\Str::limit($observasi->alamat_lengkap, 70) }}</p>
                    </div>
                    
                    <div class="flex items-center justify-between text-sm py-1 border-b border-gray-50 pb-2">
                        <span class="text-gray-500">Status Perhitungan</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">
                            Belum Dihitung
                        </span>
                    </div>
                    
                    <div class="flex items-center justify-between text-sm py-1">
                        <span class="text-gray-500">Tanggal Observasi</span>
                        <span class="font-medium text-gray-800">{{ $observasi->tanggal_observasi->format('d M Y') }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm py-1">
                        <span class="text-gray-500">Observer</span>
                        <span class="font-medium text-gray-800">{{ $observasi->user->name ?? '-' }}</span>
                    </div>
                    
                    <div class="flex items-center justify-end gap-2 mt-2 pt-3 border-t border-gray-100">
                        <a href="{{ route('manajer.observasi.show', $observasi) }}" class="inline-flex items-center px-3 py-2 bg-blue-50 border border-blue-200 rounded-md text-xs font-semibold text-blue-600 hover:bg-blue-100 w-1/3 justify-center min-h-[44px]">
                            Detail
                        </a>
                        <a href="{{ route('manajer.observasi.edit', $observasi) }}" class="inline-flex items-center px-3 py-2 bg-amber-50 border border-amber-200 rounded-md text-xs font-semibold text-amber-600 hover:bg-amber-100 w-1/3 justify-center min-h-[44px]">
                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                            Edit
                        </a>
                        
                        <div x-data="{ open: false }" class="w-1/3">
                            <button @click="open = true" class="w-full inline-flex items-center px-3 py-2 bg-red-50 border border-red-200 rounded-md text-xs font-semibold text-red-600 hover:bg-red-100 justify-center min-h-[44px]">
                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                Hapus
                            </button>
                            
                            <div x-show="open" class="fixed inset-0 z-50 flex items-center justify-center p-4 overflow-auto bg-gray-900/50 backdrop-blur-sm" x-cloak>
                                <div @click.away="open = false" class="bg-white rounded-xl shadow-xl max-w-sm w-full p-6 text-left border border-gray-100">
                                    <div class="flex items-center justify-center w-12 h-12 rounded-full bg-red-100 mb-4 text-red-600">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                    </div>
                                    <h3 class="text-lg font-bold text-base-dark mb-2 whitespace-normal text-left">Konfirmasi Hapus</h3>
                                    <p class="text-sm text-base-medium mb-6 whitespace-normal text-left">Hapus data observasi untuk <strong>{{ $observasi->nama_pemilik }}</strong>? Semua data foto juga akan ikut terhapus.</p>
                                    <div class="flex justify-end space-x-3">
                                        <button @click="open = false" class="px-4 py-2 min-h-[44px] bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors text-sm font-medium">Batal</button>
                                        <form action="{{ route('manajer.observasi.destroy', $observasi) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-4 py-2 min-h-[44px] bg-red-600 border border-transparent text-white rounded-lg hover:bg-red-700 transition-colors text-sm font-medium shadow-sm">Hapus</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center p-8 bg-gray-50 rounded-xl border border-gray-200 text-gray-500">
                    <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    <p>Belum ada data observasi yang belum dihitung.</p>
                </div>
                @endforelse
            </div>

            <!-- Desktop Table View -->
            <div class="hidden sm:block overflow-x-auto w-full border border-gray-100 rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-base-medium uppercase tracking-wider">Lokasi / Alamat</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-base-medium uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-base-medium uppercase tracking-wider">Tanggal & Observer</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-semibold text-base-medium uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse ($observasis as $observasi)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="text-sm font-semibold text-base-dark">{{ head(explode(',', $observasi->alamat_lengkap)) }}</div>
                                <div class="text-xs text-gray-500 mt-1">Pemilik: {{ $observasi->nama_pemilik }}</div>
                                <div class="text-xs text-gray-400 mt-1">{{ \Illuminate\Support\Str::limit($observasi->alamat_lengkap, 70) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">
                                    Belum Dihitung
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-700">{{ $observasi->tanggal_observasi->format('d M Y') }}</div>
                                <div class="text-xs text-gray-500">{{ $observasi->user->name ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex flex-row justify-end items-center gap-4">
                                    <a href="{{ route('manajer.observasi.show', $observasi) }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 font-semibold">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        Detail
                                    </a>
                                    
                                    <a href="{{ route('manajer.observasi.edit', $observasi) }}" class="inline-flex items-center text-amber-600 hover:text-amber-800 font-semibold">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                        Edit
                                    </a>

                                    <div x-data="{ open: false }" class="inline-block text-left">
                                        <button @click="open = true" class="inline-flex items-center text-red-500 hover:text-red-700 font-semibold">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            Hapus
                                        </button>
                                        
                                        <div x-show="open" class="fixed inset-0 z-50 flex items-center justify-center p-4 overflow-auto bg-gray-900/50 backdrop-blur-sm" x-cloak>
                                            <div @click.away="open = false" class="bg-white rounded-xl shadow-xl max-w-sm w-full p-6 text-left border border-gray-100 whitespace-normal">
                                                <div class="flex items-center justify-center w-12 h-12 rounded-full bg-red-100 mb-4 text-red-600">
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                                </div>
                                                <h3 class="text-lg font-bold text-base-dark mb-2">Konfirmasi Hapus</h3>
                                                <p class="text-sm text-base-medium mb-6">Hapus data observasi untuk <strong>{{ $observasi->nama_pemilik }}</strong>? Semua data foto juga akan ikut terhapus.</p>
                                                
                                                <div class="flex justify-end space-x-3">
                                                    <button @click="open = false" class="px-4 py-2 min-h-[44px] bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors text-sm font-medium">Batal</button>
                                                    <form action="{{ route('manajer.observasi.destroy', $observasi) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="px-4 py-2 min-h-[44px] bg-red-600 border border-transparent text-white rounded-lg hover:bg-red-700 transition-colors text-sm font-medium shadow-sm">Hapus</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 whitespace-nowrap text-sm text-center text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                <p class="font-semibold text-gray-700">Belum ada data observasi pada periode draft ini.</p>
                                <p class="text-xs text-gray-400 mt-1">Silakan klik "Tambah Lokasi" untuk memasukkan data observasi baru.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4">
                {{ $observasis->links() }}
            </div>

        </div>
    </div>
</x-app-layout>
