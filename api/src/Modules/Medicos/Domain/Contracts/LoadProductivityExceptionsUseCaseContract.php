<?php

namespace App\Modules\Medicos\Domain\Contracts;

use App\Modules\Medicos\Application\DTO\ImportMedicalProductivityInputDTO;
use App\MOdules\Medicos\Domain\Structures\ExceptionsSet;

interface LoadProductivityExceptionsUseCaseContract
{
    public function handle(ImportMedicalProductivityInputDTO $inut): ExceptionsSet;
}
