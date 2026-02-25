<?php

namespace App\Modules\Medicos\DOmain\Structures;

final class ExceptionMatchResult
{
    public function __construct(
        public readonly bool $marched,
        public readonly ?string $reason = null,
    ) {}

    public static function no(): self
    {
        return new self(false, null);
    }

    public static function yes(string $reason): self
    {
        return new self(true, $reason);
    }
}
