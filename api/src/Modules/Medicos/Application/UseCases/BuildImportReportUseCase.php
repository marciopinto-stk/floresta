<?php

namespace App\Modules\Medicos\Application\UseCases;

use App\Modules\Medicos\Application\DTO\ImportReportDTO;
use App\Modules\Medicos\Domain\Contracts\BuildImportReportUseCaseContract;

final class BuildImportReportUseCase implements BuildImportReportUseCaseContract
{
    public function handle(ImportReportDTO $report): ImportReportDTO
    {
        if ($report->total < 0) {
            $report->total = 0;
        }

        if ($report->success < 0) {
            $report->success = 0;
        }

        if ($report->failed < 0) {
            $report->failed = 0;
        }

        // Se nenhum status foi definido explicitamente
        if (empty($report->status)) {
            $report->status = $report->failed > 0
                ? 'processed_with_errors'
                : 'processed';
        }

        return $report;
    }
}

