<?php

namespace App\Modules\Produtos\Infrastructure\Repositories;

use App\Modules\Produtos\Domain\Contracts\Repositories\ProdutosOptionsRepositoryContract;
use App\SharedKernel\Application\DTO\OptionsPageDTO;
use App\SharedKernel\Infrastructure\S2\Models\Produto;

final class ProdutosOptionsRepository implements ProdutosOptionsRepositoryContract
{
    public function search(string $q = '', int $page = 1, int $limit = 20): OptionsPageDTO
    {
        $page   = max(1, $page);
        $limit  = min(max($limit,1), 50);

        $query = Produto::query()
            ->select(['id_produto', 'produto']);

        $q = trim($q);

        if($q !== '') {
            $query->where(function ($sub) use ($q) {
                if (ctype_digit($q)) {
                    $sub->where('id_produto', (int) $q)
                        ->orWhere('produto', 'like', "%{$q}%");
                } else {
                    $sub->where('produto', 'like', "%{$q}%");
                }
            });

            // ranking: quem começa com o termo vem primeiro
            $query->orderByRaw(
                "CASE WHEN produto LIKE ? THEN 0 ELSE 1 END",
                ["{$q}%"]
            );
        }

        $query->orderBy('produto');

        $total  = (clone $query)->count();
        $rows   = $query->forPage($page, $limit)->get();

        $data = $rows->map(static fn (Produto $p) => [
            'value' => (int) $p->id_produto,
            'label' => ucfirst(strtolower($p->produto)),
        ])->all();

        return new OptionsPageDTO(
            data: $data,
            page: $page,
            limit: $limit,
            total: $total,
        );
    }
}
