<?php

namespace App\Modules\Sensrit\Infrastructure\Clients\Http;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use App\Modules\Sensrit\Domain\Contracts\Clients\SensritTicketsClientContract;
use App\Modules\Sensrit\Domain\Contracts\Repositories\SensritTokenRepositoryContract;

class SensritTicketsHttpClient implements SensritTicketsClientContract
{
    private const PATH = '/api/ticket/ticketFindManagement';

    public function __construct(
        private SensritTokenRepositoryContract $tokens
    ) {}

    public function fetchTicketsUpdatedSince(?string $since = null, ?int $limit = null): array
    {
        $payload = $this->buildPayload($since, $limit);

        $response = $this->http()
            ->post(self::PATH, $payload);

        // Se der 4xx/5xx, joga exception com contexto
        $response->throw();

        $data = $response->json();

        // Aqui a API pode retornar:
        // - array direto
        // - ou objeto { data: [...] }
        // - ou { tickets: [...] }
        // Vamos aceitar os formatos comuns.
        if (is_array($data)) {
            // array de tickets direto
            if ($this->isList($data)) {
                return $data;
            }

            // wrapper com array dentro
            foreach (['data', 'tickets', 'result', 'items'] as $key) {
                $maybe = $data[$key] ?? null;
                if (is_array($maybe) && $this->isList($maybe)) {
                    return $maybe;
                }
            }
        }

        // Se chegou aqui, formato inesperado: devolve vazio pra não quebrar o sync,
        // mas você pode trocar para exception se preferir fail-fast.
        return [];
    }

    private function http(): PendingRequest
    {
        $baseUrl    = config('services.sensrit.base_url', 'https://drconsulta.sensrit.com.br');
        $token      = $this->tokens->getToken();

        if (!$token) {
            throw new \RuntimeException('SENSRIT_TOKEN não configurado.');
        }

        $timeout = (int) config('services.sensrit.timeout', 20);

        return Http::baseUrl($baseUrl)
            ->withHeaders([
                'x-access-token' => $token,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])
            ->retry(3, 300, function ($exception) {
                // Retenta em timeouts/erros transitórios
                return $exception instanceof \Illuminate\Http\Client\ConnectionException;
            })
            ->timeout($timeout);
    }

    /**
     * Monta payload do POST. Para primeira ingestão, sem filtros.
     * Depois você coloca filtros aqui.
     */
    private function buildPayload(?string $since, ?int $limit): array
    {
        $payload = [];

        // Primeira ingestão: sem filtros.
        // Futuro: caso a API aceite filtro por dt_up ou paginação, você adiciona aqui.
        //
        // Exemplos (NÃO habilitados agora):
        // if ($since) $payload['updated_since'] = $since;
        // if ($limit) $payload['limit'] = $limit;

        return $payload;
    }

    private function isList(array $arr): bool
    {
        // PHP 8.1+: array_is_list existe, mas vamos manter compatível
        $i = 0;
        foreach ($arr as $k => $v) {
            if ($k !== $i++) return false;
        }
        return true;
    }
}
