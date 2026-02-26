<?php

namespace App\Modules\Medicos\Domain\Contracts\Repositories;

use App\Modules\Medicos\Application\DTO\ResolvedProductDTO;

interface ResolveProductFromRecepcaoItemRepositoryContract
{
    public function findByRecepcaoItemId(int $idRecepcaoItem): ?ResolvedProductDTO;
}
