<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-base-dark leading-tight">
            {{ __('Tambah Periode Baru') }}
        </h2>
    </x-slot>

    <div class="max-w-2xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm border border-gray-100 sm:rounded-xl">
            <div class="p-6 sm:p-8">
                
                <form method="POST" action="{{ route('manajer.periode.store') }}">
                    @csrf

                    <div class="space-y-6">
                        <!-- Nama Periode -->
                        <div>
                            <label for="nama_periode" class="block text-sm font-medium text-base-dark mb-1">Nama Periode</label>
                            <input id="nama_periode" type="text" name="nama_periode" value="{{ old('nama_periode') }}" required autofocus placeholder="Misal: Cabang Antapani 2026" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm transition-colors py-2 px-3 min-h-[44px]">
                            <p class="text-xs text-gray-400 mt-1">Nama periode harus unik dan merepresentasikan gelombang atau cakupan survei lokasi.</p>
                            <x-input-error :messages="$errors->get('nama_periode')" class="mt-2" />
                        </div>
                    </div>

                    <div class="flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-end mt-8 pt-5 border-t border-gray-100 gap-3 sm:gap-4">
                        <a href="{{ route('manajer.periode.index') }}" class="w-full sm:w-auto text-center inline-flex items-center justify-center text-sm font-medium text-gray-500 hover:text-base-dark transition-colors py-2 min-h-[44px]">
                            Batal
                        </a>
                        <button type="submit" class="w-full sm:w-auto justify-center inline-flex items-center px-4 py-2 min-h-[44px] bg-primary border border-transparent rounded-md font-medium text-sm text-white hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition-colors shadow-sm">
                            Simpan Periode
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
