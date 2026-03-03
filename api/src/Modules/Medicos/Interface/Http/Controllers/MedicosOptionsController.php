<?php

namespace App\Modules\Medicos\Interface\Http\Controllers;

use App\Modules\Medicos\Application\UseCases\SearchMedicosOptionsUseCase;
use App\SharedKernel\Application\DTO\OptionsPageDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class MedicosOptionsController
{
    public function __construct(
        private readonly SearchMedicosOptionsUseCase $useCase,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $q          = (string) $request->query('q', '');
        $page       = max(1, (int) $request->query('page', 1));
        $limit      = (int) $request->query('limit', 20);
        $limit      = min(max($limit, 1), 50);
        $onlyActive = (string) $request->query('ativo', 'S') !== 'N';

        $dto = $this->useCase->handle(
            q: $q,
            page: $page,
            limit: $limit,
            onlyActive: $onlyActive,
        );

        return response()->json($dto->toArray());
    }
}
