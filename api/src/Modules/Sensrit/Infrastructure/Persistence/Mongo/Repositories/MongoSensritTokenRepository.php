<?php

namespace App\Modules\Sensrit\Infrastructure\Persistence\Mongo\Repositories;

use App\Modules\Sensrit\Domain\Contracts\Repositories\SensritTokenRepositoryContract;
use App\Modules\Sensrit\Infrastructure\Persistence\Mongo\Models\MongoSensritSetting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class MongoSensritTokenRepository implements SensritTokenRepositoryContract
{
    private const KEY = 'sensrit:token';

    private function collection()
    {
        return DB::connection('mongodb')
            ->getMongoDB()
            ->selectCollection('sensrit_settings');
    }

    public function getToken(): ?string
    {
        $doc = $this->collection()->findOne(['_id' => self::KEY]);

        if (!$doc || empty($doc['token_enc'])) {
            return null;
        }

        try {
            return Crypt::decryptString((string) $doc['token_enc']);
        } catch (\Throwable) {
            return null;
        }
    }

    public function setToken(string $token): void
    {
        $now = Carbon::now('UTC')->toIso8601String();

        MongoSensritSetting::raw(fn($c) => $c->updateOne(
            ['_id' => self::KEY],
            [
                '$set' => [
                    'token_enc'      => Crypt::encryptString($token),
                    'updated_at_iso' => $now,
                ],
                '$setOnInsert'  => [
                    '_id'       => self::KEY,
                ],
            ],
            ['upsert' => true]
        ));
    }
}
