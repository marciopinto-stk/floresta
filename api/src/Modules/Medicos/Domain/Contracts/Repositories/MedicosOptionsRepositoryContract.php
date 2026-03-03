<?php

namespace App\Modules\Medicos\Domain\Contracts\Repositories;

use App\SharedKernel\Application\DTO\OptionsPageDTO;

interface MedicosOptionsRepositoryContract
{
    public function search(string $q = '', int $page = 1, int $limit = 20, bool $onlyActive = true, ?string $tipoProfissional = null): OptionsPageDTO;
}
