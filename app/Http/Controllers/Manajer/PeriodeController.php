<?php

namespace App\Http\Controllers\Manajer;

use App\Http\Controllers\Controller;
use App\Models\Periode;
use App\Models\Kriteria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PeriodeController extends Controller
{
    public function index()
    {
        $periodes = Periode::orderBy('created_at', 'desc')->paginate(10);
        return view('manajer.periode.index', compact('periodes'));
    }

    public function create()
    {
        return view('manajer.periode.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_periode' => [
                'required', 'string', 'max:255',
                Rule::unique('periodes', 'nama_periode')->whereNull('deleted_at'),
            ],
        ], [
            'nama_periode.required' => 'Nama periode wajib diisi.',
            'nama_periode.unique' => 'Nama periode sudah digunakan.',
        ]);

        Periode::create([
            'nama_periode' => $request->nama_periode,
            'status' => Periode::STATUS_DRAFT,
        ]);

        return redirect()->route('manajer.periode.index')->with('success', 'Periode berhasil ditambahkan dengan status Draft.');
    }

    public function edit(Periode $periode)
    {
        return view('manajer.periode.edit', compact('periode'));
    }

    public function update(Request $request, Periode $periode)
    {
        $request->validate([
            'nama_periode' => [
                'required', 'string', 'max:255',
                Rule::unique('periodes', 'nama_periode')->ignore($periode->id)->whereNull('deleted_at'),
            ],
            'status' => 'required|string|in:Draft,Aktif,Selesai,Diarsipkan',
        ], [
            'nama_periode.required' => 'Nama periode wajib diisi.',
            'nama_periode.unique' => 'Nama periode sudah digunakan.',
            'status.required' => 'Status periode wajib dipilih.',
            'status.in' => 'Status periode tidak valid.',
        ]);

        if ($periode->status === Periode::STATUS_SELESAI && $request->status === Periode::STATUS_DRAFT) {
            if ($periode->isCalculated()) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['status' => 'Periode yang telah selesai dilakukan perhitungan tidak dapat diubah kembali ke status Draft.'])
                    ->with('error', 'Periode yang telah selesai dilakukan perhitungan tidak dapat diubah kembali ke status Draft.');
            }
        }

        $periode->update([
            'nama_periode' => $request->nama_periode,
            'status' => $request->status,
        ]);

        return redirect()->route('manajer.periode.index')->with('success', 'Periode berhasil diperbarui.');
    }

    public function destroy(Periode $periode)
    {
        // Periksa apakah periode memiliki data observasi lokasi
        if ($periode->observasiLokasis()->exists()) {
            return redirect()->route('manajer.periode.index')
                ->with('error', 'Periode tidak dapat dihapus karena sudah memiliki data observasi lokasi.');
        }

        $periodeName = $periode->nama_periode;

        DB::transaction(function () use ($periode) {
            Kriteria::where('periode_id', $periode->id)->withTrashed()->forceDelete();
            $periode->forceDelete();
        });

        return redirect()->route('manajer.periode.index')
            ->with('success', "Periode {$periodeName} berhasil dihapus.");
    }
}
