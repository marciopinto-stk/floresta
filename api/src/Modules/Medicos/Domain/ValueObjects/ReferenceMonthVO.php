<?php

namespace App\Modules\Medicos\Domain\ValueObjects;

use App\Modules\Medicos\Domain\Exceptions\InvalidReferenceMonthException;

final class ReferenceMonthVO
{
    private function __construct(
        private readonly string $value
    ) {}

    public static function fromString(?string $raw): self
    {
        $raw = is_string($raw) ? trim($raw) : $raw;

        if ($raw === null || $raw === '') {
            throw InvalidReferenceMonthException::required();
        }

        if (!preg_match('/^(19|20)\d{2}-(0[1-9]|1[0-2])$/', $raw)) {
            throw InvalidReferenceMonthException::invalidFormat($raw);
        }

        return new self($raw);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function year(): int
    {
        return (int) substr($this->value, 0, 4);
    }

    public function month(): int
    {
        return (int) substr($this->value, 5, 2);
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
