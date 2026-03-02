<?php

namespace App\Modules\Medicos\Infrastructure\Repositories;

use App\Modules\Medicos\Domain\Contracts\repositories\MedicalProductivityRepositoryContract;
use Illuminate\Database\ConnectionInterface;

final class MedicalProductivityRepository implements MedicalProductivityRepositoryContract
{
    public function __construct(
        private readonly ConnectionInterface $db,
    ) {}

    public function exists(int $idRecepcaoItem, string $data, int $medico): bool
    {
        $row = $this->db->selectOne("
            SELECT 1
            FROM produtividade_medica
            WHERE id_recepcao_item = :id_recepcao_item
                AND data = :data
                AND usuario = :usuario
            LIMIT 1
        ",
        [
            'id_recepcao_item' => $idRecepcaoItem,
            'data' => $data,
            'usuario' => $medico,
        ]);

        return $row !== null;
    }

    public function insert(int $idRecepcaoItem, string $data, int $medico): void
    {
        $this->db->insert("
            INSERT INTO produtividade_medica (id_recepcao_item, data, usuario)
            VALUES (:id_recepcao_item, :data, :usuario)
        ",[
            'id_recepcao_item'  => $idRecepcaoItem,
            'data'              => $data,
            'usuario'           => $medico
        ]);
    }
}
