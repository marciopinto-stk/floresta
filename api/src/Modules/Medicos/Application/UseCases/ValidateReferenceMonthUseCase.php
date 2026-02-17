<?php

namespace App\Modules\Medicos\Application\UseCases;

use App\Modules\Medicos\Domain\Contracts\ValidateReferenceMonthUseCaseContract;
use App\Modules\Medicos\Domain\ValueObjects\ReferenceMonthVO;


final class ValidateReferenceMonthUseCase implements ValidateReferenceMonthUseCaseContract
{
    public function handle(?string $referenceMonth): ReferenceMonthVO
    {
        return ReferenceMonthVO::fromString($referenceMonth);
    }
}
