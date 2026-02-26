<?php

namespace App\Modules\Medicos\Domain\Contracts;

use App\Modules\Medicos\Application\DTO\ResolvedProductDTO;

interface ResolveProductFromRecepcaoItemUseCaseContract
{
    public function handle(int $idRecepcaoItem): ResolvedProductDTO;
}
