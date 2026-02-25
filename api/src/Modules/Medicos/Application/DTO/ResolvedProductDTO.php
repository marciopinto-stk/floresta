<?php

namespace App\Modules\Medicos\Application\DTO;

final readonly class ResolvedProductDTO
{
    public function __construct(
        public int $id_produto,
        public ?string $produto = null,
        public ?string $grupo = null,
    ) {}
}
