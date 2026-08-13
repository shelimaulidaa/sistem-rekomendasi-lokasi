<?php

namespace App\Services;

use App\Models\Kriteria;
use App\Models\Periode;

class KriteriaService
{
    /**
     * Mengambil query builder kriteria berdasarkan periode tertentu.
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
     * Memeriksa apakah suatu periode mengizinkan operasi CRUD pada kriteria.
     * Aturan 1: Periode harus ada dan berstatus Draft.
     * Aturan 2: Periode belum memiliki data observasi lokasi.
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

        // Harus berstatus Draft
        if (!$periode->isDraft()) {
            return false;
        }

        // Tidak boleh memiliki data observasi
        if ($periode->observasiLokasi()->exists()) {
            return false;
        }

        return true;
    }

    /**
     * Mengambil alasan yang mudah dipahami mengapa CRUD kriteria dinonaktifkan untuk suatu periode.
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
     * Mengambil total bobot kriteria saat ini untuk periode tertentu.
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
     * Memeriksa apakah penambahan/pembaruan kriteria akan melebihi batas total 100%.
     */
    public function willExceedMaxBobot(float $newBobot, ?int $periodeId = null, ?int $ignoreKriteriaId = null): bool
    {
        $currentTotal = $this->getTotalBobot($periodeId, $ignoreKriteriaId);
        return ($currentTotal + $newBobot) > 100.01; // Toleransi untuk angka desimal float
    }

    /**
     * Mengambil sisa bobot yang masih tersedia untuk periode tertentu.
     */
    public function getRemainingBobot(?int $periodeId = null, ?int $ignoreKriteriaId = null): float
    {
        $currentTotal = $this->getTotalBobot($periodeId, $ignoreKriteriaId);
        return max(0, 100 - $currentTotal);
    }
}
