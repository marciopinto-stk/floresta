<?php

namespace App\Modules\Medicos\Application\DTO;

final class ImportReportDTO
{
    public function __construct(
        public string $status,          // processed | processed_with_errors | validation_failed | processed_but_costs_failed
        public string $monthReference, // "YYYYMM"
        public int $total       = 0,
        public int $success     = 0,
        public int $failed      = 0,
        public array $errors    = [],
        public array $logs      = [],
    ) {}

    public function addError(int $line, string $message, ?string $code = null): void
    {
        $this->failed++;
        $this->errors[] = array_filter([
            'line'  => $line,
            'error' => $message,
            'code'  => $code,
        ]);
    }

    public function addSuccess(): void
    {
        $this->success++;
    }

    public function addLog(string $step, string $level, string $message, array $context = []): void
    {
        $this->logs[] = [
            'step'      => $step,
            'at'        => now()->toISOString(),
            'level'     => $level,
            'message'   => $message,
            'context'   => $context?: null,
        ];
    }
}
