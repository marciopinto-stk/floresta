<?php

namespace App\Modules\Sensrit\Application\UseCases\SyncTicketsRaw;

use App\Modules\Sensrit\Domain\Contracts\Clients\SensritTicketsClientContract;
use App\Modules\Sensrit\Domain\Contracts\Repositories\SyncStateRepositoryContract;
use App\Modules\Sensrit\Domain\Contracts\Repositories\TicketRawRepositoryContract;
use App\Modules\Sensrit\Domain\Services\TicketRawHasher;
use App\Modules\Sensrit\Domain\Services\TicketRawMapper;
use Carbon\Carbon;
use Illuminate\Support\Str;

class SyncTicketsRawUseCase
{
    public function __construct(
        private SensritTicketsClientContract $client,
        private TicketRawRepositoryContract $tickets,
        private SyncStateRepositoryContract $syncState,
        private TicketRawMapper $mapper,
        private TicketRawHasher $hasher,
    ) {}

    public function execute(SyncTicketsRawInput $input): SyncTicketsRawOutput
    {
        $runId  = (string) Str::uuid();
        $now    = Carbon::now('UTC')->toIso8601String();
        // Determina "since": CLI > estado salvo > fallback
        $since = $input->since ?: $this->syncState->getCursor('sensrit:tickets') ?: null;

        // Busca no Sensrit
        $items = $this->client->fetchTicketsUpdatedSince($since, $input->limit);

        $created = 0;
        $updated = 0;
        $ignored = 0;

        $maxDtUp = $since;

        foreach ($items as $raw) {
            $ticketId = (int) ($raw['id_tickets'] ?? 0);
            if ($ticketId <= 0) {
                continue;
            }

            $doc = $this->mapper->map($raw);
            $hash = $this->hasher->hash($doc[ 'payload']);

            // track cursor
            $dtUp = $doc['extracted']['updated_at_external'] ?? null;
            if ($dtUp && (!$maxDtUp || strcmp($dtUp, $maxDtUp) > 0)) {
                $maxDtUp = $dtUp;
            }

            if ($input->dryRun) {
                $ignored++;
                continue;
            }

            $result = $this->tickets->upsertRawTicket(
                ticketId: $ticketId,
                payload: $doc['payload'],
                extracted: $doc['extracted'],
                payloadHash: $hash,
                source: 'sensrit',
                runId: $runId,
            );

            // upsert retorna created|updated|ignored
            if ($result === 'created') $created++;
            elseif ($result === 'updated') $updated++;
            else $ignored++;
        }

        if (!$input->dryRun) {
            $removed = $this->tickets->markTicketsNotSeenInRunAsOutOfList($runId);
        }
        
        if (!$input->dryRun && $maxDtUp) {
            $this->syncState->setCursor('sensrit:tickets', $maxDtUp);
        }

        return new SyncTicketsRawOutput(
            created: $created,
            updated: $updated,
            ignored: $ignored,
            cursor: $maxDtUp,
        );
    }
}
