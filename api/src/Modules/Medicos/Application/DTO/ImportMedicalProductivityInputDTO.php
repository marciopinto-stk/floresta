<?php

namespace App\Modules\Medicos\Application\DTO;

final readonly class ImportMedicalProductivityInputDTO
{
    public function __construct(
        public string $monthReference, //"YYYYMM" (ex.: "202601")
        public string $uploadedFilePath,
        public ExecutorUserDTO $executor,
        public ?string $originalFilename = null,
    ) {}
}
