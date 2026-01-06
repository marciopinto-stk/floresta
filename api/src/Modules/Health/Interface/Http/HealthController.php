<?php

namespace App\Modules\Health\Interface\Http;

use App\Modules\Health\Application\CheckHealthService;
use Symfony\Component\HttpFoundation\JsonResponse;

class HealthController
{
    public function __invoke(CheckHealthService $service): JsonResponse
    {
        return response()->json(
            $service->execute(),
            200
        );
    }
}
