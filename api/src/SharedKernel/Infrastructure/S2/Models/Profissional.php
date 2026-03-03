<?php

namespace App\SharedKernel\Infrastructure\S2\Models;

use Illuminate\Database\Eloquent\Builder;

final class Profissional extends BaseS2Model
{
    public $incrementing    = true;
    public $timestamps      = false;

    protected $table        = 'profissionais';
    protected $primaryKey   = 'id_profissional';
    protected $keyType      = 'int';

    protected $casts = [
        'dt_nasc'           => 'datetime',
        'dt_inicio'         => 'datetime',
        'dt_validade'       => 'datetime',
        'stamp_modified'    => 'datetime',
        'controle_migracao' => 'boolean',
    ];

    public function scopeAtivo(Builder $q): Builder
    {
        return $q->where('ativo_sn', 'S');
    }

    public function scopeNomeLike(Builder $q, string $term): Builder
    {
        return $q->where('nome', 'like', "%{$term}%");
    }
}
