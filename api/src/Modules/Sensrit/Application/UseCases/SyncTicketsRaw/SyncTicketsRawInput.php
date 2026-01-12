<?php

namespace App\Modules\Sensrit\Application\UseCases\SyncTicketsRaw;

class SyncTicketsRawInput
{
    public function __construct(
        public ?string $since   = null,
        public ?int $limit      = null,
        public bool $dryRun     = false,
    ) {}
}
