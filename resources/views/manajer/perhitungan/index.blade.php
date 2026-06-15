<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Hasil Perhitungan TOPSIS') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ activeTab: 'hasil' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-4 bg-green-50 border-l-4 border-green-400 p-4 rounded-md">
                    <p class="text-sm text-green-700 font-medium">{{ session('success') }}</p>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 bg-red-50 border-l-4 border-red-400 p-4 rounded-md">
                    <p class="text-sm text-red-700 font-medium">{{ session('error') }}</p>
                </div>
            @endif

            @if($hasil->isEmpty())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-center text-gray-500">
                    Belum ada perhitungan TOPSIS yang dilakukan. <a href="{{ route('manajer.penilaian.index') }}" class="text-primary hover:underline">Pergi ke menu Penilaian.</a>
                </div>
            @else
                


                <!-- Tab Navigation -->
                <div class="border-b border-gray-200 mb-6">
                    <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                        <button @click="activeTab = 'hasil'" 
                                :class="{ 'border-primary text-primary': activeTab === 'hasil', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'hasil' }"
                                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200">
                            Peringkat Akhir (Hasil)
                        </button>
                        
                        @if($steps)
                            <button @click="activeTab = 'langkah'" 
                                    :class="{ 'border-primary text-primary': activeTab === 'langkah', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'langkah' }"
                                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200">
                                Langkah Perhitungan (Math)
                            </button>
                        @endif
                    </nav>
                </div>

                <!-- Tab Content: Hasil -->
                <div x-show="activeTab === 'hasil'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 sm:gap-4 mb-4">
                                <h3 class="text-lg font-bold text-base-dark">Rekomendasi Lokasi</h3>
                                <p class="text-xs text-gray-400">Dihitung pada: {{ $hasil->first()->tanggal_hitung->format('d M Y, H:i') }}</p>
                            </div>
                            
                            <div class="overflow-x-auto w-full">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">
                                                Ranking
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                                Nama Lokasi
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">
                                                Nilai Preferensi (V)
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($hasil as $h)
                                            <tr class="hover:bg-gray-50 {{ $loop->first ? 'bg-green-50' : '' }}">
                                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                                    @if($loop->first)
                                                        <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-primary rounded-full">
                                                            #1
                                                        </span>
                                                    @else
                                                        <span class="text-sm font-bold text-gray-600">{{ $h->ranking }}</span>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm font-medium text-gray-900">{{ $h->penilaian->lokasi->nama_lokasi }}</div>
                                                    <div class="text-xs text-gray-500">{{ $h->penilaian->lokasi->alamat }}</div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                                    <div class="text-sm font-bold text-gray-900">{{ number_format($h->nilai_preferensi, 4) }}</div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab Content: Langkah Perhitungan -->
                @if($steps)
                <div x-show="activeTab === 'langkah'" style="display: none;" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100">
                    <div class="space-y-6">
                        <!-- Step 2: Normalized Matrix -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 bg-white border-b border-gray-200">
                                <h4 class="text-md font-bold text-base-dark mb-4">Tahap 1: Matriks Ternormalisasi (R)</h4>
                                <div class="overflow-x-auto w-full">
                                    <table class="min-w-full divide-y divide-gray-200 text-xs">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-4 py-2">ID</th>
                                                @foreach($steps['kriterias'] as $k)
                                                    <th class="px-4 py-2 text-center">{{ $k['kode_kriteria'] }}</th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($steps['normalizedMatrix'] as $pid => $row)
                                                <tr>
                                                    <td class="px-4 py-2 font-medium">L-{{ $pid }}</td>
                                                    @foreach($steps['kriterias'] as $k)
                                                        <td class="px-4 py-2 text-center">{{ number_format($row[$k['kriteria_id']], 4) }}</td>
                                                    @endforeach
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Step 3: Weighted Normalized Matrix -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 bg-white border-b border-gray-200">
                                <h4 class="text-md font-bold text-base-dark mb-4">Tahap 2: Matriks Ternormalisasi Terbobot (Y)</h4>
                                <div class="overflow-x-auto w-full">
                                    <table class="min-w-full divide-y divide-gray-200 text-xs">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-4 py-2">ID</th>
                                                @foreach($steps['kriterias'] as $k)
                                                    <th class="px-4 py-2 text-center">{{ $k['kode_kriteria'] }}</th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($steps['weightedMatrix'] as $pid => $row)
                                                <tr>
                                                    <td class="px-4 py-2 font-medium">L-{{ $pid }}</td>
                                                    @foreach($steps['kriterias'] as $k)
                                                        <td class="px-4 py-2 text-center">{{ number_format($row[$k['kriteria_id']], 4) }}</td>
                                                    @endforeach
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Step 4: Solusi Ideal -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 bg-white border-b border-gray-200">
                                <h4 class="text-md font-bold text-base-dark mb-4">Tahap 3: Solusi Ideal Positif (A+) dan Negatif (A-)</h4>
                                <div class="overflow-x-auto w-full">
                                    <table class="min-w-full divide-y divide-gray-200 text-xs">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-4 py-2">Solusi Ideal</th>
                                                @foreach($steps['kriterias'] as $k)
                                                    <th class="px-4 py-2 text-center">{{ $k['kode_kriteria'] }}</th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr class="bg-green-50">
                                                <td class="px-4 py-2 font-bold text-green-700">A+ (Positif)</td>
                                                @foreach($steps['kriterias'] as $k)
                                                    <td class="px-4 py-2 text-center font-bold text-green-700">{{ number_format($steps['idealPositive'][$k['kriteria_id']], 4) }}</td>
                                                @endforeach
                                            </tr>
                                            <tr class="bg-red-50">
                                                <td class="px-4 py-2 font-bold text-red-700">A- (Negatif)</td>
                                                @foreach($steps['kriterias'] as $k)
                                                    <td class="px-4 py-2 text-center font-bold text-red-700">{{ number_format($steps['idealNegative'][$k['kriteria_id']], 4) }}</td>
                                                @endforeach
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Step 5: Jarak Ideal & Preferensi -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 bg-white border-b border-gray-200">
                                <h4 class="text-md font-bold text-base-dark mb-4">Tahap 4 & 5: Jarak (D+, D-) dan Nilai Preferensi (V)</h4>
                                <div class="overflow-x-auto w-full">
                                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-4 py-2 text-left">Nama Lokasi</th>
                                                <th class="px-4 py-2 text-center">D+</th>
                                                <th class="px-4 py-2 text-center">D-</th>
                                                <th class="px-4 py-2 text-center">V (Preferensi)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($steps['results'] as $res)
                                                <tr>
                                                    <td class="px-4 py-2 font-medium">{{ $res['nama_lokasi'] }}</td>
                                                    <td class="px-4 py-2 text-center">{{ number_format($res['d_plus'], 4) }}</td>
                                                    <td class="px-4 py-2 text-center">{{ number_format($res['d_minus'], 4) }}</td>
                                                    <td class="px-4 py-2 text-center font-bold text-primary">{{ number_format($res['preference_score'], 4) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                
            @endif
        </div>
    </div>
</x-app-layout>
