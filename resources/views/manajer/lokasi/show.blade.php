<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            <a href="{{ route('manajer.lokasi.index') }}" class="text-gray-400 hover:text-primary transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h2 class="font-semibold text-xl text-base-dark leading-tight">
                {{ __('Detail Lokasi') }} - <span class="text-primary">{{ $lokasi->nama_lokasi }}</span>
            </h2>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm border border-gray-100 sm:rounded-xl">
            
            <div class="p-4 sm:p-6 md:p-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 md:gap-8">
                    
                    <!-- Detail Informasi -->
                    <div class="md:col-span-2 space-y-6">
                        <div>
                            <h3 class="text-lg font-bold text-base-dark border-b border-gray-100 pb-2 mb-4">Informasi Dasar</h3>
                            
                            <div class="space-y-4">
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-1 sm:gap-4 border-b sm:border-0 border-gray-50 pb-2 sm:pb-0">
                                    <div class="col-span-1 text-sm font-medium text-gray-500">Nama Lokasi</div>
                                    <div class="col-span-1 sm:col-span-2 text-sm font-semibold text-base-dark">{{ $lokasi->nama_lokasi }}</div>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-1 sm:gap-4 border-b sm:border-0 border-gray-50 pb-2 sm:pb-0">
                                    <div class="col-span-1 text-sm font-medium text-gray-500">Alamat Lengkap</div>
                                    <div class="col-span-1 sm:col-span-2 text-sm text-base-dark">{{ $lokasi->alamat }}</div>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-1 sm:gap-4 border-b sm:border-0 border-gray-50 pb-2 sm:pb-0">
                                    <div class="col-span-1 text-sm font-medium text-gray-500">Kecamatan</div>
                                    <div class="col-span-1 sm:col-span-2 text-sm text-base-dark">{{ $lokasi->kecamatan }}</div>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-1 sm:gap-4 border-b sm:border-0 border-gray-50 pb-2 sm:pb-0">
                                    <div class="col-span-1 text-sm font-medium text-gray-500">Kabupaten/Kota</div>
                                    <div class="col-span-1 sm:col-span-2 text-sm text-base-dark">{{ $lokasi->kabupaten }}</div>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-1 sm:gap-4 border-b sm:border-0 border-gray-50 pb-2 sm:pb-0">
                                    <div class="col-span-1 text-sm font-medium text-gray-500">Provinsi</div>
                                    <div class="col-span-1 sm:col-span-2 text-sm text-base-dark">{{ $lokasi->provinsi }}</div>
                                </div>
                            </div>
                        </div>


                    </div>

                    <!-- Side Panel / Metadata -->
                    <div class="md:col-span-1">
                        <div class="bg-gray-50 rounded-xl p-5 border border-gray-100">
                            <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-4">Metadata</h3>
                            
                            <div class="space-y-4">
                                <div>
                                    <p class="text-xs text-gray-500 mb-1">Didaftarkan Oleh</p>
                                    <div class="flex items-center text-sm font-semibold text-base-dark">
                                        <div class="w-6 h-6 rounded-full bg-soft-green text-primary flex items-center justify-center text-xs mr-2">
                                            {{ substr($lokasi->creator->name ?? 'S', 0, 1) }}
                                        </div>
                                        {{ $lokasi->creator->name ?? 'Sistem' }}
                                    </div>
                                </div>
                                
                                <div>
                                    <p class="text-xs text-gray-500 mb-1">Tanggal Pendaftaran</p>
                                    <p class="text-sm font-medium text-base-dark">{{ $lokasi->created_at->format('d M Y, H:i') }}</p>
                                </div>

                                <div>
                                    <p class="text-xs text-gray-500 mb-1">Terakhir Diupdate</p>
                                    <p class="text-sm font-medium text-base-dark">{{ $lokasi->updated_at->format('d M Y, H:i') }}</p>
                                </div>
                            </div>
                            
                            <div class="mt-6 pt-6 border-t border-gray-200">
                                <a href="{{ route('manajer.lokasi.edit', $lokasi) }}" class="w-full inline-flex justify-center items-center px-4 py-2 min-h-[44px] bg-white border border-gray-300 rounded-md font-medium text-sm text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition-colors shadow-sm">
                                    <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                    Edit Lokasi
                                </a>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>


</x-app-layout>
