<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-base-dark leading-tight">
            {{ __('Edit Kriteria') }} - <span class="text-primary">{{ $kriteria->nama_kriteria }}</span>
        </h2>
    </x-slot>

    <div class="max-w-2xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm border border-gray-100 sm:rounded-xl">
            <div class="p-6 sm:p-8">

                <form method="POST" action="{{ route('manajer.kriteria.update', $kriteria) }}">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="periode_id" value="{{ $kriteria->periode_id }}">

                    <div class="space-y-6">
                        <!-- Urutan Kriteria (Hidden) -->
                        <input type="hidden" name="urutan" value="{{ $kriteria->urutan }}">

                        <!-- Nama Kriteria -->
                        <div>
                            <label for="nama_kriteria" class="block text-sm font-medium text-base-dark mb-1">Nama Kriteria</label>
                            <input id="nama_kriteria" type="text" name="nama_kriteria" value="{{ old('nama_kriteria', $kriteria->nama_kriteria) }}" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm transition-colors py-2 px-3 min-h-[44px]">
                            <x-input-error :messages="$errors->get('nama_kriteria')" class="mt-2" />
                        </div>

                        <!-- Atribut -->
                        <div>
                            <label for="atribut" class="block text-sm font-medium text-base-dark mb-1">Tipe Kriteria Penilaian</label>
                            <select id="atribut" name="atribut" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm transition-colors py-2 px-3 min-h-[44px] bg-white">
                                <option value="" disabled>Pilih Tipe Kriteria</option>
                                <option value="benefit" {{ old('atribut', $kriteria->atribut) == 'benefit' ? 'selected' : '' }}>Penilaian Positif (Semakin besar nilai semakin baik)</option>
                                <option value="cost" {{ old('atribut', $kriteria->atribut) == 'cost' ? 'selected' : '' }}>Faktor Biaya/Jarak (Semakin kecil nilai semakin baik)</option>
                            </select>
                            <x-input-error :messages="$errors->get('atribut')" class="mt-2" />
                        </div>

                        <!-- Jenis Input -->
                        <div>
                            <label for="jenis_input" class="block text-sm font-medium text-base-dark mb-1">Jenis Input Nilai</label>
                            <select id="jenis_input" name="jenis_input" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm transition-colors py-2 px-3 min-h-[44px] bg-white">
                                <option value="" disabled>Pilih Jenis Input</option>
                                <option value="numeric" {{ old('jenis_input', $kriteria->jenis_input) == 'numeric' ? 'selected' : '' }}>Numeric (Angka Asli - misal: Rp. 10.000.000)</option>
                                <option value="scoring" {{ old('jenis_input', $kriteria->jenis_input) == 'scoring' ? 'selected' : '' }}>Scoring (Sistem Poin - misal: 1 s/d 5)</option>
                            </select>
                            <x-input-error :messages="$errors->get('jenis_input')" class="mt-2" />
                        </div>

                        <!-- Bobot -->
                        <div>
                            <label for="bobot" class="block text-sm font-medium text-base-dark mb-1">Bobot (%)</label>
                            <input id="bobot" type="number" step="0.01" name="bobot" value="{{ old('bobot', rtrim(rtrim(number_format((float)$kriteria->bobot, 4, '.', ''), '0'), '.')) }}" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm transition-colors py-2 px-3 min-h-[44px]">
                            <x-input-error :messages="$errors->get('bobot')" class="mt-2" />
                        </div>
                    </div>

                    <div class="flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-end mt-8 pt-5 border-t border-gray-100 gap-3 sm:gap-4">
                        <a href="{{ route('manajer.kriteria.index') }}" class="w-full sm:w-auto text-center inline-flex items-center justify-center text-sm font-medium text-gray-500 hover:text-base-dark transition-colors py-2 min-h-[44px]">
                            Batal
                        </a>
                        <button type="submit" class="w-full sm:w-auto justify-center inline-flex items-center px-4 py-2 min-h-[44px] bg-primary border border-transparent rounded-md font-medium text-sm text-white hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition-colors shadow-sm">
                            Update Kriteria
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
