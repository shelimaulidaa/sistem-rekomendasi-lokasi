<?php

namespace App\Http\Requests;

use App\Services\KriteriaService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateKriteriaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manage kriteria');
    }

    protected function prepareForValidation(): void
    {
        $kriteria = $this->route('kriteria');
        if (!$this->filled('kode_kriteria') && $kriteria) {
            $kode = is_object($kriteria) ? $kriteria->kode_kriteria : \App\Models\Kriteria::find($kriteria)?->kode_kriteria;
            if ($kode) {
                $this->merge([
                    'kode_kriteria' => $kode,
                ]);
            }
        }
    }

    public function rules(): array
    {
        $kriteria = $this->route('kriteria');
        $kriteriaId = is_object($kriteria) ? $kriteria->kriteria_id : $kriteria;
        $periodeId = $this->input('periode_id', is_object($kriteria) ? $kriteria->periode_id : null);

        return [
            'periode_id' => ['required', 'integer', 'exists:periodes,id'],
            'kode_kriteria' => [
                'required', 'string', 'max:10',
                Rule::unique('kriteria', 'kode_kriteria')
                    ->where('periode_id', $periodeId)
                    ->ignore($kriteriaId, 'kriteria_id')
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
                    ->ignore($kriteriaId, 'kriteria_id')
                    ->whereNull('deleted_at')
            ],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $kriteria = $this->route('kriteria');
            $kriteriaId = is_object($kriteria) ? $kriteria->kriteria_id : $kriteria;
            $periodeId = (int) $this->input('periode_id', is_object($kriteria) ? $kriteria->periode_id : 0);

            $service = app(KriteriaService::class);

            if (!$service->canManageKriteria($periodeId)) {
                $reason = $service->getRestrictionReason($periodeId);
                $validator->errors()->add('periode_id', $reason);
                return;
            }

            $bobot = (float) $this->input('bobot', 0);
            if ($service->willExceedMaxBobot($bobot, $periodeId, $kriteriaId)) {
                $remaining = $service->getRemainingBobot($periodeId, $kriteriaId);
                $validator->errors()->add('bobot', "Total bobot kriteria untuk periode ini tidak boleh melebihi 100%. Sisa bobot yang tersedia adalah {$remaining}%.");
            }
        });
    }
}
