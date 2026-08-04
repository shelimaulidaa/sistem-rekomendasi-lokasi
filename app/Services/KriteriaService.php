<?php

namespace App\Services;

use App\Models\Kriteria;
use App\Models\Periode;

class KriteriaService
{
    /**
     * Get criteria query builder scoped for a specific period.
     */
    public function getKriteriaByPeriode(?int $periodeId, ?string $search = null)
    {
        return Kriteria::query()
            ->when($periodeId, function ($query, $pId) {
                return $query->where('periode_id', $pId);
            }, function ($query) {
                return $query->whereNull('periode_id');
            })
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('nama_kriteria', 'like', "%{$search}%")
                      ->orWhere('kode_kriteria', 'like', "%{$search}%");
                });
            })
            ->orderBy('urutan', 'asc');
    }

    /**
     * Check if a period allows CRUD operations on criteria.
     * Rule 1: Periode must exist and be in Draft status.
     * Rule 2: Periode must NOT have any observations.
     */
    public function canManageKriteria(?int $periodeId): bool
    {
        if (!$periodeId) {
            return false;
        }

        $periode = Periode::find($periodeId);
        if (!$periode) {
            return false;
        }

        // Must be Draft status
        if (!$periode->isDraft()) {
            return false;
        }

        // Must NOT have observations
        if ($periode->observasiLokasi()->exists()) {
            return false;
        }

        return true;
    }

    /**
     * Get human-readable explanation why CRUD is disabled for a period.
     */
    public function getRestrictionReason(?int $periodeId): ?string
    {
        if (!$periodeId) {
            return 'Periode belum dipilih atau tidak valid.';
        }

        $periode = Periode::find($periodeId);
        if (!$periode) {
            return 'Periode tidak ditemukan.';
        }

        if (!$periode->isDraft()) {
            return "Kriteria tidak dapat diubah karena periode \"{$periode->nama_periode}\" berstatus '{$periode->status}'. Perubahan kriteria hanya diizinkan pada periode berstatus Draft.";
        }

        if ($periode->observasiLokasi()->exists()) {
            return "Kriteria tidak dapat diubah karena periode \"{$periode->nama_periode}\" sudah memiliki data observasi lokasi.";
        }

        return null;
    }

    /**
     * Get current total bobot for a specific period.
     */
    public function getTotalBobot(?int $periodeId = null, ?int $ignoreKriteriaId = null): float
    {
        $query = Kriteria::query();

        if ($periodeId) {
            $query->where('periode_id', $periodeId);
        } else {
            $query->whereNull('periode_id');
        }

        if ($ignoreKriteriaId) {
            $query->where('kriteria_id', '!=', $ignoreKriteriaId);
        }

        return (float) $query->sum('bobot');
    }

    /**
     * Check if adding/updating a criteria will exceed the 100% total limit for a period.
     */
    public function willExceedMaxBobot(float $newBobot, ?int $periodeId = null, ?int $ignoreKriteriaId = null): bool
    {
        $currentTotal = $this->getTotalBobot($periodeId, $ignoreKriteriaId);
        return ($currentTotal + $newBobot) > 100.01; // Margin for floating point errors
    }

    /**
     * Get remaining available bobot for a specific period.
     */
    public function getRemainingBobot(?int $periodeId = null, ?int $ignoreKriteriaId = null): float
    {
        $currentTotal = $this->getTotalBobot($periodeId, $ignoreKriteriaId);
        return max(0, 100 - $currentTotal);
    }
}
