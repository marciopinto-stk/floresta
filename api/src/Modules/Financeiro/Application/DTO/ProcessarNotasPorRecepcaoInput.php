<?php

namespace App\Modules\Financeiro\Application\DTO;

class ProcessarNotasPorRecepcaoInput
{
    public function __construct(
        public readonly int $idRecepcao,
        public readonly int $chunkSize = 50,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            idRecepcao: (int) $data['id_recepcao'],
            chunkSize: (int) ($data['chunk_size'] ?? 50),
        );
    }
}
