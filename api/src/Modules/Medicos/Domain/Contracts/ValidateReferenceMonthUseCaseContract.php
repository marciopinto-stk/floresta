<?php

namespace App\Modules\Medicos\Domain\Contracts;

use App\Modules\Medicos\Domain\ValueObjects\ReferenceMonthVO;

interface ValidateReferenceMonthUseCaseContract
{
    /**
     * @throws \App\Modules\Medicos\Domain\Exceptions\InvalidReferenceMonthException
     */
    public function handle(?string $referenceMonth): ReferenceMonthVO;
}
