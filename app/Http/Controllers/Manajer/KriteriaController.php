<?php

namespace App\Http\Controllers\Manajer;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreKriteriaRequest;
use App\Http\Requests\UpdateKriteriaRequest;
use App\Models\Kriteria;
use App\Models\Periode;
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
        $periodeId = $request->input('periode_id') ?? $request->input('batch_id');

        $periodes = Periode::orderBy('created_at', 'desc')->get();

        if (!$periodeId) {
            $activeDraft = $periodes->firstWhere('status', Periode::STATUS_DRAFT) ?? $periodes->first();
            $periodeId = $activeDraft?->id;
        }

        $chosenPeriode = $periodeId ? $periodes->firstWhere('id', $periodeId) : null;

        $kriterias = $this->kriteriaService->getKriteriaByPeriode($periodeId, $search)
            ->paginate(10)
            ->withQueryString();

        $totalBobot = $this->kriteriaService->getTotalBobot($periodeId);
        $canManage = $this->kriteriaService->canManageKriteria($periodeId);
        $restrictionReason = $this->kriteriaService->getRestrictionReason($periodeId);

        return view('manajer.kriteria.index', compact(
            'kriterias',
            'search',
            'totalBobot',
            'periodes',
            'periodeId',
            'chosenPeriode',
            'canManage',
            'restrictionReason'
        ));
    }

    public function create(Request $request)
    {
        $periodeId = $request->input('periode_id') ?? $request->input('batch_id');
        if (!$periodeId) {
            $draft = Periode::where('status', Periode::STATUS_DRAFT)->first();
            $periodeId = $draft?->id;
        }

        if (!$this->kriteriaService->canManageKriteria($periodeId)) {
            $reason = $this->kriteriaService->getRestrictionReason($periodeId);
            return redirect()->route('manajer.kriteria.index', ['periode_id' => $periodeId])
                ->with('error', $reason);
        }

        $chosenPeriode = Periode::findOrFail($periodeId);
        $remainingBobot = $this->kriteriaService->getRemainingBobot($periodeId);

        return view('manajer.kriteria.create', compact('periodeId', 'chosenPeriode', 'remainingBobot'));
    }

    public function store(StoreKriteriaRequest $request)
    {
        $periodeId = $request->input('periode_id');

        if (!$this->kriteriaService->canManageKriteria($periodeId)) {
            $reason = $this->kriteriaService->getRestrictionReason($periodeId);
            return redirect()->route('manajer.kriteria.index', ['periode_id' => $periodeId])
                ->with('error', $reason);
        }

        Kriteria::create($request->validated());

        return redirect()->route('manajer.kriteria.index', ['periode_id' => $periodeId])
            ->with('success', 'Kriteria baru berhasil ditambahkan.');
    }

    public function show(Kriteria $kriteria)
    {
        return redirect()->route('manajer.kriteria.edit', $kriteria);
    }

    public function edit(Kriteria $kriteria)
    {
        $periodeId = $kriteria->periode_id;

        if (!$this->kriteriaService->canManageKriteria($periodeId)) {
            $reason = $this->kriteriaService->getRestrictionReason($periodeId);
            return redirect()->route('manajer.kriteria.index', ['periode_id' => $periodeId])
                ->with('error', $reason);
        }

        $remainingBobot = $this->kriteriaService->getRemainingBobot($periodeId, $kriteria->kriteria_id) + $kriteria->bobot;
        return view('manajer.kriteria.edit', compact('kriteria', 'remainingBobot'));
    }

    public function update(UpdateKriteriaRequest $request, Kriteria $kriteria)
    {
        $periodeId = $request->input('periode_id', $kriteria->periode_id);

        if (!$this->kriteriaService->canManageKriteria($periodeId)) {
            $reason = $this->kriteriaService->getRestrictionReason($periodeId);
            return redirect()->route('manajer.kriteria.index', ['periode_id' => $periodeId])
                ->with('error', $reason);
        }

        $kriteria->update($request->validated());

        return redirect()->route('manajer.kriteria.index', ['periode_id' => $periodeId])
            ->with('success', 'Kriteria berhasil diperbarui.');
    }

    public function destroy(Kriteria $kriteria)
    {
        $periodeId = $kriteria->periode_id;

        if (!$this->kriteriaService->canManageKriteria($periodeId)) {
            $reason = $this->kriteriaService->getRestrictionReason($periodeId);
            return redirect()->route('manajer.kriteria.index', ['periode_id' => $periodeId])
                ->with('error', $reason);
        }

        $kriteria->forceDelete();

        return redirect()->route('manajer.kriteria.index', ['periode_id' => $periodeId])
            ->with('success', 'Kriteria berhasil dihapus.');
    }
}
