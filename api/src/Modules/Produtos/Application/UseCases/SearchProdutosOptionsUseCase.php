<?php

namespace App\Modules\Produtos\Application\UseCases;

use App\Modules\Produtos\Domain\Contracts\Repositories\ProdutosOptionsRepositoryContract;
use App\SharedKernel\Application\DTO\OptionsPageDTO;

final class SearchProdutosOptionsUseCase
{
    public function __construct(
        private readonly ProdutosOptionsRepositoryContract $repository,
    ) {}

    public function handle(string $q = '', int $page = 1, int $limit = 20): OptionsPageDTO
    {
        return $this->repository->search(
            q: $q,
            page: $page,
            limit: $limit,
        );
    }
}
