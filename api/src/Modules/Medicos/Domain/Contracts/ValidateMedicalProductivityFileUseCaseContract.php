<?php

namespace App\Modules\Medicos\Domain\Contracts;

use App\Modules\Medicos\Application\DTO\ImportMedicalProductivityInputDTO;

interface ValidateMedicalProductivityFileUseCaseContract
{
    public function handle(ImportMedicalProductivityInputDTO $input): void;
}
