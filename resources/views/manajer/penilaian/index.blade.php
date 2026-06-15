<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Data Penilaian') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h3 class="text-lg font-bold text-base-dark">Matriks Keputusan (X)</h3>
                    <p class="text-sm text-gray-500">Data ini diisi otomatis dari hasil Observasi Lokasi.</p>
                </div>
                
                <form action="{{ route('manajer.perhitungan.calculate') }}" method="POST" class="w-full sm:w-auto">
                    @csrf
                    <button type="submit" 
                            class="w-full sm:w-auto justify-center inline-flex items-center px-4 py-2 min-h-[44px] bg-primary border border-transparent rounded-md font-medium text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-800 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50 disabled:cursor-not-allowed"
                            {{ !$isComplete ? 'disabled' : '' }}
                            title="{{ !$isComplete ? 'Data kriteria atau observasi belum lengkap!' : 'Hitung menggunakan metode TOPSIS' }}">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                        Hitung TOPSIS
                    </button>
                </form>
            </div>

            @if(!$isComplete && count($matrix) > 0)
                <div class="mb-4 bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-md">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">
                                <strong>Perhatian:</strong> Data matriks belum lengkap! Pastikan semua kriteria sudah dinilai untuk setiap lokasi yang ada sebelum melakukan perhitungan.
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            @if(count($matrix) === 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-center text-gray-500">
                    Belum ada data observasi yang dinilai. <a href="{{ route('manajer.observasi.index') }}" class="text-primary hover:underline">Lakukan observasi lokasi terlebih dahulu.</a>
                </div>
            @else
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="overflow-x-auto w-full">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Alternatif (Lokasi)
                                    </th>
                                    @foreach($kriterias as $kriteria)
                                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider" title="{{ $kriteria->nama_kriteria }} ({{ ucwords($kriteria->atribut) }})">
                                            {{ $kriteria->kode_kriteria }}
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($matrix as $row)
                                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $row['nama_lokasi'] }}</div>
                                        </td>
                                        @foreach($kriterias as $kriteria)
                                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                                                @if(isset($row['details'][$kriteria->kriteria_id]))
                                                    {{ rtrim(rtrim(number_format($row['details'][$kriteria->kriteria_id], 2, '.', ''), '0'), '.') }}
                                                @else
                                                    <span class="text-red-500 font-bold">-</span>
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
