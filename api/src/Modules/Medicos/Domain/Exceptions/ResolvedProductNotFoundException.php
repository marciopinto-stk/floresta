<?php

namespace App\Modules\Medicos\Domain\Exceptions;

use RuntimeException;

final class ResolvedProductNotFoundException extends RuntimeException
{
    public function __construct(
        public readonly int $idRecepcaoItem,
        ?string $message = null,
    ) {
        parent::__construct($message ?? "Produto não encontrado para id_recepcao_item={$idRecepcaoItem}");
    }
}
