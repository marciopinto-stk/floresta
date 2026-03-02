<?php

namespace App\Modules\Medicos\Application\UseCases;

use App\Modules\Medicos\Application\DTO\ImportMedicalProductivityInputDTO;
use App\Modules\Medicos\Domain\Contracts\LoadProductivityExceptionsUseCaseContract;
use App\Modules\Medicos\Domain\Structures\ExceptionsSet;

final class LoadProductivityExceptionsUseCase implements LoadProductivityExceptionsUseCaseContract
{
    public function handle(ImportMedicalProductivityInputDTO $input): ExceptionsSet
    {
        $items = $input->exceptions ?? [];

        return new ExceptionsSet($items);
    }
}
