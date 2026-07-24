<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kriteria extends Model
{
    use SoftDeletes;

    protected $table = 'kriteria';
    protected $primaryKey = 'kriteria_id';

    protected $fillable = [
        'kode_kriteria',
        'nama_kriteria',
        'bobot',
        'atribut',
        'jenis_input',
        'kunci_observasi',
        'urutan',
    ];

    protected $casts = [
        'bobot' => 'float',
        'urutan' => 'integer',
    ];

    public function isBenefit(): bool
    {
        return strtolower($this->atribut) === 'benefit';
    }

    public function isCost(): bool
    {
        return strtolower($this->atribut) === 'cost';
    }

    public function detailPenilaians(): HasMany
    {
        return $this->hasMany(DetailPenilaian::class, 'kriteria_id', 'kriteria_id');
    }
}

