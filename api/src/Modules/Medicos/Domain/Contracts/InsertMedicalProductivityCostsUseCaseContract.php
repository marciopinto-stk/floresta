<?php

namespace App\Modules\Medicos\Domain\Contracts;

interface InsertMedicalProductivityCostsUseCaseContract
{
    public function handle(string $monthReference): void; // ex: "202601"
}
