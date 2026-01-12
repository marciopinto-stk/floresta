<?php

namespace App\Modules\Sensrit\Domain\Contracts\Clients;

interface SensritTicketsClientContract
{
    /**
     * @return array<int, array<string, mixed>> Lista de tickets (arrays) no formato bruto
     */
    public function fetchTicketsUpdatedSince(?string $since = null, ?int $limit = null): array;
}
