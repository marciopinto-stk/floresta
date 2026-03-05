<?php

namespace App\Modules\Produtos\Domain\Contracts\Repositories;

use App\SharedKernel\Application\DTO\OptionsPageDTO;

interface ProdutosOptionsRepositoryContract
{
    public function search(string $q = '', int $page = 1, int $limit = 20): OptionsPageDTO;
}
