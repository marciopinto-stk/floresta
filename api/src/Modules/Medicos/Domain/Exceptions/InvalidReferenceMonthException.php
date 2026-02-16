<?php

namespace App\Modules\Medicos\Domain\Exceptions;

use DomainException;

final class InvalidReferenceMonthException extends DomainException
{
    public static function required(): self
    {
        return new self('Mês de referência é obrigatório (formato esperado: YYYY-MM).');
    }

    public static function invalidFormat(string $value): self
    {
        return new self("Mês de referência inválido: '{$value}'. Formato esperado: YYYY-MM.");
    }

    public static function mustBePreviousMonth(string $value, string $expected): self
    {
        return new self("Mês de referência inválido: '{$value}'. Esperado o mês anterior ao corrente: '{$expected}'.");
    }
}
