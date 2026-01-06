<?php

namespace App\Modules\Financeiro\Interface\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotaRecepcaoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $dto = $this->resource;

        return [
            'id_recepcao_item'      => $dto->idRecepcaoItem,
            'id_recepcao'           => $dto->idRecepcao,
            'id_executante'         => $dto->idExecutante,
            'valor'                 => $dto->valor,
            'id_invoice_oracle'     => $dto->idInvoiceOracle,
            'id_produto'            => $dto->idProduto,
            'descricao'             => $dto->descricao,
            'id_item_erp'           => $dto->idItemErp,
            'id_unidade'            => $dto->idUnidade,
            'cod_servico'           => $dto->codServico,
            'id_solicitante'        => $dto->idSolicitante,
            'id_convenio'           => $dto->idConvenio,
            'sigla'                 => $dto->sigla,
            'id_atendimento'        => $dto->idAtendimento,
            'id_ac_atendimento'     => $dto->idAcAtendimento,
            'grupo_produto'         => $dto->grupoProduto,
            'ordem_venda'           => $dto->ordemVenda,
            'id_paciente'           => $dto->idPaciente,
            'credito_sn'            => $dto->creditoSn,
            'nsu'                   => $dto->nsu,
            'codigo_autorizacao'    => $dto->codigoAutorizacao,
            'adquirente'            => $dto->adquirente,
            'bandeira'              => $dto->bandeira,
            'memolineName'          => $dto->memolineName,
            'atendimento_em_sala'   => $dto->atendimentoEmSala,
            'payment_terms'         => $dto->paymentTerms,
            'sigla_unidade'         => $dto->siglaUnidade,
        ];
    }
}
