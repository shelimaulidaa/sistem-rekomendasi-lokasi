<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-base-dark leading-tight">
            {{ __('Edit Periode') }} - <span class="text-primary">{{ $periode->nama_periode }}</span>
        </h2>
    </x-slot>

    <div class="max-w-2xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm border border-gray-100 sm:rounded-xl">
            <div class="p-6 sm:p-8">
                
                <form method="POST" action="{{ route('manajer.periode.update', $periode) }}">
                    @csrf
                    @method('PUT')

                    <div class="space-y-6">
                        <!-- Nama Periode -->
                        <div>
                            <label for="nama_periode" class="block text-sm font-medium text-base-dark mb-1">Nama Periode</label>
                            <input id="nama_periode" type="text" name="nama_periode" value="{{ old('nama_periode', $periode->nama_periode) }}" required autofocus class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm transition-colors py-2 px-3 min-h-[44px]">
                            <p class="text-xs text-gray-400 mt-1">Nama periode harus unik dan menggambarkan wilayah atau target survei.</p>
                            <x-input-error :messages="$errors->get('nama_periode')" class="mt-2" />
                        </div>

                        <!-- Status Periode -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-base-dark mb-1">Status Periode</label>
                            <select id="status" name="status" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm transition-colors py-2 px-3 min-h-[44px]">
                                <option value="Draft" {{ old('status', $periode->status) === 'Draft' ? 'selected' : '' }}>Draft</option>
                                <option value="Selesai" {{ old('status', $periode->status) === 'Selesai' ? 'selected' : '' }}>Selesai</option>
                                <option value="Diarsipkan" {{ old('status', $periode->status) === 'Diarsipkan' ? 'selected' : '' }}>Diarsipkan</option>
                            </select>
                            <p class="text-xs text-gray-400 mt-1">Ubah status ke "Diarsipkan" jika periode ini sudah tidak digunakan lagi untuk proses observasi/perhitungan baru.</p>
                            <x-input-error :messages="$errors->get('status')" class="mt-2" />
                        </div>
                    </div>

                    <div class="flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-end mt-8 pt-5 border-t border-gray-100 gap-3 sm:gap-4">
                        <a href="{{ route('manajer.periode.index') }}" class="w-full sm:w-auto text-center inline-flex items-center justify-center text-sm font-medium text-gray-500 hover:text-base-dark transition-colors py-2 min-h-[44px]">
                            Batal
                        </a>
                        <button type="submit" class="w-full sm:w-auto justify-center inline-flex items-center px-4 py-2 min-h-[44px] bg-primary border border-transparent rounded-md font-medium text-sm text-white hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition-colors shadow-sm">
                            Update Periode
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
