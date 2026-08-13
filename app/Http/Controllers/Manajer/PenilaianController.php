<?php

namespace App\Http\Controllers\Manajer;

use App\Http\Controllers\Controller;
use App\Models\Kriteria;
use App\Models\Penilaian;
use Illuminate\Http\Request;

class PenilaianController extends Controller
{
    public function index(Request $request)
    {
        // Ambil periode untuk dropdown filter
        $periodes = \App\Models\Periode::orderBy('created_at', 'desc')->get();
        
        $activePeriodeId = $request->query('periode_id');
        if (!$activePeriodeId && $periodes->isNotEmpty()) {
            $activePeriodeId = $periodes->firstWhere('status', \App\Models\Periode::STATUS_AKTIF)?->id ?? $periodes->first()->id;
        }

        // Ambil semua kriteria aktif untuk periode terpilih, diurutkan berdasarkan 'urutan'
        $kriterias = Kriteria::query()
            ->when($activePeriodeId, fn($q) => $q->where('periode_id', $activePeriodeId), fn($q) => $q->whereNull('periode_id'))
            ->orderBy('urutan')
            ->get();

        // Ambil semua lokasi yang memiliki data Penilaian pada periode terpilih
        $penilaiansQuery = Penilaian::with(['observasiLokasi', 'detailPenilaians.kriteria'])
            ->whereHas('observasiLokasi', function ($q) use ($activePeriodeId) {
                if ($activePeriodeId) {
                    $q->where('periode_id', $activePeriodeId);
                }
            });

        $penilaians = $penilaiansQuery->get();

        $totalKriteria = $kriterias->count();
        $isComplete = true;

        // Buat struktur data matriks keputusan
        $matrix = [];
        foreach ($penilaians as $penilaian) {
            $row = [
                'penilaian_id' => $penilaian->penilaian_id,
                'nama_pemilik' => $penilaian->observasiLokasi->nama_pemilik,
                'details' => []
            ];

            // Inisialisasi semua slot kriteria dengan nilai null
            foreach ($kriterias as $k) {
                $row['details'][$k->kriteria_id] = null;
            }

            // Isi dengan nilai detail penilaian yang sebenarnya
            $detailsCount = 0;
            foreach ($penilaian->detailPenilaians as $detail) {
                $row['details'][$detail->kriteria_id] = $detail->nilai;
                $detailsCount++;
            }

            // Periksa kelengkapan untuk baris ini
            if ($detailsCount < $totalKriteria) {
                $isComplete = false;
            }

            $matrix[] = $row;
        }

        // Jika tidak ada data penilaian sama sekali, matriks dianggap belum lengkap
        if ($penilaians->isEmpty() || $totalKriteria === 0) {
            $isComplete = false;
        }

        return view('manajer.penilaian.index', compact('kriterias', 'matrix', 'isComplete', 'periodes', 'activePeriodeId'));
    }

    public function calculate(Request $request, \App\Services\TopsisService $topsisService)
    {
        $periodeId = $request->input('periode_id');
        
        if (!$periodeId) {
            return redirect()->back()->with('error', 'Silakan pilih periode terlebih dahulu sebelum menghitung.');
        }

        try {
            $topsisService->calculate($periodeId);

            return redirect()->route('manajer.hasil.index', ['periode_id' => $periodeId])
                ->with('success', 'Perhitungan berhasil dilakukan. Silakan buka menu Hasil Observasi untuk melihat rekomendasi lokasi terbaik.')
                ->with('calculation_success', true);
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal melakukan perhitungan: ' . $e->getMessage());
        }
    }
}
