<?php

namespace App\SharedKernel\Infrastructure\S2\Models;

final class Recepcao extends BaseS2Model
{
    public $incrementing    = true;
    public $timestamps      = false;

    protected $table        = 'recepcao';
    protected $primaryKey   = 'id_recepcao';
    protected $keyType      = 'int';
    protected $guarded      = [];
    protected $casts        = [
        'id_recepcao'                       => 'int',
        'id_clinica'                        => 'int',
        'id_senha'                          => 'int',
        'senha'                             => 'int',
        'id_usuario'                        => 'int',
        'id_sala'                           => 'int',
        'id_paciente'                       => 'int',

        'stamp_inicio'                      => 'datetime',
        'stamp_fim'                         => 'datetime',
        'stamp_modified'                    => 'datetime',

        'tempo_sec'                         => 'int',
        'id_motivo_cancelamento'            => 'int',
        'id_convenio'                       => 'int',
        'id_matricula'                      => 'int',

        'pac_responsavel_id_parentesco'     => 'int',
        'pac_responsavel_id_motivo'         => 'int',

        // bit(1) no MySQL geralmente vem como "\0"/"\1" ou 0/1 dependendo do driver
        'controle_migracao'                 => 'bool',
    ];

    public function isAtiva(): bool
    {
        return ($this->ativo_sn ?? null) === 'S';
    }

    public function isCancelada(): bool
    {
        return ($this->cancelado_sn ?? null) === 'S';
    }

    public function isCobrada(): bool
    {
        return ($this->cobrado_sn ?? null) === 'S';
    }

    public function itens()
    {
        return $this->hasMany(RecepcaoITem::class, 'id_recepcao', 'id_recepcao');
    }

}
