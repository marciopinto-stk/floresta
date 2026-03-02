<?php

namespace App\Modules\Medicos\Application\DTO;

final readonly class PersistRowResultDTO
{
    public function __construct(
        public string $status,
        public ?string $message = null,
    ) {}

    public static function inserted(): self
    {
        return new self('inserted');
    }

    public static function skippedDuplicate(): self
    {
        return new self('skipped_duplicate');
    }

    public static function failed(string $message): self
    {
        return new self('failed', $message);
    }
}
