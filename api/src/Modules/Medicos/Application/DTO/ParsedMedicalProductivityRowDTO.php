<?php

namespace App\Modules\Medicos\Application\DTO;

final readonly class ParsedMedicalProductivityRowDTO
{
    public function __construct(
        public int $lineNumber,
        /** payload normalizado (ex: accessionNumber/dataLaudo/profissionalIdS2 etc.) */
        public array $data,
    ) {}
}
