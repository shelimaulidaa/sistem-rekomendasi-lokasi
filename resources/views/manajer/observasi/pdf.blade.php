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
            font-size: 10px;
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
            margin-top: 10px;
            margin-bottom: 6px;
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
            width: 120px;
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
            padding: 4px 6px;
            text-align: left;
            font-size: 9px;
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
        .topsis-card {
            background-color: #065F46;
            color: #FFFFFF;
            padding: 8px 12px;
            border-radius: 6px;
            margin-bottom: 10px;
        }
        .topsis-card table {
            width: 100%;
            border-collapse: collapse;
        }
        .topsis-card td {
            color: #FFFFFF;
            vertical-align: middle;
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
            margin-bottom: 3px;
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
        .text-strike {
            text-decoration: line-through;
            color: #9CA3AF;
        }
        .footer {
            margin-top: 15px;
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

    <!-- 2. Hasil Penilaian TOPSIS (Jika Ada) -->
    @if(isset($hasilTopsis) && $hasilTopsis)
        <div class="topsis-card">
            <table>
                <tr>
                    <td style="width: 50%;">
                        <div style="font-size: 8.5px; text-transform: uppercase; letter-spacing: 0.5px; opacity: 0.85;">Hasil Penilaian Rekomendasi (TOPSIS)</div>
                        <div style="font-size: 13px; font-weight: bold; margin-top: 2px;">
                            Skor Preferensi: {{ number_format($hasilTopsis->nilai_preferensi, 4) }}
                        </div>
                    </td>
                    <td style="width: 50%; text-align: right;">
                        <div style="font-size: 8.5px; text-transform: uppercase; letter-spacing: 0.5px; opacity: 0.85;">Peringkat Rekomendasi</div>
                        <div style="font-size: 15px; font-weight: bold; margin-top: 2px;">
                            #{{ $hasilTopsis->ranking }}
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    @endif

    <!-- 3. Informasi Observasi & Pemilik -->
    <div class="section-title">Informasi Observasi & Pemilik</div>
    <table class="table-layout" style="margin-bottom: 4px;">
        <tr>
            <td style="width: 50%; padding-right: 10px;">
                <table class="info-table">
                    @if(!empty($observasi->nama_pemilik))
                    <tr>
                        <td class="info-label">Nama Pemilik</td>
                        <td class="info-value">: {{ $observasi->nama_pemilik }}</td>
                    </tr>
                    @endif
                    @if(!empty($observasi->nomor_telepon_pemilik))
                    <tr>
                        <td class="info-label">No. Telepon</td>
                        <td class="info-value">: {{ $observasi->nomor_telepon_pemilik }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td class="info-label">Observer (Petugas)</td>
                        <td class="info-value">: {{ $observasi->user->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">Anggota Pendamping</td>
                        <td class="info-value">: 
                            @if(!empty($observasi->anggota_pendamping) && is_array($observasi->anggota_pendamping) && count(array_filter($observasi->anggota_pendamping)) > 0)
                                {{ implode(', ', array_filter($observasi->anggota_pendamping)) }}
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width: 50%; padding-left: 10px;">
                <table class="info-table">
                    <tr>
                        <td class="info-label">Periode Observasi</td>
                        <td class="info-value">: {{ $observasi->periode->nama_periode ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">Tanggal Survei</td>
                        <td class="info-value">: 
                            @if($observasi->tanggal_observasi)
                                {{ \Carbon\Carbon::parse($observasi->tanggal_observasi)->translatedFormat('d F Y') }}
                                @if($observasi->jam_observasi)
                                    ({{ \Carbon\Carbon::parse($observasi->jam_observasi)->format('H:i') }} WIB)
                                @endif
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="info-label">Kecamatan</td>
                        <td class="info-value">: {{ $observasi->kecamatan ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">Kabupaten / Kota</td>
                        <td class="info-value">: {{ $observasi->kabupaten_kota ?? '-' }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- 4. Alamat Lengkap, Wilayah & Koordinat -->
    <div class="section-title">Alamat Lengkap & Profil Wilayah</div>
    <table class="grid-data" style="margin-bottom: 4px;">
        <tr>
            <th>Alamat Lengkap</th>
            <td colspan="5" style="font-weight: bold;">{{ $observasi->alamat_lengkap ?? '-' }}</td>
        </tr>
        <tr>
            <th>Provinsi</th>
            <td>{{ $observasi->provinsi ?? '-' }}</td>
            <th>Kabupaten/Kota</th>
            <td>{{ $observasi->kabupaten_kota ?? '-' }}</td>
            <th>Kecamatan</th>
            <td>{{ $observasi->kecamatan ?? '-' }}</td>
        </tr>
        @if($observasi->umk || $observasi->pdrb || $observasi->jumlah_penduduk_muslim)
        <tr>
            <th>UMK Kab/Kota</th>
            <td>{{ $observasi->umk ? 'Rp ' . number_format($observasi->umk, 2, ',', '.') : '-' }}</td>
            <th>PDRB Per Kapita</th>
            <td>{{ $observasi->pdrb ? 'Rp ' . number_format($observasi->pdrb, 0, ',', '.') : '-' }}</td>
            <th>Penduduk Muslim</th>
            <td>{{ $observasi->jumlah_penduduk_muslim ? number_format($observasi->jumlah_penduduk_muslim, 0, ',', '.') . ' Jiwa' : '-' }}</td>
        </tr>
        @endif
        @if($observasi->latitude && $observasi->longitude)
        <tr>
            <th>Latitude</th>
            <td>{{ $observasi->latitude }}</td>
            <th>Longitude</th>
            <td colspan="3">{{ $observasi->longitude }}</td>
        </tr>
        @endif
    </table>

    <!-- 5. Informasi Pendukung (Operasional & Bangunan) -->
    @php
        $buildingInfoItems = [];
        if ($observasi->harga_sewa !== null && $observasi->harga_sewa !== '') {
            $buildingInfoItems[] = ['label' => 'Biaya Sewa / Thn', 'value' => 'Rp ' . number_format($observasi->harga_sewa, 0, ',', '.'), 'is_highlight' => true];
        }
        if ($observasi->luas_bangunan !== null && $observasi->luas_bangunan !== '') {
            $buildingInfoItems[] = ['label' => 'Luas Bangunan', 'value' => floatval($observasi->luas_bangunan) . ' m²'];
        }
        if ($observasi->luas_tanah !== null && $observasi->luas_tanah !== '') {
            $buildingInfoItems[] = ['label' => 'Luas Tanah', 'value' => floatval($observasi->luas_tanah) . ' m²'];
        }
        if ($observasi->jumlah_lantai !== null && $observasi->jumlah_lantai !== '') {
            $buildingInfoItems[] = ['label' => 'Jumlah Lantai', 'value' => $observasi->jumlah_lantai . ' Lantai'];
        }
        if ($observasi->jumlah_ruangan !== null && $observasi->jumlah_ruangan !== '') {
            $buildingInfoItems[] = ['label' => 'Ruang Ops', 'value' => $observasi->jumlah_ruangan . ' Ruang'];
        }
        if ($observasi->jumlah_wc !== null && $observasi->jumlah_wc !== '') {
            $buildingInfoItems[] = ['label' => 'Kamar Mandi / WC', 'value' => $observasi->jumlah_wc];
        }
        if (!empty($observasi->kondisi_bangunan)) {
            $buildingInfoItems[] = ['label' => 'Kondisi Bangunan', 'value' => $observasi->kondisi_bangunan];
        }
        if (!empty($observasi->sumber_air)) {
            $buildingInfoItems[] = ['label' => 'Sumber Air Bersih', 'value' => $observasi->sumber_air];
        }
        if (!empty($observasi->daya_listrik)) {
            $buildingInfoItems[] = ['label' => 'Daya Listrik', 'value' => $observasi->daya_listrik];
        }
        if (!empty($observasi->area_parkir)) {
            $buildingInfoItems[] = ['label' => 'Akses Parkir', 'value' => $observasi->area_parkir];
        }
        if (!empty($observasi->lebar_jalan)) {
            $buildingInfoItems[] = ['label' => 'Lebar Jalan', 'value' => $observasi->lebar_jalan];
        }
        if (!empty($observasi->ventilasi)) {
            $buildingInfoItems[] = ['label' => 'Kualitas Ventilasi', 'value' => $observasi->ventilasi];
        }
        if (!empty($observasi->sirkulasi)) {
            $buildingInfoItems[] = ['label' => 'Sirkulasi Udara', 'value' => $observasi->sirkulasi];
        }
    @endphp
    @if(count($buildingInfoItems) > 0)
    <div class="section-title">Informasi Pendukung (Operasional & Bangunan)</div>
    <table class="grid-data" style="margin-bottom: 4px;">
        @foreach(array_chunk($buildingInfoItems, 3) as $row)
        <tr>
            @foreach($row as $item)
                <th>{{ $item['label'] }}</th>
                <td style="{{ !empty($item['is_highlight']) ? 'font-weight: bold; color: #047857;' : '' }}">{{ $item['value'] }}</td>
            @endforeach
            @for($i = count($row); $i < 3; $i++)
                <th></th>
                <td></td>
            @endfor
        </tr>
        @endforeach
    </table>
    @endif

    <!-- 6. Ringkasan Hasil Observasi -->
    <div class="section-title">Ringkasan Hasil Observasi</div>
    @php
        $rphDistance = ($observasi->jarak_rph !== null && $observasi->jarak_rph !== '') ? $observasi->jarak_rph : ($spatialData['nearest_rph_distance'] ?? null);
        $rphName = !empty($observasi->nearest_rph_name) ? $observasi->nearest_rph_name : ($spatialData['nearest_rph_name'] ?? 'RPH Terdekat');
        $compList = $spatialData['competitors_list'] ?? [];
        $compCount = (int) ($spatialData['competitor_count'] ?? $observasi->jumlah_kompetitor ?? count($compList));
    @endphp

    <table class="grid-data" style="margin-bottom: 4px;">
        <tr>
            <th>RPH Terdekat</th>
            <td>{{ $rphName }}</td>
            <th>Jarak ke RPH</th>
            <td>{{ $rphDistance !== null ? rtrim(rtrim(number_format((float)$rphDistance, 4, '.', ''), '0'), '.') : '-' }} KM</td>
            <th>Tingkat Pesaing</th>
            <td>{{ $compCount }} titik (Radius ±5 KM)</td>
        </tr>
    </table>

    @if(count($compList) > 0)
        <table class="grid-data" style="margin-bottom: 4px;">
            <thead>
                <tr>
                    <th style="width: 5%; text-align: center;">No</th>
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
                        <td>{{ isset($comp['rating']) && $comp['rating'] ? '★ ' . number_format((float)$comp['rating'], 1, '.', '') . ' / 5' : '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <!-- Indikator Kelayakan & Aksesibilitas -->
    @php
        $ltrues = ($observasi->luas_mencukupi ? 1 : 0) + ($observasi->bangunan_layak ? 1 : 0) + ($observasi->ventilasi_baik ? 1 : 0) + ($observasi->air_listrik_memadai ? 1 : 0) + ($observasi->parkir_memadai ? 1 : 0);
        $layakScore = max(1, $ltrues);

        $trues = ($observasi->akses_roda4 ? 1 : 0) + ($observasi->jalan_bagus ? 1 : 0) + ($observasi->dekat_fasilitas ? 1 : 0) + ($observasi->mudah_ditemukan ? 1 : 0) + ($observasi->mudah_dijangkau ? 1 : 0);
        $aksesScore = max(1, $trues);
    @endphp

    <table class="table-layout" style="margin-bottom: 6px;">
        <tr>
            <!-- Left Column: Kelayakan Bangunan -->
            <td style="width: 50%; padding-right: 10px; border-right: 1px dashed #D1D5DB;">
                <div style="font-weight: bold; margin-bottom: 4px; color: #111827; font-size: 9.5px;">
                    2. Kelayakan Bangunan
                    <span class="badge-score">Skor: {{ $layakScore }} / 5</span>
                </div>
                <ul class="indicator-list">
                    <li class="indicator-item">
                        <span class="checkbox-box {{ $observasi->luas_mencukupi ? 'checkbox-checked' : 'checkbox-unchecked' }}">
                            {!! $observasi->luas_mencukupi ? '✓' : '✗' !!}
                        </span>
                        <span class="{{ $observasi->luas_mencukupi ? '' : 'text-strike' }}">Luas bangunan mencukupi</span>
                    </li>
                    <li class="indicator-item">
                        <span class="checkbox-box {{ $observasi->bangunan_layak ? 'checkbox-checked' : 'checkbox-unchecked' }}">
                            {!! $observasi->bangunan_layak ? '✓' : '✗' !!}
                        </span>
                        <span class="{{ $observasi->bangunan_layak ? '' : 'text-strike' }}">Kondisi fisik bangunan layak</span>
                    </li>
                    <li class="indicator-item">
                        <span class="checkbox-box {{ $observasi->ventilasi_baik ? 'checkbox-checked' : 'checkbox-unchecked' }}">
                            {!! $observasi->ventilasi_baik ? '✓' : '✗' !!}
                        </span>
                        <span class="{{ $observasi->ventilasi_baik ? '' : 'text-strike' }}">Ventilasi & sirkulasi udara memadai</span>
                    </li>
                    <li class="indicator-item">
                        <span class="checkbox-box {{ $observasi->air_listrik_memadai ? 'checkbox-checked' : 'checkbox-unchecked' }}">
                            {!! $observasi->air_listrik_memadai ? '✓' : '✗' !!}
                        </span>
                        <span class="{{ $observasi->air_listrik_memadai ? '' : 'text-strike' }}">Fasilitas air bersih & listrik memadai</span>
                    </li>
                    <li class="indicator-item">
                        <span class="checkbox-box {{ $observasi->parkir_memadai ? 'checkbox-checked' : 'checkbox-unchecked' }}">
                            {!! $observasi->parkir_memadai ? '✓' : '✗' !!}
                        </span>
                        <span class="{{ $observasi->parkir_memadai ? '' : 'text-strike' }}">Area parkir mendukung</span>
                    </li>
                </ul>
            </td>

            <!-- Right Column: Aksesibilitas Lokasi -->
            <td style="width: 50%; padding-left: 10px;">
                <div style="font-weight: bold; margin-bottom: 4px; color: #111827; font-size: 9.5px;">
                    3. Aksesibilitas Lokasi
                    <span class="badge-score">Skor: {{ $aksesScore }} / 5</span>
                </div>
                <ul class="indicator-list">
                    <li class="indicator-item">
                        <span class="checkbox-box {{ $observasi->dekat_fasilitas ? 'checkbox-checked' : 'checkbox-unchecked' }}">
                            {!! $observasi->dekat_fasilitas ? '✓' : '✗' !!}
                        </span>
                        <span class="{{ $observasi->dekat_fasilitas ? '' : 'text-strike' }}">Dekat dengan jalan utama</span>
                    </li>
                    <li class="indicator-item">
                        <span class="checkbox-box {{ $observasi->akses_roda4 ? 'checkbox-checked' : 'checkbox-unchecked' }}">
                            {!! $observasi->akses_roda4 ? '✓' : '✗' !!}
                        </span>
                        <span class="{{ $observasi->akses_roda4 ? '' : 'text-strike' }}">Dapat dilalui kendaraan (roda 4)</span>
                    </li>
                    <li class="indicator-item">
                        <span class="checkbox-box {{ $observasi->jalan_bagus ? 'checkbox-checked' : 'checkbox-unchecked' }}">
                            {!! $observasi->jalan_bagus ? '✓' : '✗' !!}
                        </span>
                        <span class="{{ $observasi->jalan_bagus ? '' : 'text-strike' }}">Kondisi jalan baik</span>
                    </li>
                    <li class="indicator-item">
                        <span class="checkbox-box {{ $observasi->mudah_ditemukan ? 'checkbox-checked' : 'checkbox-unchecked' }}">
                            {!! $observasi->mudah_ditemukan ? '✓' : '✗' !!}
                        </span>
                        <span class="{{ $observasi->mudah_ditemukan ? '' : 'text-strike' }}">Mudah ditemukan oleh petunjuk</span>
                    </li>
                    <li class="indicator-item">
                        <span class="checkbox-box {{ $observasi->mudah_dijangkau ? 'checkbox-checked' : 'checkbox-unchecked' }}">
                            {!! $observasi->mudah_dijangkau ? '✓' : '✗' !!}
                        </span>
                        <span class="{{ $observasi->mudah_dijangkau ? '' : 'text-strike' }}">Mudah dijangkau oleh pelanggan</span>
                    </li>
                </ul>
            </td>
        </tr>
    </table>

    <!-- 7. Penilaian Kriteria (Dynamic Table) -->
    @if(isset($kriterias) && $kriterias->count() > 0)
        <div class="section-title">Penilaian Kriteria</div>
        @php
            $nilaiMap = [];
            if ($observasi->penilaians && $observasi->penilaians->first()) {
                foreach ($observasi->penilaians->first()->detailPenilaians as $dp) {
                    $nilaiMap[$dp->kriteria_id] = $dp->nilai;
                }
            }
        @endphp
        <table class="grid-data" style="margin-bottom: 6px;">
            <thead>
                <tr>
                    <th style="width: 70%;">Nama Kriteria</th>
                    <th style="width: 30%; text-align: right;">Nilai</th>
                </tr>
            </thead>
            <tbody>
                @foreach($kriterias as $kriteria)
                    @php
                        $val = $nilaiMap[$kriteria->kriteria_id] ?? null;
                        $isBiayaSewa = ($kriteria->kunci_observasi === 'biaya_sewa') || str_contains(strtolower($kriteria->nama_kriteria), 'sewa');
                    @endphp
                    <tr>
                        <td style="font-weight: bold;">
                            <span style="color: #047857;">[{{ $kriteria->kode_kriteria }}]</span> {{ $kriteria->nama_kriteria }}
                        </td>
                        <td style="text-align: right; font-weight: bold;">
                            @if($val !== null)
                                @if($isBiayaSewa && is_numeric($val))
                                    Rp {{ number_format((float)$val, 0, ',', '.') }}
                                @else
                                    {{ is_numeric($val) ? rtrim(rtrim(number_format((float)$val, 4, '.', ''), '0'), '.') : $val }}
                                @endif
                            @else
                                <span style="color: #9CA3AF; font-style: italic;">-</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <!-- 8. Catatan Tambahan -->
    <div class="section-title">Catatan Tambahan</div>
    <div style="background-color: #F9FAFB; border: 1px solid #E5E7EB; padding: 6px 8px; border-radius: 4px; font-style: italic; min-height: 25px; font-size: 9px; color: #4B5563;">
        {{ $observasi->catatan ?: 'Tidak ada catatan tambahan.' }}
    </div>

    <!-- Tanda Tangan Penanggung Jawab -->
    <table style="width: 100%; margin-top: 15px; border-collapse: collapse; page-break-inside: avoid;">
        <tr>
            <td style="width: 60%;"></td>
            <td style="width: 40%; text-align: center;">
                <div style="font-size: 9.5px; color: #374151; font-weight: bold; margin-bottom: 45px;">
                    Penanggung Jawab (Observer)
                </div>
                <div style="font-size: 9.5px; color: #111827; font-weight: bold;">
                    {{ $observasi->user->name ?? '-' }}
                </div>
            </td>
        </tr>
    </table>

    <!-- 9. Halaman Dokumentasi Lokasi -->
    @php
        $photos = $observasi->dokumentasiLokasis ? $observasi->dokumentasiLokasis : collect();
    @endphp

    @if($photos->count() > 0)
        <div style="page-break-before: always;">
            <div class="header" style="margin-bottom: 10px;">
                <h1>Dokumentasi Observasi Lokasi</h1>
                <p>{{ $observasi->nama_pemilik }} - Kec. {{ $observasi->kecamatan }}, {{ $observasi->kabupaten_kota }}</p>
            </div>

            <div class="section-title" style="margin-top: 0; margin-bottom: 10px;">Dokumentasi Foto Lokasi ({{ $photos->count() }})</div>

            <table style="width: 100%; border-collapse: separate; border-spacing: 10px;">
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
                            <td style="width: 50%; text-align: center; vertical-align: middle; height: 240px; padding: 6px; border: 1px solid #E5E7EB; border-radius: 6px; background-color: #FAFAFA;">
                                @if($fullPath)
                                    <img src="{{ $fullPath }}" style="max-width: 100%; max-height: 228px; width: auto; height: auto; display: block; margin: 0 auto; object-fit: contain; border-radius: 4px;">
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
        Dicetak otomatis oleh Sistem Pendukung Keputusan Pemilihan Lokasi pada {{ now()->translatedFormat('d F Y H:i') }} WIB
    </div>

</body>
</html>
