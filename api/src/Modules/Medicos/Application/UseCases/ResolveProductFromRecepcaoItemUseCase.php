<?php

namespace App\Modules\Medicos\Application\UseCases;

use App\Modules\Medicos\Application\DTO\ResolvedProductDTO;
use App\Modules\Medicos\Domain\Contracts\Repositories\ResolveProductFromRecepcaoItemRepositoryContract;
use App\Modules\Medicos\Domain\Contracts\ResolveProductFromRecepcaoItemUseCaseContract;
use App\Modules\Medicos\Domain\Exceptions\ResolvedProductNotFoundException;
use Psr\Log\LoggerInterface;

final class ResolveProductFromRecepcaoItemUseCase implements ResolveProductFromRecepcaoItemUseCaseContract
{
    public function __construct(
        private readonly ResolveProductFromRecepcaoItemRepositoryContract $repository,
        private readonly LoggerInterface $logger
    ) {}

    public function handle (int $idRecepcaoItem): ResolvedProductDTO
    {
        $resolved = $this->repository->findByRecepcaoItemId($idRecepcaoItem);

        if ($resolved !== null) {
            return $resolved;
        }

        $this->logger->warning('Produto não resolvido a partir de id_recepcao_item', [
            'id_recepcao_item'  => $idRecepcaoItem,
            'usecase'           => self::class,
            'module'            => 'medicos',
        ]);

        throw new ResolvedProductNotFoundException($idRecepcaoItem);
    }
}
