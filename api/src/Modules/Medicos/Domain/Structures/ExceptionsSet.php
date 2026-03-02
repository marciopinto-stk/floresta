<?php

namespace App\Modules\Medicos\Domain\Structures;

final class ExceptionsSet
{
    public const REASON = 'rejeitado por exceção';

    private array $index = [];

    public function __construct(array $items = [])
    {
        foreach ($items as $item) {
            $medico  = (int) $item['id_medico'];
            $produto = (int) $item['id_produto'];

            $this->index[$this->key($medico, $produto)] = true;
        }
    }

    public function matches(int $idMedico, int $idProduto): ?string
    {
        if (!isset($this->index[$this->key($idMedico, $idProduto)])) {
            return null;
        }

        return self::REASON;
    }

    private function key(int $idMedico, int $idProduto): string
    {
        return $idMedico . ':' . $idProduto;
    }

    public function count(): int
    {
        return count($this->index);
    }
}
