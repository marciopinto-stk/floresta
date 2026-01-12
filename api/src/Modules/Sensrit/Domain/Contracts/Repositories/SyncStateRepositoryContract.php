<?php

namespace App\Modules\Sensrit\Domain\Contracts\Repositories;

interface SyncStateRepositoryContract
{
    public function getCursor(string $key): ?string;
    public function setCursor(string $key, string $cursor): void;
}
