<?php

namespace App\SharedKernel\Infrastructure\S2\Models;

final class Produto extends BaseS2Model
{
    protected $table        = 'produtos';
    protected $primaryKey   = 'id_produto';

    public $incrementing    = false;
    protected $keyType      = 'int';

    public $timestamps = false;

    protected $guarded = [];

    protected $casts = [
        'id_produto'        => 'int',
        'id_produto_pai'    => 'int',

        'ws_agend'          => 'bool',
        'stamp_created'     => 'datetime',
        'stamp_modified'    => 'datetime',

        'id_usuario_modified'   => 'int',
        'retorno'               => 'int',

        'qtde_pulos'    => 'int',
        'atd_idade_max' => 'int',
        'atd_idade_min' => 'int',

        'limite_agenda'     => 'decimal:2',
        'limite_desfecho'   => 'int',
        'prazo_entrega'     => 'int',

        'pagamento_head_peso'   => 'int',
        'max_agendas'           => 'int',

        'controle_migracao' => 'bool',

        'id_prod_aderencia' => 'int',
        'id_produto_laudo'  => 'int',

        'dias_prazo_agendamento'    => 'int',
        'produto_telemed'           => 'int',
    ];

    public function isAtivo(): bool
    {
        return ($this->ativo_sn ?? null) === 'S';
    }
}
