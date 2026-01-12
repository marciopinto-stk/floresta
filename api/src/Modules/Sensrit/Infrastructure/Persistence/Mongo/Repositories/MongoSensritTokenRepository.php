<?php

namespace App\Modules\Sensrit\Infrastructure\Persistence\Mongo\Repositories;

use App\Modules\Sensrit\Domain\Contracts\Repositories\SensritTokenRepositoryContract;
use App\Modules\Sensrit\Infrastructure\Persistence\Mongo\Models\MongoSensritSetting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;

class MongoSensritTokenRepository implements SensritTokenRepositoryContract
{
    private const KEY = 'sensrit:token';

    public function getToken(): ?string
    {
        $doc = MongoSensritSetting::query()->find(self::KEY);
        if (!$doc) {
            return null;
        }

        $enc = $doc->token_enc ?? null;
        if (!$enc) {
            return null;
        }

        try {
            return Crypt::decryptString($enc);
        } catch (\Throwable) {
            return null;
        }
    }

    public function setToken(string $token): void
    {
        $now = Carbon::now('UTC')->toIso8601String();

        MongoSensritSetting::query()->updateOrCreate(
            ['_id' => self::KEY],
            [
                'token_enc' => Crypt::encryptString($token),
                'updated_at_iso' => $now,
            ]
        );
    }
}
