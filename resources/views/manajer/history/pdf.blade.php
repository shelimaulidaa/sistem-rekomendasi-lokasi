<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Hasil Rekomendasi TOPSIS</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            line-height: 1.5;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #22c55e;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            color: #166534;
            font-size: 24px;
            text-transform: uppercase;
        }
        .header p {
            margin: 5px 0 0;
            color: #666;
            font-size: 14px;
        }
        .info-section {
            margin-bottom: 20px;
            background-color: #f8fafc;
            padding: 15px;
            border-left: 4px solid #3b82f6;
            font-size: 13px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 13px;
        }
        th, td {
            border: 1px solid #e2e8f0;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f1f5f9;
            color: #475569;
            font-weight: bold;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .rank-1 {
            background-color: #f0fdf4;
            font-weight: bold;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-green {
            background-color: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }
        .badge-blue {
            background-color: #dbeafe;
            color: #1e40af;
            border: 1px solid #bfdbfe;
        }
        .badge-gray {
            background-color: #f1f5f9;
            color: #334155;
            border: 1px solid #e2e8f0;
        }
        .footer {
            margin-top: 30px;
            font-size: 12px;
            color: #94a3b8;
            text-align: center;
            border-top: 1px solid #e2e8f0;
            padding-top: 10px;
        }
        
        @media print {
            body {
                padding: 0;
            }
            .header {
                border-bottom: 2px solid #000;
            }
            .header h1 {
                color: #000;
            }
            .info-section {
                border-left: 4px solid #000;
            }
            .rank-1 {
                background-color: #f0f0f0 !important;
                -webkit-print-color-adjust: exact;
            }
            .badge-green, .badge-blue, .badge-gray {
                border: 1px solid #000;
                background-color: transparent !important;
                color: #000 !important;
            }
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>Saung Aqiqah</h1>
        <p>Laporan Keputusan Pemilihan Lokasi Cabang Baru (Metode TOPSIS)</p>
    </div>

    <div class="info-section">
        <table style="border: none; margin: 0;">
            <tr>
                <td style="border: none; padding: 2px; width: 120px;"><strong>Tanggal Cetak</strong></td>
                <td style="border: none; padding: 2px;">: {{ $timestamp }}</td>
            </tr>
            <tr>
                <td style="border: none; padding: 2px;"><strong>Total Alternatif</strong></td>
                <td style="border: none; padding: 2px;">: {{ $hasil->count() }} Lokasi</td>
            </tr>
            <tr>
                <td style="border: none; padding: 2px;"><strong>Catatan</strong></td>
                <td style="border: none; padding: 2px;">: Laporan ini diurutkan berdasarkan nilai preferensi tertinggi (V). Lokasi Peringkat 1 sangat direkomendasikan untuk dipilih.</td>
            </tr>
        </table>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 60px;">Peringkat</th>
                <th>Nama Pemilik</th>
                <th>Alamat Lengkap</th>
                <th class="text-center">Nilai Preferensi</th>
                <th class="text-center">Status Rekomendasi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($hasil as $item)
                @php
                    $isTop1 = $item->ranking === 1;
                    $isTop3 = $item->ranking > 1 && $item->ranking <= 3;
                @endphp
                <tr class="{{ $isTop1 ? 'rank-1' : '' }}">
                    <td class="text-center">{{ $item->ranking }}</td>
                    <td>{{ $item->penilaian->observasiLokasi->nama_pemilik }}</td>
                    <td>{{ $item->penilaian->observasiLokasi->alamat_lengkap }}</td>
                    <td class="text-center" style="font-family: monospace;">{{ number_format($item->nilai_preferensi, 4) }}</td>
                    <td class="text-center">
                        @if($isTop1)
                            <span class="badge badge-green">Sangat Direkomendasikan</span>
                        @elseif($isTop3)
                            <span class="badge badge-blue">Direkomendasikan</span>
                        @else
                            <span class="badge badge-gray">Dipertimbangkan</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">Belum ada data perhitungan hasil.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Dokumen ini di-generate secara otomatis oleh Sistem Pendukung Keputusan Saung Aqiqah.</p>
    </div>

</body>
</html>
