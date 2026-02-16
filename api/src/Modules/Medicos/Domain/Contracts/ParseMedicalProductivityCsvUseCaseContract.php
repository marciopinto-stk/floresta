<?php

namespace App\Modules\Medicos\Domain\Contracts;

use App\Modules\Medicos\Application\DTO\ImportMedicalProductivityInputDTO;
use App\Modules\Medicos\Application\DTO\ParsedMedicalProductivityRowDTO;

interface ParseMedicalProductivityCsvUseCaseContract
{
    /**
     * Retorna um iterável para permitir stream (memória baixa).
     * Cada item deve conter lineNumber + payload normalizado.
     *
     * @return iterable<ParsedMedicalProductivityRowDTO>
     */
    public function handle(ImportMedicalProductivityInputDTO $input): iterable;
}
