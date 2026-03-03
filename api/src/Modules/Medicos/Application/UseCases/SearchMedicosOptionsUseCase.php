<?php

namespace App\Modules\Medicos\Application\UseCases;

use App\Modules\Medicos\Domain\Contracts\Repositories\MedicosOptionsRepositoryContract;

final class SearchMedicosOptionsUseCase
{
    public function __construct(
        private readonly MedicosOptionsRepositoryContract $repository,
    ) {}

    public function handle(string $q = '', int $page = 1, int $limit = 20, bool $onlyActive = true,)
    {
        return $this->repository->search(
            q: $q,
            page: $page,
            limit: $limit,
            onlyActive: $onlyActive,
            tipoProfissional: null
        );
    }
}
