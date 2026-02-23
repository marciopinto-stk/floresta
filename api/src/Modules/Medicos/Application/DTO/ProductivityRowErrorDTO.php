<?php

namespace App\Modules\Medicos\Application\DTO;

final class ProductivityRowErrorDTO
{
    public function __construct(
        public readonly int $line,
        public readonly string $message,
        public readonly array $context = [],
        public readonly array $raw = [],
    ) {}
}
