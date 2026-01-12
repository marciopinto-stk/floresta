<?php

namespace App\Modules\Sensrit\Domain\Contracts\Repositories;

interface TicketRawRepositoryContract
{
    /**
     * Deve fazer upsert no Mongo e retornar: created|updated|ignored
     */
    public function upsertRawTicket(
        int $ticketId,
        array $payload,
        array $extracted,
        string $payloadHash,
        string $source,
    ): string;
}
