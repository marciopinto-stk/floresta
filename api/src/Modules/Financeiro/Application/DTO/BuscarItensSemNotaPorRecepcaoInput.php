<?php

namespace App\Modules\Financeiro\Application\DTO;

final class BuscarItensSemNotaPorRecepcaoInput
{
    public function __construct(
        public readonly int $idRecepcao,
        public readonly int $limit = 1500,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            idRecepcao: (int) ($data['id_recepcao'] ?? 0),
            limit: (int) ($data['limit'] ?? 1500),
        );
    }
}
