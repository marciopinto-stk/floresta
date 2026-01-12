<?php

namespace App\Modules\Sensrit\Application\UseCases\UpdateSensritToken;

use App\Modules\Sensrit\Domain\Contracts\Repositories\SensritTokenRepositoryContract;

class UpdateSensritTokenUseCase
{
    public function __construct(
        private SensritTokenRepositoryContract $repo,
    ) {}

    public function execute(UpdateSensritTokenInput $input): void
    {
        $token = trim($input->token);
        if ($token === '') {
            throw new \InvalidArgumentException('Token inválido.');
        }

        $this->repo->setToken($token);
    }
}
