<?php

namespace App\Modules\Medicos\Domain\Contracts;

use App\Modules\Medicos\Application\DTO\ParsedMedicalProductivityRowDTO;

interface ValidateMedicalProductivityRowUseCaseContract
{
    /** @throws \DomainException|\InvalidArgumentException */
    public function handle(ParsedMedicalProductivityRowDTO $row): void;
}
