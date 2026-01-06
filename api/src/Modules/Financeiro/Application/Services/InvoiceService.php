<?php

namespace App\Modules\Financeiro\Application\Services;

use App\Modules\Financeiro\Application\DTO\BuscarItensSemNotaPorRecepcaoInput;
use App\Modules\Financeiro\Application\DTO\NotaRecepcaoDTO;
use App\Modules\Financeiro\Application\DTO\ProcessarNotasPorRecepcaoInput;
use App\Modules\Financeiro\Infrastructure\Clients\AtendimentoExternoClient;
use App\Modules\Financeiro\Infrastructure\Repositories\InvoiceRepository;
use Illuminate\Support\Facades\Log;
use Throwable;

class InvoiceService
{
    public function __construct(
        private readonly InvoiceRepository $repository,
        private readonly AtendimentoExternoClient $client
    ) {}

    public function buscarItensSemNotaPorRecepcao(BuscarItensSemNotaPorRecepcaoInput $input): array
    {
        return $this->repository->buscarItensSemNotaPorRecepcao($input);
    }

    public function processarNotasPorRecepcao(ProcessarNotasPorRecepcaoInput $input): array
    {
        $itens = $this->buscarItensSemNotaPorRecepcao(BuscarItensSemNotaPorRecepcaoInput::fromArray(['id_recepcao' => $input->idRecepcao]));

        if (empty($itens)) {
            return ['status' => 'Nenhuma nota para processar.', 'blocos' => 0];
        }

        $payload    = array_map(fn ($dto) => $dto->toArray(), $itens);
        $chunks     = array_chunk($payload, $input->chunkSize);
        $resultados = [];

        foreach($chunks as $index => $chunk) {
            try {
                $resp = $this->client->processarNotas($chunk);

                Log::info('Bloco processado', [
                    'bloco' => $index + 1,
                    'status_http' => $resp['status'],
                ]);

                $resultados[] = [
                    'bloco' => $index + 1,
                    'ok' => $resp['ok'],
                    'status_http' => $resp['status'],
                    'response' => $resp['body'],
                ];
            } catch (\Throwable $e) {
                Log::error('Erro no bloco', [
                    'bloco' => $index + 1,
                    'error' => $e->getMessage(),
                ]);

                $resultados[] = [
                    'bloco' => $index + 1,
                    'ok' => false,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return [
            'status'        => 'Processamento concluído',
            'blocos'        => count($chunks),
            'resultados'    => $resultados,
        ];
    }

    public function emitirNotasPorRecepcao(ProcessarNotasPorRecepcaoInput $input)
    {
        try{
            Log::info('Contando notas por id_recepcao...', ['id_recepcao' => $input->idRecepcao]);
            $notasCount = $this->repository->countNotasByRecepcao($input->idRecepcao);
            Log::info('Ids de recepção encontrados', ['count' => count($notasCount)]);

            if (empty($notasCount)) {
                return [
                    'status' => 'Nenhuma nota para processar.',
                    'blocos' => 0,
                    'itens'  => [],
                ];
            }

            $resultados = [];
            $totalBlocos = 0;

            foreach($notasCount as $row) {
                $idRecepcao = (int) $row->id_recepcao;
                $totalNotas = (int) $row->total_notas;

                Log::info('Processando recepção', [
                    'id_recepcao' => $idRecepcao,
                    'total_notas' => $totalNotas,
                ]);

                $notas = $this->repository->getNotasFromDBByRecepcao($idRecepcao);

                Log::info('Notas encontradas', [
                    'id_recepcao' => $idRecepcao,
                    'count' => count($notas),
                ]);

                if (empty($notas)) {
                    $resultados[] = [
                        'id_recepcao' => $idRecepcao,
                        'blocos'      => 0,
                        'message'     => 'Nenhuma nota elegível encontrada para envio.',
                    ];
                    continue;
                }

                $blocos         = array_chunk($notas, $totalNotas > 0 ? $totalNotas : 1);
                $totalBlocos    += count($blocos);

                foreach ($blocos as $index => $bloco) {
                    try{
                        $resp = $this->client->enviarNotas($bloco);

                        Log::info('Bloco processado', [
                            'id_recepcao' => $idRecepcao,
                            'bloco' => $index + 1,
                            'http_status' => $resp['status'],
                            'ok' => $resp['ok'],
                        ]);

                        $resultados[] = [
                            'id_recepcao' => $idRecepcao,
                            'index'       => $index + 1,
                            'ok'          => $resp['ok'],
                            'status'      => $resp['status'],
                            'response'    => $resp['body'],
                        ];
                    } catch(Throwable $e) {
                        Log::error('Erro enviando bloco', [
                            'id_recepcao' => $idRecepcao,
                            'bloco' => $index + 1,
                            'error' => $e->getMessage(),
                        ]);

                        $resultados[] = [
                            'id_recepcao' => $idRecepcao,
                            'index'       => $index + 1,
                            'ok'          => false,
                            'error'       => $e->getMessage(),
                        ];
                    }
                }
            }

            return [
                'status' => 'Processamento concluído',
                'blocos' => $totalBlocos,
                'itens'  => $resultados,
            ];
        } catch (Throwable $e) {
            Log::error('Erro no processamento', ['error' => $e->getMessage()]);

            return [
                'status' => 'Erro no processamento',
                'error'  => $e->getMessage(),
            ];
        }
    }
}
