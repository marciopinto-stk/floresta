<?php

namespace App\Modules\Medicos\Application\UseCases;

use App\Modules\Medicos\Application\DTO\ParsedMedicalProductivityRowDTO;
use App\Modules\Medicos\Domain\Contracts\PersistMedicalProductivityRowUseCaseContract;
use App\Modules\Medicos\Domain\Contracts\ResolveProductFromRecepcaoItemUseCaseContract;

final class PersistMedicalProductivityRowUseCase implements PersistMedicalProductivityRowUseCaseContract
{
    public function __construct(
        private readonly ResolveProductFromRecepcaoItemUseCaseContract $resolveProduct,
        private readonly MedicalProductivityRepositoryContract $repository,
    ) {}

    public function handle(ParsedMedicalProductivityRowDTO $row): void
    {
        $resolved = $this->resolveProduct->handle((int) $row->id_recepcao_item);

        $row->id_produto    = $resolved->id_produto;
        $row->produto       = $resolved->produto;
        $row->grupo         = $resolved->grupo;

        $this->repository->insert($row);
    }
}
