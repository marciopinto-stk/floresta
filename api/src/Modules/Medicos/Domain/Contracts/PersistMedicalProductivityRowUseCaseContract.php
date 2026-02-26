<?php

namespace App\Modules\Medicos\Domain\Contracts;

use App\Modules\Medicos\Application\DTO\ParsedMedicalProductivityRowDTO;

interface PersistMedicalProductivityRowUseCaseContract
{
    public function handle(ParsedMedicalProductivityRowDTO $row): void;
}
