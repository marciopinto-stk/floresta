<?php

namespace App\Modules\Medicos\Infrastructure\Repositories;

use App\Modules\Medicos\Application\DTO\ResolvedProductDTO;
use App\Modules\Medicos\Domain\Contracts\Repositories\ResolveProductFromRecepcaoItemRepositoryContract;
use App\SharedKernel\Infrastructure\S2\Models\RecepcaoItem;

final class ResolveProductFromRecepcaoItemRepository implements ResolveProductFromRecepcaoItemRepositoryContract
{
    public function findByRecepcaoItemId(int $idRecepcaoItem): ?ResolvedProductDTO
    {
        $item = RecepcaoItem::query()
            ->with('produto:id_produto,produto,grupo')
            ->select(['id_item', 'id_produto'])
            ->whereKey($idRecepcaoItem)
            ->fisrt();

        if (!$item || !$item->produto) {
            return null;
        }

        return new ResolvedProductDTO(
            id_produto: (int) $item->produto->id_produto,
            produto: $item->produto->produto ?? null,
            grupo: $item->produto->grupo ?? null,
        );
    }
}
