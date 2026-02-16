<?php

namespace App\Modules\Medicos\Domain\ValueObjects;

final class ReferenceMonthVO
{
    private function __construct(
        private readonly string $value
    ) {}

    public static function fromString(?string $raw): self
    {
        
    }
}
