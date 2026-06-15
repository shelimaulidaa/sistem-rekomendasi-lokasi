<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h2 class="font-semibold text-xl text-base-dark leading-tight">
                {{ __('Kelola Lokasi') }}
            </h2>
            <a href="{{ route('manajer.lokasi.create') }}" class="w-full sm:w-auto justify-center inline-flex items-center px-4 py-2 sm:py-2 min-h-[44px] sm:min-h-0 bg-primary border border-transparent rounded-md font-medium text-sm text-white hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition-colors shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah Lokasi
            </a>
        </div>
    </x-slot>

    <!-- Toast Notification -->
    @if (session('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" class="mb-6 bg-soft-green border border-green-200 text-primary px-4 py-3 rounded-lg flex items-center shadow-sm" role="alert">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        <span class="block sm:inline font-medium text-sm">{{ session('success') }}</span>
    </div>
    @endif

    <div class="bg-white overflow-hidden shadow-sm border border-gray-100 sm:rounded-xl">
        <div class="p-6">
            
            <!-- Search Bar -->
            <div class="mb-6 flex justify-end">
                <form method="GET" action="{{ route('manajer.lokasi.index') }}" class="flex w-full sm:w-1/2 lg:w-1/3">
                    <div class="relative w-full">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama, alamat, kecamatan..." class="w-full pl-10 pr-3 py-2 min-h-[44px] border border-gray-300 rounded-l-md focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary sm:text-sm transition-colors">
                    </div>
                    <button type="submit" class="px-4 py-2 min-h-[44px] bg-gray-50 border border-l-0 border-gray-300 text-base-dark rounded-r-md hover:bg-gray-100 transition-colors text-sm font-medium">
                        Cari
                    </button>
                </form>
            </div>

            <!-- Responsive Table -->
            <div class="overflow-x-auto w-full border border-gray-100 rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-base-medium uppercase tracking-wider">Nama Lokasi</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-base-medium uppercase tracking-wider">Kecamatan</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-base-medium uppercase tracking-wider">Kabupaten</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-base-medium uppercase tracking-wider">Pendaftar</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-semibold text-base-medium uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse ($lokasis as $lokasi)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-base-dark">{{ $lokasi->nama_lokasi }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $lokasi->kecamatan }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $lokasi->kabupaten }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $lokasi->creator->name ?? 'Sistem' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex flex-col sm:flex-row justify-end items-end sm:items-center gap-3 sm:gap-4">
                                    <a href="{{ route('manajer.lokasi.show', $lokasi) }}" class="inline-flex items-center text-blue-600 hover:text-blue-800">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        Detail
                                    </a>
                                    <a href="{{ route('manajer.lokasi.edit', $lokasi) }}" class="inline-flex items-center text-primary hover:text-primary-dark">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                        Edit
                                    </a>
                                    
                                    <!-- Delete Modal Component (Alpine) -->
                                    <div x-data="{ open: false }" class="inline-block text-left">
                                        <button @click="open = true" class="inline-flex items-center text-red-500 hover:text-red-700">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            Hapus
                                        </button>
                                        
                                        <!-- Modal -->
                                        <div x-show="open" class="fixed inset-0 z-50 flex items-center justify-center p-4 overflow-auto bg-gray-900 bg-opacity-50 backdrop-blur-sm" x-cloak style="display: none;">
                                            <div @click.away="open = false" class="bg-white rounded-xl shadow-xl max-w-sm w-full p-6 text-left border border-gray-100">
                                                <div class="flex items-center justify-center w-12 h-12 rounded-full bg-red-100 mb-4 text-red-600">
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                                </div>
                                                <h3 class="text-lg font-bold text-base-dark mb-2 whitespace-normal text-left">Konfirmasi Hapus</h3>
                                                <p class="text-sm text-base-medium mb-6 whitespace-normal text-left">Hapus Lokasi <strong>{{ $lokasi->nama_lokasi }}</strong>? Tindakan ini tidak dapat dibatalkan.</p>
                                                
                                                <div class="flex justify-end space-x-3">
                                                    <button @click="open = false" class="px-4 py-2 min-h-[44px] bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors text-sm font-medium">Batal</button>
                                                    <form action="{{ route('manajer.lokasi.destroy', $lokasi) }}" method="POST">
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
                            <td colspan="5" class="px-6 py-8 whitespace-nowrap text-sm text-center text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.243-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                <p>Tidak ada data lokasi.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4">
                {{ $lokasis->links() }}
            </div>

        </div>
    </div>
</x-app-layout>
