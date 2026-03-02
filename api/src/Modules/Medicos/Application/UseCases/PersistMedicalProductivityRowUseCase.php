<?php

namespace App\Modules\Medicos\Application\UseCases;

use App\Modules\Medicos\Application\DTO\ParsedMedicalProductivityRowDTO;
use App\Modules\Medicos\Application\DTO\PersistRowResultDTO;
use App\Modules\Medicos\Domain\Contracts\PersistMedicalProductivityRowUseCaseContract;
use App\Modules\Medicos\Domain\Contracts\repositories\MedicalProductivityRepositoryContract;
use App\Modules\Medicos\Domain\Contracts\ResolveProductFromRecepcaoItemUseCaseContract;
use App\Modules\Medicos\Domain\Exceptions\ResolvedProductNotFoundException;
use Psr\Log\LoggerInterface;

final class PersistMedicalProductivityRowUseCase implements PersistMedicalProductivityRowUseCaseContract
{
    public function __construct(
        private readonly ResolveProductFromRecepcaoItemUseCaseContract $resolveProduct,
        private readonly MedicalProductivityRepositoryContract $repository,
        private readonly LoggerInterface $logger,
    ) {}

    public function handle(ParsedMedicalProductivityRowDTO $row): PersistRowResultDTO
    {
        try {
            $resolved = $this->resolveProduct->handle((int) $row->id_recepcao_item);

            // enriquece a linha
            $row->id_produto = $resolved->id_produto;
            $row->produto   = $resolved->produto;
            $row->grupo     = $resolved->grupo;
        } catch(ResolvedProductNotFoundException $e) {
            $this->logger->warning('Produto não resolvido (persist continuará)', [
                'line' => $row->line,
                'id_recepcao_item' => $row->id_recepcao_item,
            ]);
        }

        $idRecepcaoItem = (int) $row->id_recepcao_item;
        $data           = (string) $row->data;
        $medico         = (string) $row->id_executante;

        if ($this->repository->exists($idRecepcaoItem, $data, $medico)) {
            $this->logger->info('Produtividade médica: duplicado ignorado', [
                'line' => $row->line,
                'id_recepcao_item' => $idRecepcaoItem,
                'id_produto' => $row->id_produto ?? null,
            ]);
        }

        return PersistRowResultDTO::skippedDuplicate();
    }
}
