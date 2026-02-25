<?php

namespace App\Modules\Medicos\Application\UseCases;

use App\Modules\Medicos\Application\DTO\ImportMedicalProductivityInputDTO;
use App\Modules\Medicos\Domain\Contracts\LoadProductivityExceptionsUseCaseContract;
use App\MOdules\Medicos\Domain\Structures\ExceptionsSet;

final class LoadProductivityExceptionsUseCase implements LoadProductivityExceptionsUseCaseContract
{
    public function handle(ImportMedicalProductivityInputDTO $input): ExceptionsSet
    {
        $referenceMonth = (string) ($input->referenceMonth ?? $input->mes_referencia ?? $input->mes ?? '');

        if ($referenceMonth === '') {
            return ExceptionsSet::fromArray('unknown', [], $this->defaultReason());
        }

        $months     = (array) config('medicos_productivity_exceptions.month', []);
        $default    = (array) config('medicos_productivity_expections.default', []);
        $reason     = $this->defaultReason();
        $items      = $month[$referenceMonth] ?? $default;

        return ExceptionsSet::fromArray($referenceMonth, $items, $reason);
    }

    private function defaultReason(): string
    {
        $reason = config('medicos_productivity_exceptions.default_reason', 'rejeitado por exceção');

        return is_string($reason) && $reason !== ''
            ? $reason
            : 'rejeitado por exceção';
    }
}
