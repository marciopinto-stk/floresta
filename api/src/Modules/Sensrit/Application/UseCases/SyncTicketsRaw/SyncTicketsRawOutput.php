<?php

namespace App\Modules\Sensrit\Application\UseCases\SyncTicketsRaw;

class SyncTicketsRawOutput
{
    public function __construct(
        public int $created,
        public int $updated,
        public int $ignored,
        public ?string $cursor = null,
    ) {}
}
