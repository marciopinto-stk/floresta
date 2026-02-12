<?php

namespace App\Modules\Medicos\Application\DTO;

final class ProdutividadeRowDTO
{
    public function __construct(
        public readonly int|string $laudoId,
    ) {}
}
