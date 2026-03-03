<?php

namespace App\Modules\Medicos\Infrastructure\Repositories;

use App\Modules\Medicos\Domain\Contracts\Repositories\MedicosOptionsRepositoryContract;
use App\SharedKernel\Application\DTO\OptionsPageDTO;
use App\SharedKernel\Infrastructure\S2\Models\Profissional;

final class MedicosOptionsRepository implements MedicosOptionsRepositoryContract
{
    public function search(string $q = '', int $page = 1, int $limit = 20, bool $onlyActive = true, ?string $tipoProfissional = null): OptionsPageDTO
    {
        $page   = max(1, $page);
        $limit  = min(max($limit,1), 50);

        $query = Profissional::query()
            ->select(['id_profissional', 'nome']);

        if($onlyActive) {
            $query->ativo();
        }

        if ($tipoProfissional !== null && $tipoProfissional !== '') {
            $query->where('id_tipo_profissional', $tipoProfissional);
        }

        $q = trim($q);
        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                if (ctype_digit($q)) {
                    $sub->where('id_profissional', (int) $q)
                        ->orWhere(fn ($s) => $s->nomeLike($q));
                } else {
                    $sub->nomeLike($q);
                }
            });

            $query->orderByRaw(
                "CASE
                    WHEN nome LIKE ? THEN 0
                    WHEN nome LIKE ? THEN 1
                    ELSE 2
                END",
                ["{$q}%", "% {$q}%"]
            );
        }

        $query->orderBy('nome');

        $total = (clone $query)->count();

        $rows = $query
            ->forPage($page, $limit)
            ->get();

        $data = $rows->map(static fn (Profissional $p) => [
            'value' => (int) $p->id_profissional,
            'label' => "{$p->nome}",
        ])->all();

        return new OptionsPageDTO(
            data: $data,
            page: $page,
            limit: $limit,
            total: $total
        );
    }
}
