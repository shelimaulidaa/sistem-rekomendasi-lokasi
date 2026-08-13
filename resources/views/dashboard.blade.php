<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <!-- Welcome Section & Periode Filter Bar -->
    <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h3 class="text-2xl font-extrabold text-base-dark tracking-tight">Selamat Datang, {{ Auth::user()->name }}!</h3>
            <p class="text-base-medium text-sm mt-1">
                Anda login sebagai <span class="font-semibold text-primary">{{ ucfirst(Auth::user()->roles->first()?->name ?? 'User') }}</span>.
            </p>
        </div> 
        
        <!-- Periode Selection Dropdown -->
        <div class="bg-white p-2 rounded-xl shadow-sm border border-gray-200 flex flex-col sm:flex-row items-stretch sm:items-center space-y-2 sm:space-y-0 sm:space-x-3 w-full sm:w-auto">
            <label for="periode_select" class="text-xs font-bold text-gray-500 uppercase tracking-wider pl-2 flex items-center whitespace-nowrap">
                <svg class="w-4 h-4 mr-1.5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                Periode:
            </label>
            <form method="GET" action="{{ route('manajer.dashboard') }}" class="m-0 flex items-center w-full sm:w-auto">
                <select id="periode_select" name="periode_id" onchange="this.form.submit()" class="w-full sm:w-auto rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm font-semibold text-gray-800 py-1.5 pl-3 pr-8 min-w-full sm:min-w-[220px]">
                    @foreach($periodes as $b)
                        <option value="{{ $b->id }}" {{ $chosenPeriode && $chosenPeriode->id == $b->id ? 'selected' : '' }}>
                            {{ $b->nama_periode }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>

    <!-- Minimalist Premium Metric Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
        
        <!-- Card 1: Total Observasi -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col hover:shadow-md hover:-translate-y-1 transition-all duration-300">
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs uppercase tracking-wider font-bold text-base-medium">Observasi Lokasi</span>
                <div class="w-10 h-10 rounded-xl bg-green-50 text-primary flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
            </div>
            <h4 class="text-3xl font-extrabold text-base-dark mt-2">{{ $totalObservasi }}</h4>
            <p class="text-sm text-base-medium mt-1">Total lokasi yang disurvei untuk pembukaan cabang baru.</p>
        </div>

        <!-- Card 2: Total Kriteria -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col hover:shadow-md hover:-translate-y-1 transition-all duration-300">
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs uppercase tracking-wider font-bold text-base-medium">Kriteria Penilaian</span>
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01m-.01 4h.01"></path>
                    </svg>
                </div>
            </div>
            <h4 class="text-3xl font-extrabold text-base-dark mt-2">{{ $totalKriteria }}</h4>
            <div class="flex gap-2 mt-2">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-100">
                    {{ $totalKriteria }} Kriteria Aktif
                </span>
            </div>
            <p class="text-xs text-base-medium mt-3">Kriteria aktif yang digunakan dalam penilaian lokasi.</p>
        </div>

        <!-- Card 3: Rekomendasi Terbaik -->
        <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-2xl shadow-sm border border-emerald-100 p-6 flex flex-col hover:shadow-md hover:-translate-y-1 transition-all duration-300 sm:col-span-2 lg:col-span-1">
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs uppercase tracking-wider font-bold text-emerald-800">Rekomendasi Terbaik</span>
                <div class="w-10 h-10 rounded-xl bg-yellow-100 text-yellow-600 flex items-center justify-center shadow-sm">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                    </svg>
                </div>
            </div>
            
            @if($lokasiTerbaik)
                <h4 class="text-xl font-extrabold text-gray-900 truncate" title="{{ $lokasiTerbaik->penilaian->observasiLokasi->alamat_lengkap }}">
                    {{ $lokasiTerbaik->penilaian->observasiLokasi->alamat_lengkap }}
                </h4>
                <p class="text-xs text-emerald-700 font-semibold mt-1">Skor Rekomendasi = {{ number_format($lokasiTerbaik->nilai_preferensi, 4) }}</p>
                <p class="text-xs text-gray-500 mt-2 truncate"><span class="font-medium text-gray-700">Pemilik:</span> {{ $lokasiTerbaik->penilaian->observasiLokasi->nama_pemilik ?? 'Tidak ada nama' }}</p>
            @else
                <h4 class="text-lg font-bold text-gray-500 mt-2">Belum Ada Hasil Penilaian</h4>
                <p class="text-sm text-gray-400 mt-1">Silakan lakukan proses penilaian lokasi terlebih dahulu.</p>
            @endif
        </div>

    </div>

    <!-- Main Strategic Content -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 md:p-6 mt-8 flex flex-col justify-between">
        <div>
            <h3 class="text-lg font-bold text-gray-800">Perbandingan Skor Rekomendasi Lokasi (Top 5)</h3>
            <p class="text-sm text-gray-500 mb-4">Menampilkan 5 lokasi dengan nilai rekomendasi tertinggi pada periode yang dipilih</p>
        </div>

        <div class="relative w-full flex-1" style="min-height: 250px; height: 320px;">
            <canvas id="rankingChart"></canvas>
        </div>
    </div>

    <!-- Visualisasi Peta Hasil Penilaian Lokasi -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 md:p-6 mt-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-4 pb-4 border-b border-gray-100">
            <div>
                <h3 class="text-lg font-bold text-gray-800 flex items-center">
                    <svg class="w-5 h-5 text-primary mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                    </svg>
                    Peta Hasil Penilaian Lokasi
                </h3>
                <p class="text-xs text-gray-500 mt-1">Menampilkan seluruh lokasi observasi pada periode yang dipilih.</p>
            </div>
            
            <!-- Legend Indicator -->
            <div class="flex flex-wrap items-center gap-2 text-[11px] font-semibold">
                <span class="inline-flex items-center px-2.5 py-1 rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 mr-1.5 shadow-sm"></span> Ranking #1 (Rekomendasi utama)
                </span>
                <span class="inline-flex items-center px-2.5 py-1 rounded-md bg-amber-50 text-amber-700 border border-amber-200">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500 mr-1.5 shadow-sm"></span> Ranking #2 - #3 (Rekomendasi)
                </span>
                <span class="inline-flex items-center px-2.5 py-1 rounded-md bg-rose-50 text-rose-700 border border-rose-200">
                    <span class="w-2.5 h-2.5 rounded-full bg-rose-500 mr-1.5 shadow-sm"></span> Ranking > #3 (Alternatif)
                </span>
            </div>
        </div>
        
        <div id="dashboardMap" class="w-full rounded-xl border border-gray-200 z-10 shadow-inner h-[320px] sm:h-[420px]"></div>
    </div>

    <!-- Leaflet Assets -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Leaflet Map Initialization
            const mapContainer = document.getElementById('dashboardMap');
            if (mapContainer) {
                const mapLocationsData = {!! json_encode($mapLocations) !!};
                const resultBaseUrl = "{{ route('manajer.hasil.index') }}";

                // Leaflet Layer Groups
                const locationLayerGroup = L.layerGroup();
                const activeRphLayerGroup = L.layerGroup();
                const activeCompetitorLayerGroup = L.layerGroup();
                const activePolylineLayerGroup = L.layerGroup();

                // Marker Creators
                function createLocationPin(color, rankText) {
                    const colors = {
                        green: '#10B981',
                        yellow: '#F59E0B',
                        red: '#EF4444',
                        blue: '#6366F1'
                    };
                    const hex = colors[color] || '#6366F1';
                    const html = `
                        <div style="position: relative; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                            <div style="background-color: ${hex}; width: 28px; height: 28px; border-radius: 50% 50% 50% 0; transform: rotate(-45deg); border: 2px solid #ffffff; box-shadow: 0 3px 6px rgba(0,0,0,0.3); display: flex; align-items: center; justify-content: center;">
                                <span style="transform: rotate(45deg); color: #ffffff; font-weight: 800; font-size: 11px; font-family: sans-serif;">${rankText}</span>
                            </div>
                        </div>
                    `;
                    return L.divIcon({
                        className: 'custom-location-pin',
                        html: html,
                        iconSize: [32, 32],
                        iconAnchor: [16, 32],
                        popupAnchor: [0, -30]
                    });
                }

                function createRphPin() {
                    const html = `
                        <div style="background-color: #2563EB; width: 28px; height: 28px; border-radius: 50%; border: 2px solid white; box-shadow: 0 2px 6px rgba(0,0,0,0.3); display: flex; align-items: center; justify-content: center;">
                            <span style="color: white; font-size: 12px;">🥩</span>
                        </div>
                    `;
                    return L.divIcon({
                        className: 'custom-rph-pin',
                        html: html,
                        iconSize: [28, 28],
                        iconAnchor: [14, 14],
                        popupAnchor: [0, -14]
                    });
                }

                function createCompetitorPin() {
                    const html = `
                        <div style="background-color: #D97706; width: 28px; height: 28px; border-radius: 50%; border: 2px solid white; box-shadow: 0 2px 6px rgba(0,0,0,0.3); display: flex; align-items: center; justify-content: center;">
                            <span style="color: white; font-size: 12px;">🏪</span>
                        </div>
                    `;
                    return L.divIcon({
                        className: 'custom-competitor-pin',
                        html: html,
                        iconSize: [28, 28],
                        iconAnchor: [14, 14],
                        popupAnchor: [0, -14]
                    });
                }

                const allBounds = [];

                // Fungsi untuk menampilkan data turunan lokasi terpilih (RPH, Pesaing, Garis Penghubung)
                function selectLocation(item) {
                    // Bersihkan layer aktif sebelumnya
                    activeRphLayerGroup.clearLayers();
                    activeCompetitorLayerGroup.clearLayers();
                    activePolylineLayerGroup.clearLayers();

                    // 1. Gambar penanda & garis RPH terkait
                    if (item.rph && item.rph.lat && item.rph.lng) {
                        const rphMarker = L.marker([item.rph.lat, item.rph.lng], { icon: createRphPin() });
                        const rphPopup = `
                            <div style="font-family: ui-sans-serif, system-ui, sans-serif; padding: 2px; min-width: 180px;">
                                <div style="font-size: 10px; font-weight: bold; color: #2563EB; text-transform: uppercase; margin-bottom: 2px;">Rumah Potong Hewan (RPH)</div>
                                <h4 style="font-weight: 700; font-size: 12px; color: #111827; margin: 0 0 2px 0;">${item.rph.nama}</h4>
                                <p style="font-size: 11px; color: #4B5563; margin: 0;">Data RPH terkait untuk lokasi <b>${item.nama_pemilik}</b></p>
                            </div>
                        `;
                        rphMarker.bindPopup(rphPopup);
                        activeRphLayerGroup.addLayer(rphMarker);

                        // Garis penghubung dari Lokasi ke RPH
                        const polyline = L.polyline([[item.lat, item.lng], [item.rph.lat, item.rph.lng]], {
                            color: '#2563EB',
                            weight: 3,
                            dashArray: '6, 8',
                            opacity: 0.85
                        });
                        activePolylineLayerGroup.addLayer(polyline);
                    }

                    // 2. Gambar penanda pesaing terkait
                    if (item.competitors && item.competitors.length > 0) {
                        item.competitors.forEach(function(comp) {
                            if (comp.lat && comp.lng) {
                                const compMarker = L.marker([comp.lat, comp.lng], { icon: createCompetitorPin() });
                                const compPopup = `
                                    <div style="font-family: ui-sans-serif, system-ui, sans-serif; padding: 2px; min-width: 180px;">
                                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2px;">
                                            <span style="font-size: 10px; font-weight: bold; color: #D97706; text-transform: uppercase;">Pesaing Aqiqah</span>
                                            ${comp.rating ? `<span style="font-size: 11px; font-weight: bold; color: #D97706;">⭐ ${comp.rating}</span>` : ''}
                                        </div>
                                        <h4 style="font-weight: 700; font-size: 12px; color: #111827; margin: 0 0 2px 0;">${comp.nama}</h4>
                                        <p style="font-size: 11px; color: #4B5563; margin: 0;">Data pesaing terkait untuk lokasi <b>${item.nama_pemilik}</b></p>
                                    </div>
                                `;
                                compMarker.bindPopup(compPopup);
                                activeCompetitorLayerGroup.addLayer(compMarker);
                            }
                        });
                    }
                }

                const detailBaseUrl = "{{ url('/manajer/observasi') }}";

                // Tampilkan penanda Alternatif Lokasi (Initial load)
                mapLocationsData.forEach(function(item) {
                    if (item.lat && item.lng) {
                        let color = 'blue';
                        let rankText = item.rank;
                        if (item.category === 'terbaik') {
                            color = 'green';
                            rankText = '1';
                        } else if (item.category === 'sedang') {
                            color = 'yellow';
                        } else if (item.category === 'kurang') {
                            color = 'red';
                        }

                        const pinIcon = createLocationPin(color, rankText);
                        const marker = L.marker([item.lat, item.lng], { icon: pinIcon });

                        const rankBadgeClass = item.category === 'terbaik' ? 'background-color: #10B981;' : (item.category === 'sedang' ? 'background-color: #F59E0B;' : (item.category === 'kurang' ? 'background-color: #EF4444;' : 'background-color: #6B7280;'));
                        const rankTitle = item.rank !== '-' ? `Peringkat #${item.rank}` : 'Belum Dinilai';
                        const prefDisplay = item.nilai_preferensi !== '-' ? item.nilai_preferensi : '-';
                        const rankDisplay = item.rank !== '-' ? `#${item.rank}` : 'Belum Dinilai';
                        const namaLokasi = item.nama_lokasi || item.alamat || 'Lokasi Observasi';
                        const detailUrl = `${detailBaseUrl}/${item.id}?ref=dashboard`;

                        const popupContent = `
                            <div style="min-width: 230px; font-family: ui-sans-serif, system-ui, sans-serif; padding: 4px;">
                                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px; padding-bottom: 6px; border-bottom: 1px solid #E5E7EB;">
                                    <span style="${rankBadgeClass} color: white; font-size: 11px; font-weight: 700; padding: 3px 8px; border-radius: 6px;">
                                        ${rankTitle}
                                    </span>
                                    <span style="font-size: 11px; font-weight: 700; color: #059669; background-color: #ECFDF5; padding: 3px 8px; border-radius: 6px; border: 1px solid #A7F3D0;">
                                        Skor: ${prefDisplay}
                                    </span>
                                </div>
                                <h4 style="font-weight: 700; font-size: 13px; color: #111827; margin: 0 0 6px 0; line-height: 1.4;">
                                    ${namaLokasi}
                                </h4>
                                <div style="font-size: 11px; color: #4B5563; margin-bottom: 10px; display: flex; flex-direction: column; gap: 3px;">
                                    <div><span style="font-weight: 600; color: #374151;">Pemilik Lokasi:</span> ${item.nama_pemilik || '-'}</div>
                                    <div><span style="font-weight: 600; color: #374151;">Skor Rekomendasi:</span> ${prefDisplay}</div>
                                    <div><span style="font-weight: 600; color: #374151;">Peringkat:</span> ${rankDisplay}</div>
                                </div>
                                <a href="${detailUrl}" style="display: block; width: 100%; text-align: center; background-color: #16A34A; color: white; font-weight: 700; font-size: 11px; padding: 7px 12px; border-radius: 6px; text-decoration: none; box-shadow: 0 1px 2px rgba(0,0,0,0.1); transition: background-color 0.2s;">
                                    Lihat Detail Observasi
                                </a>
                            </div>
                        `;

                        marker.bindPopup(popupContent);

                        // On Marker Click event
                        marker.on('click', function() {
                            selectLocation(item);
                        });

                        locationLayerGroup.addLayer(marker);
                        allBounds.push([item.lat, item.lng]);
                    }
                });

                const defaultCenter = allBounds.length > 0 ? allBounds[0] : [-6.9175, 107.6191];
                const map = L.map('dashboardMap', {
                    center: defaultCenter,
                    zoom: 11,
                    layers: [locationLayerGroup, activePolylineLayerGroup, activeRphLayerGroup, activeCompetitorLayerGroup]
                });

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; OpenStreetMap'
                }).addTo(map);

                if (allBounds.length === 1) {
                    map.setView(allBounds[0], 13);
                } else if (allBounds.length > 1) {
                    map.fitBounds(allBounds, { padding: [40, 40] });
                }
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('rankingChart');
            if (ctx) {
                new Chart(ctx.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: {!! json_encode($chartLabels) !!},
                        datasets: [{
                            label: 'Skor Rekomendasi',

                            data: {!! json_encode($chartData) !!},
                            backgroundColor: 'rgba(22, 163, 74, 0.6)', 
                            borderColor: 'rgba(22, 163, 74, 1)',
                            borderWidth: 1,
                            borderRadius: 6,
                            hoverBackgroundColor: 'rgba(21, 128, 61, 0.8)',
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                suggestedMax: 1,
                                grid: {
                                    color: '#f3f4f6',
                                    drawBorder: false,
                                }
                            },
                            x: {
                                grid: {
                                    display: false,
                                    drawBorder: false,
                                },
                                ticks: {
                                    maxRotation: 45,
                                    minRotation: 0,
                                    autoSkip: false
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: 'rgba(17, 24, 39, 0.9)',
                                padding: 12,
                                titleFont: { size: 13, family: 'Arial' },
                                bodyFont: { size: 12, family: 'Arial' },
                                cornerRadius: 8,
                                displayColors: false
                            }
                        }
                    }
                });
            }
        });
    </script>
</x-app-layout>
