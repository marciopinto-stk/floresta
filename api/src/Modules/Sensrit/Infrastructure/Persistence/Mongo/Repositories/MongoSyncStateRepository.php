<?php

namespace App\Modules\Sensrit\Infrastructure\Persistence\Mongo\Repositories;

use App\Modules\Sensrit\Domain\Contracts\Repositories\SyncStateRepositoryContract;
use App\Modules\Sensrit\Infrastructure\Persistence\Mongo\Models\MongoSyncState;
use Carbon\Carbon;

class MongoSyncStateRepository implements SyncStateRepositoryContract
{
    public function getCursor(string $key): ?string
    {
        $doc = MongoSyncState::query()->find($key);
        if (!$doc) {
            return null;
        }

        return $doc->cursor ?? null;
    }

    public function setCursor(string $key, string $cursor): void
    {
        $now = Carbon::now('UTC')->toIso8601String();

        MongoSyncState::query()->updateOrCreate(
            ['_id' => $key],
            [
                'cursor'            => $cursor,
                'updated_at_iso'    => $now,
            ]
        );
    }
}
