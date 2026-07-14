<?php

namespace App\Http\Controllers\Manajer;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use Illuminate\Http\Request;

class BatchController extends Controller
{
    public function index()
    {
        $batches = Batch::orderBy('created_at', 'desc')->get();
        return response()->json($batches);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_batch' => 'required|string|max:255|unique:batches,nama_batch',
        ]);

        // Automatically set the new one as active, optionally setting others to inactive
        // Batch::query()->update(['is_active' => false]); // Optional: if only 1 can be active

        $batch = Batch::create([
            'nama_batch' => $request->nama_batch,
            'is_active' => true,
        ]);

        return response()->json([
            'message' => 'Batch berhasil ditambahkan.',
            'batch' => $batch
        ]);
    }

    public function update(Request $request, Batch $batch)
    {
        $request->validate([
            'nama_batch' => 'required|string|max:255|unique:batches,nama_batch,' . $batch->id,
        ]);

        $batch->update([
            'nama_batch' => $request->nama_batch,
        ]);

        return response()->json([
            'message' => 'Batch berhasil diperbarui.',
            'batch' => $batch
        ]);
    }

    public function destroy(Batch $batch)
    {
        if ($batch->observasiLokasis()->count() > 0) {
            return response()->json([
                'message' => 'Tidak dapat menghapus batch karena sudah memiliki observasi lokasi.'
            ], 422);
        }

        $batch->delete();

        return response()->json([
            'message' => 'Batch berhasil dihapus.'
        ]);
    }
}
