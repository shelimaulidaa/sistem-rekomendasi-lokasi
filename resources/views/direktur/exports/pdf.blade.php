<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Rekomendasi Lokasi TOPSIS</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #22c55e;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            color: #166534;
            font-size: 20px;
        }
        .header p {
            margin: 5px 0 0 0;
            color: #666;
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 10px;
            color: #1f2937;
            background-color: #f3f4f6;
            padding: 5px;
            border-left: 4px solid #22c55e;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #d1d5db;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f9fafb;
            font-weight: bold;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .badge {
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
        }
        .badge-sangat { background-color: #dcfce7; color: #166534; }
        .badge-rekom { background-color: #dbeafe; color: #1e3a8a; }
        .badge-timbang { background-color: #f3f4f6; color: #4b5563; }
        .footer {
            margin-top: 50px;
            text-align: right;
            font-size: 11px;
            color: #666;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>LAPORAN HASIL REKOMENDASI LOKASI</h1>
        <p>Sistem Pendukung Keputusan Penentuan Lokasi Cabang Baru Saung Aqiqah</p>
        <p>Metode: TOPSIS (Technique for Order Preference by Similarity to Ideal Solution)</p>
    </div>

    <p><strong>Waktu Kalkulasi:</strong> {{ \Carbon\Carbon::parse($timestamp)->format('d F Y, H:i:s') }}</p>

    <div class="section-title">1. Daftar Kriteria dan Bobot</div>
    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 10%">Kode</th>
                <th>Nama Kriteria</th>
                <th class="text-center" style="width: 20%">Atribut</th>
                <th class="text-center" style="width: 20%">Bobot (W)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($kriterias as $k)
            <tr>
                <td class="text-center">{{ $k->kode_kriteria }}</td>
                <td>{{ $k->nama_kriteria }}</td>
                <td class="text-center">{{ ucfirst($k->atribut) }}</td>
                <td class="text-center">{{ $k->bobot }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="section-title">2. Hasil Pemeringkatan (Ranking)</div>
    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 10%">Rank</th>
                <th>Nama Lokasi Alternatif</th>
                <th class="text-center" style="width: 20%">Nilai Preferensi (V)</th>
                <th class="text-center" style="width: 25%">Status Rekomendasi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($results as $item)
            <tr>
                <td class="text-center font-bold">{{ $item->ranking }}</td>
                <td>{{ $item->penilaian->observasiLokasi->nama_pemilik }}</td>
                <td class="text-center">{{ number_format($item->nilai_preferensi, 4) }}</td>
                <td class="text-center">
                    @if($item->ranking === 1)
                        <span class="badge badge-sangat">Sangat Direkomendasikan</span>
                    @elseif($item->ranking <= 3)
                        <span class="badge badge-rekom">Direkomendasikan</span>
                    @else
                        <span class="badge badge-timbang">Dipertimbangkan</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center">Belum ada data perhitungan.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak oleh Sistem - {{ now()->format('d F Y, H:i:s') }}</p>
    </div>

</body>
</html>
