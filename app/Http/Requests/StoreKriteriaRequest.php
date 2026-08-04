<?php

namespace App\Http\Requests;

use App\Services\KriteriaService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreKriteriaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manage kriteria');
    }

    protected function prepareForValidation(): void
    {
        $periodeId = $this->input('periode_id');

        if (!$this->filled('kode_kriteria')) {
            $existingCount = \App\Models\Kriteria::withTrashed()->where('periode_id', $periodeId)->count();
            $nextNum = $existingCount + 1;
            while (\App\Models\Kriteria::withTrashed()->where('periode_id', $periodeId)->where('kode_kriteria', "C{$nextNum}")->exists()) {
                $nextNum++;
            }
            $this->merge([
                'kode_kriteria' => "C{$nextNum}",
            ]);
        }

        if (!$this->has('urutan') || $this->input('urutan') === null) {
            $maxUrutan = \App\Models\Kriteria::withTrashed()->where('periode_id', $periodeId)->max('urutan') ?? 0;
            $this->merge([
                'urutan' => $maxUrutan + 1,
            ]);
        }
    }

    public function rules(): array
    {
        $periodeId = $this->input('periode_id');

        return [
            'periode_id' => ['required', 'integer', 'exists:periodes,id'],
            'kode_kriteria' => [
                'required', 'string', 'max:10',
                Rule::unique('kriteria', 'kode_kriteria')
                    ->where('periode_id', $periodeId)
                    ->whereNull('deleted_at')
            ],
            'nama_kriteria' => ['required', 'string', 'max:255'],
            'bobot' => ['required', 'numeric', 'min:0', 'max:100'],
            'atribut' => ['required', 'in:benefit,cost'],
            'jenis_input' => ['required', 'in:numeric,scoring'],
            'kunci_observasi' => ['nullable', 'string', 'max:50'],
            'urutan' => [
                'required', 'integer', 'min:1',
                Rule::unique('kriteria', 'urutan')
                    ->where('periode_id', $periodeId)
                    ->whereNull('deleted_at')
            ],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $periodeId = (int) $this->input('periode_id');
            $service = app(KriteriaService::class);

            if (!$service->canManageKriteria($periodeId)) {
                $reason = $service->getRestrictionReason($periodeId);
                $validator->errors()->add('periode_id', $reason);
                return;
            }

            $bobot = (float) $this->input('bobot', 0);
            if ($service->willExceedMaxBobot($bobot, $periodeId)) {
                $remaining = $service->getRemainingBobot($periodeId);
                $validator->errors()->add('bobot', "Total bobot kriteria untuk periode ini tidak boleh melebihi 100%. Sisa bobot yang tersedia adalah {$remaining}%.");
            }
        });
    }
}
