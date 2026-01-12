<?php

namespace App\Modules\Sensrit\Domain\Contracts\Repositories;

interface SensritTokenRepositoryContract
{
    public function getToken(): ?string;
    public function setToken(string $token): void;
}
