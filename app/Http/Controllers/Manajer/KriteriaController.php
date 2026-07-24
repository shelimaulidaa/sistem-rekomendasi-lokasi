<?php

namespace App\Http\Controllers\Manajer;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreKriteriaRequest;
use App\Http\Requests\UpdateKriteriaRequest;
use App\Models\Kriteria;
use App\Services\KriteriaService;
use Illuminate\Http\Request;


class KriteriaController extends Controller
{
    protected $kriteriaService;

    public function __construct(KriteriaService $kriteriaService)
    {
        $this->kriteriaService = $kriteriaService;
    }

    public function index(Request $request)
    {
        $search = $request->input('search');

        $kriterias = Kriteria::when($search, function ($query, $search) {
            return $query->where('nama_kriteria', 'like', "%{$search}%")
                ->orWhere('kode_kriteria', 'like', "%{$search}%");
        })
            ->orderBy('urutan', 'asc')
            ->paginate(10)
            ->withQueryString();

        $totalBobot = $this->kriteriaService->getTotalBobot();

        return view('manajer.kriteria.index', compact('kriterias', 'search', 'totalBobot'));
    }

    // create and store methods removed to lock schema

    public function edit(Kriteria $kriteria)
    {
        $remainingBobot = $this->kriteriaService->getRemainingBobot($kriteria->kriteria_id) + $kriteria->bobot;
        return view('manajer.kriteria.edit', compact('kriteria', 'remainingBobot'));
    }

    public function update(UpdateKriteriaRequest $request, Kriteria $kriteria)
    {
        $kriteria->update($request->validated());
        return redirect()->route('manajer.kriteria.index')->with('success', 'Kriteria berhasil diperbarui.');
    }
}
