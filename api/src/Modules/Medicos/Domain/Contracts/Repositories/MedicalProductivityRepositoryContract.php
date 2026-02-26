<?php

namespace App\Modules\Medicos\Domain\Contracts\repositories;

interface MedicalProductivityRepositoryContract
{
    public function exists(int $idRecepcaoItem, string $data, int $usuario): bool;

    public function insert(int $idRecepcaoItem, string $data, int $usuario): void;
}
