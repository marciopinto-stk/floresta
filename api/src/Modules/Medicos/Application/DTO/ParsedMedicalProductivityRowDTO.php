<?php

namespace App\Modules\Medicos\Application\DTO;

final readonly class ParsedMedicalProductivityRowDTO
{
    public function __construct(
        public int $line,
        public readonly int $id_recepcao_item,
        public readonly string $data,
        public readonly int $id_executante,

        public ?int $id_produto     = null,
        public ?string $produto     = null,
        public ?string $grupo       = null,
        public readonly ?array $raw = null,
    ) {}
}
