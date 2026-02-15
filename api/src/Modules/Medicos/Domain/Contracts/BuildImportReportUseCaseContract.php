<?php

namespace App\Modules\Medicos\Domain\Contracts;

use App\Modules\Medicos\Application\DTO\ImportReportDTO;

interface BuildImportReportUseCaseContract
{
    public function handle(ImportReportDTO $report): ImportReportDTO;
}
