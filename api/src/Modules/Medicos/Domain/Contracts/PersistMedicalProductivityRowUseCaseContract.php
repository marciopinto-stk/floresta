<?php

namespace App\Modules\Medicos\Domain\Contracts;

use App\Modules\Medicos\Application\DTO\ParsedMedicalProductivityRowDTO;
use App\Modules\Medicos\Application\DTO\PersistRowResultDTO;

interface PersistMedicalProductivityRowUseCaseContract
{
    public function handle(ParsedMedicalProductivityRowDTO $row): PersistRowResultDTO;
}
