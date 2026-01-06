<?php
namespace App\Modules\Financeiro\Infrastructure\Repositories;

use App\Modules\Financeiro\Application\DTO\BuscarItensSemNotaPorRecepcaoInput;
use App\Modules\Financeiro\Application\DTO\NotaRecepcaoDTO;
use Illuminate\Support\Facades\DB;

class InvoiceRepository
{
    public function buscarItensSemNotaPorRecepcao(BuscarItensSemNotaPorRecepcaoInput $input): array
    {
        $select = <<<SQL
            ri.id_item AS id_recepcao_item,
            ri.id_recepcao,
            COALESCE(ri.id_executante, 0) AS id_executante,
            ri.valor,
            ri.id_invoice_oracle,
            ri.id_produto,
            p.produto AS descricao,
            p.id_item_erp,
            u.id_unidade,
            ous.codigo_servico_municipio AS cod_servico,
            COALESCE(ri.id_solicitante, 0) AS id_solicitante,
            ri.id_convenio,
            u.sigla,
            a.id_atendimento,
            COALESCE(ae.id_ac_exame, 0) AS id_ac_atendimento,
            p.grupo AS grupo_produto,
            ri.id_recepcao AS ordem_venda,
            ri.id_paciente,
            0 AS credito_sn,
            '' AS nsu,
            '' AS codigo_autorizacao,
            '' AS adquirente,
            '' AS bandeira,
            'SERVIÇOS PRESTADOS' AS memolineName,
            'S' AS atendimento_em_sala,
            'A Vista' AS payment_terms,
            u.sigla AS sigla_unidade
SQL;

        $rows = DB::table('recepcao_itens AS ri')
            ->selectRaw($select)
            ->join('recepcao as r', function ($join) {
                $join->on('ri.id_recepcao', '=', 'r.id_recepcao')
                    ->where('r.ativo_sn', '=', 'S')
                    ->where('r.cobrado_sn', '=', 'S')
                    ->where('r.cancelado_sn', '<>', 'S');
            })
            ->join('datas as dt', 'dt.data', '=', 'r.data')
            ->leftJoin('atendimentos as a', 'a.id_recepcao_item', '=', 'ri.id_item')
            ->leftJoin('atendimentos_stamps as ast', 'a.id_atendimento', '=', 'ast.id_atendimento')
            ->leftJoin('ac_atendimentos_exames as ae', 'ae.id_recepcao_item', '=', 'ri.id_item')
            ->leftJoin('executantes as e', 'e.id_executante', '=', 'ri.id_executante')
            ->leftJoin('produtos as p', 'p.id_produto', '=', 'ri.id_produto')
            ->leftJoin('unidades as u', 'u.id_unidade', '=', 'ri.id_unidade')
            ->leftJoin('oracle_unidades_servicos as ous', function ($join) {
                $join->whereRaw('(ous.id_unidade = r.id_clinica OR ous.id_unidade_oracle = u.id_unidade_oracle)')
                    ->whereRaw('p.grupo = ous.grupo');
            })
            ->where('ri.ativo_sn', '=', 'S')
            ->where('ri.cancelado_sn', '<>', 'S')
            ->where('ri.recoleta_sn', '!=', 'S')
            ->where('ri.id_recepcao', '=', $input->idRecepcao)
            ->whereNull('ri.id_invoice_oracle')
            ->whereRaw('(ri.valor - ri.valor_desconto) > 0')
            ->where(function ($q) {
                $q->where('ri.id_convenio', '=', 6)
                  ->orWhere('ri.id_convenio', '=', 0)
                  ->orWhereNull('ri.id_convenio');
            })
            ->orderBy('ri.id_recepcao', 'asc')
            ->limit($input->limit)

            ->get();

        return $rows->map(fn ($row) => NotaRecepcaoDTO::fromRow($row))->all();
    }

    public function countNotasByRecepcao(int $idRecepcao): array
    {
        $sql = "
            SELECT id_recepcao, COUNT(*) AS total_notas
            FROM emissao_notas_fiscais
            WHERE id_recepcao = ?
              AND sigla <> 'TABO'
              AND status = 'NAO_EMITIDO'
            GROUP BY id_recepcao
        ";

        return DB::select($sql, [$idRecepcao]);
    }

    public function getNotasFromDBByRecepcao(int $idRecepcao): array
    {
        $sql = "
            SELECT *
            FROM emissao_notas_fiscais
            WHERE sigla <> 'TABO'
              AND status = 'NAO_EMITIDO'
              AND account_number IS NOT NULL
              AND id_recepcao = ?
        ";

        return DB::select($sql, [$idRecepcao]);
    }
}
