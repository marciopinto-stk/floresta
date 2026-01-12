<?php

namespace App\Modules\Sensrit\Application\UseCases\ValidateSensritToken;

use App\Modules\Sensrit\Domain\Contracts\Repositories\SensritTokenRepositoryContract;
use App\Modules\Sensrit\Domain\Contracts\Clients\SensritTicketsClientContract;

class ValidateSensritTokenUseCase
{
    public function __construct(
        private SensritTokenRepositoryContract $tokens,
        private SensritTicketsClientContract $client,
    ) {}

    public function execute(): ValidateSensritTokenOutput
    {
        $token = $this->tokens->getToken();

        if (!$token) {
            return new ValidateSensritTokenOutput(
                hasToken: false,
                isValid: null,
                statusCode: null,
                message: 'Nenhum token configurado.',
            );
        }

        try {
            // Faz uma chamada leve. Seu client atual faz POST vazio e retorna array.
            // Se token estiver inválido, o client deve lançar exception via ->throw().
            $this->client->fetchTicketsUpdatedSince(null, 1);

            return new ValidateSensritTokenOutput(
                hasToken: true,
                isValid: true,
                statusCode: 200,
                message: 'Token válido.',
            );
        } catch (\Illuminate\Http\Client\RequestException $e) {
            $status = $e->response?->status();

            if (in_array($status, [401, 403], true)) {
                return new ValidateSensritTokenOutput(
                    hasToken: true,
                    isValid: false,
                    statusCode: $status,
                    message: 'Token inválido/expirado.',
                );
            }

            return new ValidateSensritTokenOutput(
                hasToken: true,
                isValid: null,
                statusCode: $status,
                message: 'Não foi possível validar agora (erro HTTP).',
            );
        } catch (\Throwable) {
            return new ValidateSensritTokenOutput(
                hasToken: true,
                isValid: null,
                statusCode: null,
                message: 'Não foi possível validar agora (erro inesperado).',
            );
        }
    }
}
