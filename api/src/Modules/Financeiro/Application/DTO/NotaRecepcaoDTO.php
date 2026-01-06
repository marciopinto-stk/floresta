<?php

namespace App\Modules\Financeiro\Application\DTO;

final class NotaRecepcaoDTO
{
    public function __construct (
        public readonly int $idRecepcaoItem,
        public readonly int $idRecepcao,
        public readonly int $idExecutante,
        public readonly float $valor,
        public readonly ?int $idInvoiceOracle,
        public readonly int $idProduto,
        public readonly ?string $descricao,
        public readonly ?int $idItemErp,
        public readonly int $idUnidade,
        public readonly ?string $codServico,
        public readonly int $idSolicitante,
        public readonly ?int $idConvenio,
        public readonly string $sigla,
        public readonly ?int $idAtendimento,
        public readonly int $idAcAtendimento,
        public readonly string $grupoProduto,
        public readonly int $ordemVenda,
        public readonly int $idPaciente,
        public readonly int $creditoSn,
        public readonly string $nsu,
        public readonly string $codigoAutorizacao,
        public readonly string $adquirente,
        public readonly string $bandeira,
        public readonly string $memolineName,
        public readonly string $atendimentoEmSala,
        public readonly string $paymentTerms,
        public readonly string $siglaUnidade,
    ) {}

    public static function fromRow(object $row): self
    {
        return new self(
            idRecepcaoItem: (int) $row->id_recepcao_item,
            idRecepcao: (int) $row->id_recepcao,
            idExecutante: (int) $row->id_executante,
            valor: (float) $row->valor,
            idInvoiceOracle: $row->id_invoice_oracle !== null ? (int) $row->id_invoice_oracle : null,
            idProduto: (int) $row->id_produto,
            descricao: (string) ($row->descricao ?? ''),
            idItemErp: $row->id_item_erp !== null ? (int) $row->id_item_erp : null,
            idUnidade: (int) $row->id_unidade,
            codServico: (string) ($row->cod_servico ?? ''),
            idSolicitante: (int) $row->id_solicitante,
            idConvenio: $row->id_convenio !== null ? (int) $row->id_convenio : null,
            sigla: (string) ($row->sigla ?? ''),
            idAtendimento: $row->id_atendimento !== null ? (int) $row->id_atendimento : null,
            idAcAtendimento: (int) $row->id_ac_atendimento,
            grupoProduto: (string) ($row->grupo_produto ?? ''),
            ordemVenda: (int) $row->ordem_venda,
            idPaciente: (int) $row->id_paciente,
            creditoSn: (int) $row->credito_sn,
            nsu: (string) $row->nsu,
            codigoAutorizacao: (string) $row->codigo_autorizacao,
            adquirente: (string) $row->adquirente,
            bandeira: (string) $row->bandeira,
            memolineName: (string) $row->memolineName,
            atendimentoEmSala: (string) $row->atendimento_em_sala,
            paymentTerms: (string) $row->payment_terms,
            siglaUnidade: (string) $row->sigla_unidade,
        );
    }

    public function toArray(): array
    {
        return [
            'id_recepcao_item'   => $this->idRecepcaoItem,
            'id_recepcao'        => $this->idRecepcao,
            'id_executante'      => $this->idExecutante,
            'valor'              => $this->valor,
            'id_invoice_oracle'  => $this->idInvoiceOracle,

            'id_produto'         => $this->idProduto,
            'descricao'          => $this->descricao,
            'id_item_erp'        => $this->idItemErp,

            'id_unidade'         => $this->idUnidade,
            'cod_servico'        => $this->codServico,
            'id_solicitante'     => $this->idSolicitante,
            'id_convenio'        => $this->idConvenio,

            'sigla'              => $this->sigla,
            'id_atendimento'     => $this->idAtendimento,
            'id_ac_atendimento'  => $this->idAcAtendimento,

            'grupo_produto'      => $this->grupoProduto,
            'ordem_venda'        => $this->ordemVenda,
            'id_paciente'        => $this->idPaciente,

            'credito_sn'         => $this->creditoSn,
            'nsu'                => $this->nsu,
            'codigo_autorizacao' => $this->codigoAutorizacao,
            'adquirente'         => $this->adquirente,
            'bandeira'           => $this->bandeira,

            'memolineName'       => $this->memolineName,
            'atendimento_em_sala'=> $this->atendimentoEmSala,
            'payment_terms'      => $this->paymentTerms,
            'sigla_unidade'      => $this->siglaUnidade,
        ];
    }
}
