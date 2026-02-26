<?php

namespace App\SharedKernel\Infrastructure\S2\Models;

final class Atendimento extends BaseS2Model
{
    protected $table        = 'atendimentos';
    protected $primaryKey   = 'id_atendimento';

    public $incrementing    = true;
    protected $keyType      = 'int';

    public $timestamps = false;

    protected $guarded = [];

    protected $casts = [
        'id_atendimento'                => 'int',
        'id_clinica'                    => 'int',
        'id_profissional'               => 'int',
        'id_mps'                        => 'int',
        'id_especialidade'              => 'int',
        'id_paciente'                   => 'int',
        'id_agenda'                     => 'int',
        'id_responsavel'                => 'int',
        'id_produto_unidade_parceiro'   => 'int',

        'sqa'       => 'int',
        'dt_agenda' => 'datetime',
        'dt_inicio' => 'datetime',
        'dt_fim'    => 'datetime',

        'id_recepcao'           => 'int',
        'id_produto'            => 'int',
        'id_recepcao_item'      => 'int',
        'id_convenio'           => 'int',
        'semana'                => 'int',
        'id_motivo_recoleta'    => 'int',
        'prazo_dias'            => 'int',
        'id_atendimento_pai'    => 'int',
        'id_escala'             => 'int',
        'id_usuario_pos'        => 'int',
        'id_senha_pos'          => 'int',

        'controle_migracao' => 'bool',
        'liberar_retorno'   => 'bool',
        'stamp_modified'    => 'datetime',
    ];

    public function recepcao()
    {
        return $this->belongsTo(Recepcao::class, 'id_recepcao', 'id_recepcao');
    }

    public function item()
    {
        return $this->belongsTo(RecepcaoItem::class, 'id_recepcao_item', 'id_item');
    }

    public function produto()
    {
        return $this->belongsTo(Produto::class, 'id_produto', 'id_produto');
    }
}
