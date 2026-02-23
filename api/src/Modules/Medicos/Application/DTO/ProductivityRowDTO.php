<?php

namespace App\Modules\Medicos\Application\DTO;

final class ProductivityRowDTO
{
    public function __construct(
        public readonly int $line,
        public readonly string $idRecepcaoItem, //AccessionNumber
        public readonly string $data,           // Y-m-d H:i:s
        public readonly string $medico,        // ProfissionalIdS2 normalixado
        public readonly array $raw = [],        // linha original (debug)
    ) {}
}
