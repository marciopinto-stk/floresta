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
        string $runId,
    ): string {
        $now        = Carbon::now('UTC')->toIso8601String();
        $existing   = MongoTicketRaw::query()->where('ticket_id', (int) $ticketId)->first();

        $seenSyncPatch = [
            'sync.source'               => $source,
            'sync.last_seen_at'         => $now,
            'sync.last_seen_run'        => $runId,
            'sync.in_current_list'      => true,
            'sync.removed_from_list_at' => null,
        ];

        if (!$existing) {
            MongoTicketRaw::query()->create([
                'ticket_id' => $ticketId,
                'payload'   => $payload,
                'extracted' => $extracted,
                'sync' => [
                    'source'                => $source,
                    'version'               => 1,
                    'payload_hash'          => $payloadHash,
                    'first_seen_at'         => $now,
                    'last_seen_at'          => $now,
                    'last_seen_run'         => $runId,
                    'in_current_list'       => true,
                    'removed_from_list_at'  => null,
                ],
            ]);

            return 'created';
        }

        $existingArray  = $existing->toArray();
        $existingHash   = data_get($existing->toArray(), 'sync.payload_hash');
        $version        = (int) (data_get($existing->toArray(), 'sync.version') ?? 1);
        $firstSeen      = data_get($existingArray, 'sync.first_seen_at') ?? $now;

        if ($existingHash !== $payloadHash) {
            // Mudou algo => incrementa versão e atualiza payload/extracted
            $existing->update([
                'payload'   => $payload,
                'extracted' => $extracted,
                'sync' => [
                    'source'                => $source,
                    'version'               => $version + 1,
                    'payload_hash'          => $payloadHash,
                    'first_seen_at'         => data_get($existing->toArray(), 'sync.first_seen_at') ?? $now,
                    'last_seen_at'          => $now,
                    'last_seen_run'         => $runId,
                    'in_current_list'       => true,
                    'removed_from_list_at'  => null,
                ],
            ]);

            return 'updated';
        }

        // Não mudou => só atualiza last_seen
        $sync = $existing->toArray()['sync'] ?? [];
        $sinc[ 'source']                = $source;
        $sync['last_seen_at']           = $now;
        $sync['last_seen_run']          = $runId;
        $sync['in_current_list']        = true;
        $sync['removed_from_list_at']   = null;

        $existing->update([
            'sync' => $sync,
        ]);

        return 'ignored';
    }

    public function markTicketsNotSeenInRunAsOutOfList(string $runId): int
    {
        $now = Carbon::now('UTC')->toIso8601String();

        $result = MongoTicketRaw::raw(fn($c) => $c->updateMany(
            [
                '$or' => [
                    ['sync.last_seen_run' => ['$ne' => $runId]],
                    ['sync.last_seen_run' => ['$exists' => false]],
                ],
                'sync.in_current_list' => true,
            ],
            [
                '$set' => [
                    'sync.in_current_list' => false,
                    'sync.removed_from_list_at' => $now,
                ],
            ]
        ));

        return (int) ($result->getModifiedCount() ?? 0);
    }
}
