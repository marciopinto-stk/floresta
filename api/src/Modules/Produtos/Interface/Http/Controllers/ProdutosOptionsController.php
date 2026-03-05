<?php

namespace App\Modules\Produtos\Interface\Http\Controllers;

use App\Modules\Produtos\Application\UseCases\SearchProdutosOptionsUseCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class ProdutosOptionsController
{
    public function __construct(
        private readonly SearchProdutosOptionsUseCase $useCase
    ) {}

    public function index(Request $request): JsonResponse
    {
        $q      = (string) $request->query('q', '');
        $page   = max(1, (int) $request->query('page', 1));
        $limit  = (int) $request->query('limit', 20);
        $limit  = min(max($limit, 1), 50);

        $dto = $this->useCase->handle(
            q: $q,
            page: $page,
            limit: $limit,
        );

        return response()->json($dto->toArray());
    }
}
