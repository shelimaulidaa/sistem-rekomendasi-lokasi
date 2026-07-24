<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Observasi - {{ $observasi->nama_pemilik }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 20px 25px;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10.5px;
            line-height: 1.4;
            color: #374151;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #10B981;
            padding-bottom: 6px;
            margin-bottom: 10px;
        }
        .header h1 {
            font-size: 16px;
            margin: 0;
            color: #111827;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .header p {
            margin: 2px 0 0 0;
            color: #6B7280;
            font-size: 9.5px;
        }
        .section-title {
            font-size: 10.5px;
            font-weight: bold;
            color: #047857;
            border-bottom: 1px solid #D1D5DB;
            padding-bottom: 2px;
            margin-top: 8px;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .table-layout {
            width: 100%;
            border-collapse: collapse;
        }
        .table-layout td {
            vertical-align: top;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 2px 0;
            vertical-align: top;
        }
        .info-label {
            color: #6B7280;
            font-weight: 500;
            width: 115px;
        }
        .info-value {
            color: #111827;
            font-weight: bold;
        }
        .grid-data {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 4px;
        }
        .grid-data th, .grid-data td {
            border: 1px solid #E5E7EB;
            padding: 3.5px 5px;
            text-align: left;
            font-size: 9.5px;
        }
        .grid-data th {
            background-color: #F9FAFB;
            color: #4B5563;
            font-weight: bold;
            width: 16%;
        }
        .grid-data td {
            width: 17%;
        }
        .badge-score {
            float: right;
            background-color: #D1FAE5;
            color: #065F46;
            border: 1px solid #A7F3D0;
            padding: 1px 6px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 9px;
            margin-top: -2px;
        }
        .indicator-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .indicator-item {
            margin-bottom: 2.5px;
            font-size: 9px;
        }
        .checkbox-box {
            display: inline-block;
            width: 10px;
            height: 10px;
            border: 1px solid #10B981;
            margin-right: 5px;
            text-align: center;
            line-height: 10px;
            font-size: 8px;
            font-weight: bold;
        }
        .checkbox-checked {
            background-color: #D1FAE5;
            color: #065F46;
            border-color: #065F46;
        }
        .checkbox-unchecked {
            background-color: #FEE2E2;
            color: #991B1B;
            border-color: #991B1B;
        }
        .footer {
            margin-top: 12px;
            text-align: center;
            font-size: 8px;
            color: #9CA3AF;
            border-top: 1px solid #E5E7EB;
            padding-top: 4px;
        }
    </style>
</head>
<body>

    <!-- 1. Header Laporan -->
    <div class="header">
        <h1>Laporan Detail Observasi Lokasi</h1>
        <p>Sistem Rekomendasi Pemilihan Lokasi Cabang Baru</p>
    </div>

    <!-- 2. Informasi Observasi -->
    <div class="section-title">Informasi Observasi</div>
    <table class="table-layout" style="margin-bottom: 4px;">
        <tr>
            <td style="width: 50%; padding-right: 10px;">
                <table class="info-table">
                    <tr>
                        <td class="info-label">Nama Pemilik</td>
                        <td class="info-value">: {{ $observasi->nama_pemilik ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">Nomor Telepon</td>
                        <td class="info-value">: {{ $observasi->nomor_telepon_pemilik ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">Alamat Lengkap</td>
                        <td class="info-value">: {{ $observasi->alamat_lengkap ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">Wilayah</td>
                        <td class="info-value">: Kec. {{ $observasi->kecamatan ?? '-' }}, {{ $observasi->kabupaten_kota ?? '-' }}</td>
                    </tr>
                </table>
            </td>
            <td style="width: 50%; padding-left: 10px;">
                <table class="info-table">
                    <tr>
                        <td class="info-label">Periode Observasi</td>
                        <td class="info-value">: {{ $observasi->periode->nama_periode ?? $observasi->batch->nama_batch ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">Waktu Observasi</td>
                        <td class="info-value">: {{ \Carbon\Carbon::parse($observasi->tanggal_observasi)->translatedFormat('d F Y') }} @if($observasi->jam_observasi) ({{ \Carbon\Carbon::parse($observasi->jam_observasi)->format('H:i') }} WIB) @endif</td>
                    </tr>
                    <tr>
                        <td class="info-label">Petugas Lapangan</td>
                        <td class="info-value">: {{ $observasi->user->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">Pendamping</td>
                        <td class="info-value">: 
                            @if($observasi->anggota_pendamping && is_array($observasi->anggota_pendamping))
                                {{ implode(', ', $observasi->anggota_pendamping) }}
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- 3. Informasi Wilayah -->
    <div class="section-title">Informasi Wilayah</div>
    <table class="grid-data" style="margin-bottom: 4px;">
        <tr>
            <th>Provinsi</th>
            <td>{{ $observasi->provinsi ?? '-' }}</td>
            <th>Kabupaten/Kota</th>
            <td>{{ $observasi->kabupaten_kota ?? '-' }}</td>
            <th>Kecamatan</th>
            <td>{{ $observasi->kecamatan ?? '-' }}</td>
        </tr>
        <tr>
            <th>UMK Kab/Kota</th>
            <td>{{ $observasi->umk ? 'Rp ' . number_format($observasi->umk, 0, ',', '.') : '-' }}</td>
            <th>PDRB Per Kapita</th>
            <td>{{ $observasi->pdrb ? 'Rp ' . number_format($observasi->pdrb, 0, ',', '.') : '-' }}</td>
            <th>Penduduk Muslim</th>
            <td>{{ $observasi->jumlah_penduduk_muslim ? number_format($observasi->jumlah_penduduk_muslim, 0, ',', '.') . ' Jiwa' : '-' }}</td>
        </tr>
    </table>

    <!-- 4. Informasi Bangunan & Operasional -->
    <div class="section-title">Informasi Bangunan & Operasional</div>
    <table class="grid-data" style="margin-bottom: 4px;">
        <tr>
            <th>Harga Sewa / Thn</th>
            <td style="font-weight: bold; color: #047857;">Rp {{ number_format($observasi->harga_sewa, 0, ',', '.') }}</td>
            <th>Jenis Bangunan</th>
            <td>{{ $observasi->jenis_bangunan ?? '-' }}</td>
            <th>Kondisi Bangunan</th>
            <td>{{ $observasi->kondisi_bangunan ?? '-' }}</td>
        </tr>
        <tr>
            <th>Luas Bangunan</th>
            <td>{{ floatval($observasi->luas_bangunan) }} m²</td>
            <th>Luas Tanah</th>
            <td>{{ floatval($observasi->luas_tanah) }} m²</td>
            <th>Jumlah Lantai</th>
            <td>{{ $observasi->jumlah_lantai ?? '-' }} Lantai</td>
        </tr>
        <tr>
            <th>Ruang Ops</th>
            <td>{{ $observasi->jumlah_ruangan }} Ruang</td>
            <th>Kamar Mandi / WC</th>
            <td>{{ $observasi->jumlah_wc }} WC</td>
            <th>Sumber Air</th>
            <td>{{ $observasi->sumber_air }}</td>
        </tr>
        <tr>
            <th>Daya Listrik</th>
            <td>{{ $observasi->daya_listrik ?? '-' }}</td>
            <th>Area Parkir</th>
            <td>{{ $observasi->area_parkir ?? '-' }}</td>
            <th>Lebar Jalan</th>
            <td>{{ $observasi->lebar_jalan ?? '-' }}</td>
        </tr>
    </table>

    <!-- 5. Analisis Spasial (RPH dan Kompetitor) -->
    <div class="section-title">Analisis Spasial (RPH & Kompetitor)</div>
    @php
        $rphDistance = ($observasi->jarak_rph !== null && $observasi->jarak_rph !== '') ? $observasi->jarak_rph : ($spatialData['nearest_rph_distance'] ?? null);
        $rphName = !empty($observasi->nearest_rph_name) ? $observasi->nearest_rph_name : ($spatialData['nearest_rph_name'] ?? 'RPH Terdekat');
        $compList = $spatialData['competitors_list'] ?? [];
        $compCount = (int) ($spatialData['competitor_count'] ?? $observasi->jumlah_kompetitor ?? count($compList));
        $ratings = array_filter(array_column($compList, 'rating'));
        $avgRating = count($ratings) > 0 ? round(array_sum($ratings) / count($ratings), 1) : null;
    @endphp
    <table class="grid-data" style="margin-bottom: 4px;">
        <tr>
            <th>RPH Terdekat</th>
            <td>{{ $rphName }}</td>
            <th>Jarak ke RPH</th>
            <td>{{ $rphDistance !== null ? rtrim(rtrim(number_format((float)$rphDistance, 4, '.', ''), '0'), '.') : '-' }} KM</td>
            <th>Total Kompetitor</th>
            <td>{{ $compCount }} Titik @if($avgRating !== null && $avgRating > 0) (★ {{ $avgRating }} / 5) @endif</td>
        </tr>
    </table>
    @if(count($compList) > 0)
        <table class="grid-data" style="margin-bottom: 4px;">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 55%;">Nama Kompetitor</th>
                    <th style="width: 20%;">Jarak (KM)</th>
                    <th style="width: 20%;">Rating</th>
                </tr>
            </thead>
            <tbody>
                @foreach($compList as $index => $comp)
                    <tr>
                        <td style="text-align: center;">{{ $index + 1 }}</td>
                        <td>{{ $comp['nama'] ?? '-' }}</td>
                        <td>{{ isset($comp['distance']) ? $comp['distance'] . ' KM' : '-' }}</td>
                        <td>{{ isset($comp['rating']) && $comp['rating'] ? '★ ' . $comp['rating'] . ' / 5' : '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <!-- 6. Hasil Penilaian Kriteria (Aksesibilitas dan Kelayakan Bangunan) -->
    <div class="section-title">Hasil Penilaian Kriteria</div>
    @php
        $truesC1 = ($observasi->akses_jalan_utama ? 1 : 0) + ($observasi->akses_kendaraan_operasional ? 1 : 0) + ($observasi->kondisi_jalan_baik ? 1 : 0) + ($observasi->mudah_ditemukan_google_maps ? 1 : 0) + ($observasi->mudah_dijangkau_pelanggan ? 1 : 0);
        $scoreC1 = max(1, $truesC1);

        $truesC2 = ($observasi->luas_bangunan_mencukupi ? 1 : 0) + ($observasi->kondisi_bangunan_baik ? 1 : 0) + ($observasi->ventilasi_sirkulasi_memadai ? 1 : 0) + ($observasi->air_listrik_tersedia ? 1 : 0) + ($observasi->area_parkir_memadai ? 1 : 0);
        $scoreC2 = max(1, $truesC2);
    @endphp
    <table class="table-layout" style="margin-bottom: 6px;">
        <tr>
            <!-- Left Column: C1 Aksesibilitas -->
            <td style="width: 50%; padding-right: 10px; border-right: 1px dashed #D1D5DB;">
                <div style="font-weight: bold; margin-bottom: 4px; color: #111827; font-size: 10px;">
                    Aksesibilitas (C1)
                    <span class="badge-score">Skor: {{ $scoreC1 }} / 5</span>
                </div>
                <ul class="indicator-list">
                    <li class="indicator-item">
                        <span class="checkbox-box {{ $observasi->akses_jalan_utama ? 'checkbox-checked' : 'checkbox-unchecked' }}">
                            {!! $observasi->akses_jalan_utama ? '✓' : '✗' !!}
                        </span>
                        Dekat jalan utama
                    </li>
                    <li class="indicator-item">
                        <span class="checkbox-box {{ $observasi->akses_kendaraan_operasional ? 'checkbox-checked' : 'checkbox-unchecked' }}">
                            {!! $observasi->akses_kendaraan_operasional ? '✓' : '✗' !!}
                        </span>
                        Akses Roda 4
                    </li>
                    <li class="indicator-item">
                        <span class="checkbox-box {{ $observasi->kondisi_jalan_baik ? 'checkbox-checked' : 'checkbox-unchecked' }}">
                            {!! $observasi->kondisi_jalan_baik ? '✓' : '✗' !!}
                        </span>
                        Kondisi jalan bagus
                    </li>
                    <li class="indicator-item">
                        <span class="checkbox-box {{ $observasi->mudah_ditemukan_google_maps ? 'checkbox-checked' : 'checkbox-unchecked' }}">
                            {!! $observasi->mudah_ditemukan_google_maps ? '✓' : '✗' !!}
                        </span>
                        Mudah ditemukan (Google Maps)
                    </li>
                    <li class="indicator-item">
                        <span class="checkbox-box {{ $observasi->mudah_dijangkau_pelanggan ? 'checkbox-checked' : 'checkbox-unchecked' }}">
                            {!! $observasi->mudah_dijangkau_pelanggan ? '✓' : '✗' !!}
                        </span>
                        Mudah dijangkau pelanggan
                    </li>
                </ul>
            </td>

            <!-- Right Column: C2 Kelayakan Bangunan -->
            <td style="width: 50%; padding-left: 10px;">
                <div style="font-weight: bold; margin-bottom: 4px; color: #111827; font-size: 10px;">
                    Kelayakan Bangunan (C2)
                    <span class="badge-score">Skor: {{ $scoreC2 }} / 5</span>
                </div>
                <ul class="indicator-list">
                    <li class="indicator-item">
                        <span class="checkbox-box {{ $observasi->luas_bangunan_mencukupi ? 'checkbox-checked' : 'checkbox-unchecked' }}">
                            {!! $observasi->luas_bangunan_mencukupi ? '✓' : '✗' !!}
                        </span>
                        Luas bangunan mencukupi
                    </li>
                    <li class="indicator-item">
                        <span class="checkbox-box {{ $observasi->kondisi_bangunan_baik ? 'checkbox-checked' : 'checkbox-unchecked' }}">
                            {!! $observasi->kondisi_bangunan_baik ? '✓' : '✗' !!}
                        </span>
                        Bangunan layak (siap digunakan)
                    </li>
                    <li class="indicator-item">
                        <span class="checkbox-box {{ $observasi->ventilasi_sirkulasi_memadai ? 'checkbox-checked' : 'checkbox-unchecked' }}">
                            {!! $observasi->ventilasi_sirkulasi_memadai ? '✓' : '✗' !!}
                        </span>
                        Ventilasi & sirkulasi baik
                    </li>
                    <li class="indicator-item">
                        <span class="checkbox-box {{ $observasi->air_listrik_tersedia ? 'checkbox-checked' : 'checkbox-unchecked' }}">
                            {!! $observasi->air_listrik_tersedia ? '✓' : '✗' !!}
                        </span>
                        Fasilitas air & listrik memadai
                    </li>
                    <li class="indicator-item">
                        <span class="checkbox-box {{ $observasi->area_parkir_memadai ? 'checkbox-checked' : 'checkbox-unchecked' }}">
                            {!! $observasi->area_parkir_memadai ? '✓' : '✗' !!}
                        </span>
                        Area parkir memadai
                    </li>
                </ul>
            </td>
        </tr>
    </table>

    <!-- 7. Catatan Tambahan -->
    <div class="section-title">Catatan Tambahan</div>
    <div style="background-color: #F9FAFB; border: 1px solid #E5E7EB; padding: 6px 8px; border-radius: 4px; font-style: italic; min-height: 30px; font-size: 9.5px; color: #4B5563;">
        {{ $observasi->catatan ?: 'Tidak ada catatan tambahan.' }}
    </div>

    <!-- Tanda Tangan Penanggung Jawab -->
    <table style="width: 100%; margin-top: 18px; border-collapse: collapse; page-break-inside: avoid;">
        <tr>
            <td style="width: 60%;"></td>
            <td style="width: 40%; text-align: center;">
                <div style="font-size: 10px; color: #374151; font-weight: bold; margin-bottom: 50px;">
                    Penanggung Jawab
                </div>
                <div style="font-size: 10px; color: #111827; font-weight: bold;">
                    {{ $observasi->user->name ?? '-' }}
                </div>
            </td>
        </tr>
    </table>

    <!-- 8. Halaman Dokumentasi Lokasi (Halaman Terakhir Jika Ada Foto) -->
    @php
        $photos = $observasi->dokumentasiLokasis ? $observasi->dokumentasiLokasis->take(4) : collect();
    @endphp

    @if($photos->count() > 0)
        <div style="page-break-before: always;">
            <div class="header" style="margin-bottom: 12px;">
                <h1>Dokumentasi Observasi Lokasi</h1>
                <p>{{ $observasi->nama_pemilik }} - Kec. {{ $observasi->kecamatan }}, {{ $observasi->kabupaten_kota }}</p>
            </div>

            <div class="section-title" style="margin-top: 0; margin-bottom: 12px;">Dokumentasi Foto Lokasi</div>

            <table style="width: 100%; border-collapse: separate; border-spacing: 12px;">
                @foreach($photos->chunk(2) as $row)
                    <tr>
                        @foreach($row as $foto)
                            @php
                                $fullPath = null;
                                if (!empty($foto->foto_path)) {
                                    if (file_exists(public_path('storage/' . $foto->foto_path))) {
                                        $fullPath = public_path('storage/' . $foto->foto_path);
                                    } elseif (file_exists(storage_path('app/public/' . $foto->foto_path))) {
                                        $fullPath = storage_path('app/public/' . $foto->foto_path);
                                    }
                                }
                            @endphp
                            <td style="width: 50%; text-align: center; vertical-align: middle; height: 260px; padding: 6px; border: 1px solid #E5E7EB; border-radius: 6px; background-color: #FAFAFA;">
                                @if($fullPath)
                                    <img src="{{ $fullPath }}" style="max-width: 100%; max-height: 248px; width: auto; height: auto; display: block; margin: 0 auto; object-fit: contain; border-radius: 4px;">
                                @else
                                    <div style="color: #9CA3AF; font-size: 9px; font-style: italic;">Foto tidak dapat dimuat</div>
                                @endif
                            </td>
                        @endforeach
                        @if($row->count() < 2)
                            <td style="width: 50%;"></td>
                        @endif
                    </tr>
                @endforeach
            </table>
        </div>
    @endif

    <div class="footer">
        Dicetak otomatis oleh Sistem Pendukung Keputusan Pemilihan Lokasi pada {{ now()->translatedFormat('d F Y H:i') }}
    </div>

</body>
</html>
