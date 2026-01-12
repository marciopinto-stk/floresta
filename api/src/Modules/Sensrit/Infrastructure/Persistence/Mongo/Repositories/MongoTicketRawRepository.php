<?php

namespace App\Modules\Sensrit\Infrastructure\Persistence\Mongo\Repositories;

use App\Modules\Sensrit\Domain\Contracts\Repositories\TicketRawRepositoryContract;
use App\Modules\Sensrit\Infrastructure\Persistence\Mongo\Models\MongoTicketRaw;
use Carbon\Carbon;

class MongoTicketRawRepository implements TicketRawRepositoryContract
{
    public function upsertRawTicket(
        int $ticketId,
        array $payload,
        array $extracted,
        string $payloadHash,
        string $source,
    ): string {
        $now        = Carbon::now('UTC')->toIso8601String();
   
        /** @var MongoTicketRaw|null $existing */
        //$existing = MongoTicketRaw::query()->find($ticketId);
        $existing = MongoTicketRaw::query()->where('ticket_id', (int) $ticketId)->first();

        if (!$existing) {
            MongoTicketRaw::query()->create([
                'ticket_id' => $ticketId,
                'payload'   => $payload,
                'extracted' => $extracted,
                'sync' => [
                    'source'        => $source,
                    'version'       => 1,
                    'payload_hash'  => $payloadHash,
                    'first_seen_at' => $now,
                    'last_seen_at'  => $now,
                ],
            ]);

            return 'created';
        }

        $existingHash   = data_get($existing->toArray(), 'sync.payload_hash');
        $version        = (int) (data_get($existing->toArray(), 'sync.version') ?? 1);

        if ($existingHash !== $payloadHash) {
            // Mudou algo => incrementa versão e atualiza payload/extracted
            $existing->update([
                'payload'   => $payload,
                'extracted' => $extracted,
                'sync' => [
                    'source'        => $source,
                    'version'       => $version + 1,
                    'payload_hash'  => $payloadHash,
                    'first_seen_at' => data_get($existing->toArray(), 'sync.first_seen_at') ?? $now,
                    'last_seen_at'  => $now,
                ],
            ]);

            return 'updated';
        }

        // Não mudou => só atualiza last_seen
        $sync = $existing->toArray()['sync'] ?? [];
        $sync['last_seen_at'] = $now;

        $existing->update([
            'sync' => $sync,
        ]);

        return 'ignored';
    }
}
