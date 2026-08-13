<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h3 class="text-lg font-bold text-base-dark">Kelola Periode</h3>
                <p class="text-sm text-base-medium mt-1">Konfigurasi periode survei lokasi. Status **Draft** untuk observasi baru, otomatis **Selesai** setelah rekomendasi dihitung, dan **Diarsipkan** untuk riwayat histori.</p>
            </div>

            <a href="{{ route('manajer.periode.create') }}" class="w-full sm:w-auto justify-center inline-flex items-center px-4 py-2 min-h-[44px] bg-primary border border-transparent rounded-md font-medium text-sm text-white hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition-colors shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah Periode
            </a>
        </div>
    </x-slot>

    <!-- Toast Notifications -->
    <x-alert />


    <div class="bg-white shadow-sm border border-gray-100 sm:rounded-xl mb-8">
        <div class="p-4 sm:p-6">
            
            <!-- Mobile Card View -->
            <div class="block sm:hidden space-y-4">
                @forelse ($periodes as $periode)
                <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm flex flex-col gap-3">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                        <h4 class="font-bold text-base-dark text-base">{{ $periode->nama_periode }}</h4>
                        @if($periode->status === 'Selesai')
                            <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-md bg-green-50 text-green-700 border border-green-200">Selesai</span>
                        @elseif($periode->status === 'Diarsipkan')
                            <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-md bg-gray-100 text-gray-700 border border-gray-200">Diarsipkan</span>
                        @else
                            <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-md bg-amber-50 text-amber-700 border border-amber-200">Draft</span>
                        @endif
                    </div>
                    <div class="flex items-center justify-between text-sm py-1">
                        <span class="text-gray-500">Total Observasi</span>
                        <span class="font-bold text-base-dark">{{ $periode->observasiLokasis()->count() }} lokasi</span>
                    </div>
                    <div class="flex items-center justify-between text-sm py-1">
                        <span class="text-gray-500">Tanggal Dibuat</span>
                        <span class="font-medium text-gray-800">{{ $periode->created_at->format('d M Y') }}</span>
                    </div>
                    <div class="flex items-center justify-end gap-2 mt-2 pt-3 border-t border-gray-100">
                        <a href="{{ route('manajer.periode.edit', $periode) }}" title="Edit" class="inline-flex items-center justify-center p-2.5 bg-amber-50 border border-amber-200 rounded-lg text-amber-600 hover:bg-amber-100 min-h-[44px] min-w-[44px] transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                        </a>
                        @if($periode->observasiLokasis()->exists())
                            <button type="button" disabled title="Periode tidak dapat dihapus karena sudah memiliki data observasi lokasi." class="inline-flex items-center justify-center p-2.5 bg-gray-100 border border-gray-200 rounded-lg text-gray-400 cursor-not-allowed min-h-[44px] min-w-[44px]">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        @else
                            <form action="{{ route('manajer.periode.destroy', $periode) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus periode {{ $periode->nama_periode }}?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" title="Hapus" class="inline-flex items-center justify-center p-2.5 bg-red-50 border border-red-200 rounded-lg text-red-600 hover:bg-red-100 min-h-[44px] min-w-[44px] transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
                @empty
                <div class="text-center p-8 bg-gray-50 rounded-xl border border-gray-200 text-gray-500">
                    <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <p>Tidak ada data periode.</p>
                </div>
                @endforelse
            </div>

            <!-- Desktop Table View -->
            <div class="hidden sm:block overflow-x-auto w-full border border-gray-100 rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-base-medium uppercase tracking-wider">Nama Periode</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-base-medium uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-base-medium uppercase tracking-wider">Jumlah Observasi</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-base-medium uppercase tracking-wider">Tanggal Dibuat</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-semibold text-base-medium uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse ($periodes as $periode)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-base-dark">
                                {{ $periode->nama_periode }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if($periode->status === 'Selesai')
                                    <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-md bg-green-50 text-green-700 border border-green-200">
                                        Selesai
                                    </span>
                                @elseif($periode->status === 'Diarsipkan')
                                    <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-md bg-gray-100 text-gray-700 border border-gray-200">
                                        Diarsipkan
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-md bg-amber-50 text-amber-700 border border-amber-200">
                                        Draft
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-medium">
                                {{ $periode->observasiLokasis()->count() }} lokasi
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $periode->created_at->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end items-center gap-2">
                                    <a href="{{ route('manajer.periode.edit', $periode) }}" title="Edit" class="p-2 bg-amber-50 border border-amber-200 text-amber-600 hover:bg-amber-100 rounded-lg transition-colors inline-flex items-center justify-center">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                    </a>

                                    @if($periode->observasiLokasis()->exists())
                                        <button type="button" disabled title="Periode tidak dapat dihapus karena sudah memiliki data observasi lokasi." class="p-2 bg-gray-100 border border-gray-200 text-gray-400 rounded-lg cursor-not-allowed inline-flex items-center justify-center">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    @else
                                        <form action="{{ route('manajer.periode.destroy', $periode) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus periode {{ $periode->nama_periode }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Hapus" class="p-2 bg-red-50 border border-red-200 text-red-600 hover:bg-red-100 rounded-lg transition-colors inline-flex items-center justify-center">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 whitespace-nowrap text-sm text-center text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                <p>Tidak ada data periode. Silakan tambah baru.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $periodes->links() }}
            </div>

        </div>
    </div>
</x-app-layout>
