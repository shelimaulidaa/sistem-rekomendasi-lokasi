<?php

namespace App\Http\Controllers\Manajer;

use App\Http\Controllers\Controller;
use App\Models\Batch as Periode;
use Illuminate\Http\Request;

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
        $table = \Illuminate\Support\Facades\Schema::hasTable('periodes') ? 'periodes' : 'batches';
        $col = \Illuminate\Support\Facades\Schema::hasColumn($table, 'nama_periode') ? 'nama_periode' : 'nama_batch';

        $request->validate([
            'nama_periode' => "required|string|max:255|unique:{$table},{$col}",
        ], [
            'nama_periode.required' => 'Nama periode wajib diisi.',
            'nama_periode.unique' => 'Nama periode sudah digunakan.',
        ]);

        Periode::create([
            'nama_batch' => $request->nama_periode,
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
        $table = \Illuminate\Support\Facades\Schema::hasTable('periodes') ? 'periodes' : 'batches';
        $col = \Illuminate\Support\Facades\Schema::hasColumn($table, 'nama_periode') ? 'nama_periode' : 'nama_batch';

        $request->validate([
            'nama_periode' => "required|string|max:255|unique:{$table},{$col}," . $periode->id,
            'status' => 'required|string|in:Draft,Aktif,Selesai,Diarsipkan',

        ], [
            'nama_periode.required' => 'Nama periode wajib diisi.',
            'nama_periode.unique' => 'Nama periode sudah digunakan.',
            'status.required' => 'Status periode wajib dipilih.',
            'status.in' => 'Status periode tidak valid.',
        ]);


        $periode->update([
            'nama_batch' => $request->nama_periode,
            'status' => $request->status,
        ]);

        return redirect()->route('manajer.periode.index')->with('success', 'Periode berhasil diperbarui.');
    }

    public function destroy(Periode $periode)
    {
        // Check if period has observation locations
        if ($periode->observasiLokasis()->exists()) {
            return redirect()->route('manajer.periode.index')
                ->with('error', 'Periode tidak dapat dihapus karena sudah memiliki data observasi lokasi.');
        }

        $periodeName = $periode->nama_batch;
        $periode->delete();

        return redirect()->route('manajer.periode.index')
            ->with('success', "Periode {$periodeName} berhasil dihapus.");
    }
}
