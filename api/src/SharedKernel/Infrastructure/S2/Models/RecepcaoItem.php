<?php

namespace App\SharedKernel\Infrastructure\S2\Models;

final class RecepcaoItem extends BaseS2Model
{
    public $incrementing    = true;
    public $timestamps      = false;
    protected $table        = 'recepcao_itens';
    protected $primaryKey   = 'id_item';
    protected $keyType      = 'int';
    protected $guarded      = [];
    protected $casts = [
        'id_item'       => 'int',
        'id_recepcao'   => 'int',
        'ordem'         => 'int',

        'id_produto'                => 'int',
        'id_agenda'                 => 'int',
        'id_especialidade'          => 'int',
        'id_solicitante'            => 'int',
        'id_solicitante_externo'    => 'int',
        'id_executante'             => 'int',
        'id_unidade'                => 'int',
        'id_usuario'                => 'int',
        'id_paciente'               => 'int',

        'valor'             => 'decimal:2',
        'valor_desconto'    => 'decimal:2',

        'stamp_created'     => 'datetime',
        'stamp_modified'    => 'datetime',
        'stamp_previsao'    => 'datetime',

        'id_orcamento_item'     => 'int',
        'id_solicitacao_exames' => 'int',
        'id_tratamento'         => 'int',
        'id_tratamento_item'    => 'int',

        'desconto_id_voucher'   => 'int',
        'desconto_id_campanha'  => 'int',
        'desconto_id_voucher'   => 'int',

        'id_prevenda'           => 'int',
        'id_prevenda_item'      => 'int',
        'id_motivo_desconto'    => 'int',

        'id_convenio_tabpreco'  => 'int',
        'id_convenio'           => 'int',

        'status_credito'    => 'int',
        'id_item_credito'   => 'int',
        'id_motivo_credito' => 'int',

        'id_programa'       => 'int',
        'id_programa_item'  => 'int',

        'id_remessa_tiss'       => 'int',
        'id_convenio_lote_tiss' => 'int',

        'id_procedimento_sala'  => 'int',
        'oracle_sequencial'     => 'int',

        'id_convenio_vpp' => 'int',

        'controle_migracao' => 'bool',
    ];

    public function isAtivo(): bool
    {
        return ($this->ativo_sn ?? null) === 'S';
    }

    public function isCancelado(): bool
    {
        return ($this->cancelado_sn ?? null) === 'S';
    }

    public function isAgendado(): bool
    {
        return ($this->agendado_sn ?? null) === 'S';
    }

    public function recepcao()
    {
        return $this->belogsTo(Recepcao::class, 'id_recepcao', 'id_recepcao');
    }

    public function produto()
    {
        return $this->belongsTo(Produto::class, 'id_produto', 'id_produto');
    }

    public function atendimentos()
    {
        return $this->hasMany(Atendimento::class, 'id_recepcao_item', 'id_item');
    }
}
