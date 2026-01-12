<?php

namespace App\Modules\Sensrit\Application\UseCases\UpdateSensritToken;

class UpdateSensritTokenInput
{
    public function __construct(
        public string $token,
    ) {}
}
