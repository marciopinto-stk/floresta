<?php

namespace App\Modules\Financeiro\Infrastructure\Clients;

use Illuminate\Support\Facades\Http;

class AtendimentoExternoClient
{
    public function processarNotas(array $payload): array
    {
        $endpoint   = config('financeiro.atendimento_externo.endpoint');
        $token      = config('financeiro.atendimento_externo.token');
        $timeout    = (int) config('financeiro.atendimento_externo.timeout', 60);

        $response = Http::withToken($token)
            ->timeout($timeout)
            ->acceptJson()
            ->asJson()
            ->post($endpoint, $payload);

        return [
            'ok' => $response->successful(),
            'status' => $response->status(),
            'body' => $response->body()
        ];
    }

    public function enviarNotas(array $payload): array
    {
        $endpoint   = config('financeiro.enviar_invoice_oracle.endpoint');
        $token      = config('financeiro.atendimento_externo.token');
        $timeout    = (int) config('financeiro.atendimento_externo.timeout', 60);

        $response = Http::withToken($token)
            ->timeout($timeout)
            ->acceptJson()
            ->asJson()
            ->post($endpoint, $payload);

        return [
            'ok' => $response->successful(),
            'status' => $response->status(),
            'body' => $response->body()
        ];
    }
}
