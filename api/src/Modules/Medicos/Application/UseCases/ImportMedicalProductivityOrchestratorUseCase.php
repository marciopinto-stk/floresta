<?php

namespace App\Modules\Medicos\Application\UseCases;

use App\Modules\Medicos\Application\DTO\ImportMedicalProductivityInputDTO;
use App\Modules\Medicos\Application\DTO\ImportReportDTO;
use App\Modules\Medicos\Domain\Contracts\BuildImportReportUseCaseContract;
use App\Modules\Medicos\Domain\Contracts\InsertMedicalProductivityCostsUseCaseContract;
use App\Modules\Medicos\Domain\Contracts\ParseMedicalProductivityCsvUseCaseContract;
use App\Modules\Medicos\Domain\Contracts\PersistMedicalProductivityRowUseCaseContract;
use App\Modules\Medicos\Domain\Contracts\ValidateMedicalProductivityFileUseCaseContract;
use App\Modules\Medicos\Domain\Contracts\ValidateMedicalProductivityRowUseCaseContract;
use App\Modules\Medicos\Domain\Contracts\ValidateReferenceMonthUseCaseContract;
use Psr\Log\LoggerInterface;
use Throwable;

final class ImportMedicalProductivityOrchestratorUseCase
{
    public function __construct(
        private readonly ValidateReferenceMonthUseCaseContract $validateReferenceMonth,
        private readonly ValidateMedicalProductivityFileUseCaseContract $validateFile,
        private readonly LoggerInterface $logger,
        private readonly BuildImportReportUseCaseContract $buildReport,
        private readonly ParseMedicalProductivityCsvUseCaseContract $parseCsv,
        private readonly ValidateMedicalProductivityRowUseCaseContract $validateRow,
        private readonly PersistMedicalProductivityRowUseCaseContract $persistRow,
        private readonly InsertMedicalProductivityCostsUseCaseContract $insertCosts,
    ) {}

    public function handle(ImportMedicalProductivityInputDTO $input): ImportReportDTO
    {
        $report = new ImportReportDTO(
            status: 'processing',
            monthReference: $input->monthReference,
        );

        $this->logStep($report, 'start', 'info', 'Iniciando importação de produtividade médica', [
            'monthReference'    => $input->monthReference,
            'executorId'        => $input->executor->id,
            'file'              => $input->originalFilename,
        ]);

        // Valida mês de referência
        try {
            $this->logStep($report, 'validate_reference_month:start', 'info', 'Validando mês de referência', [
                'monthReference' => $input->monthReference,
            ]);

            $month = $this->validateReferenceMonth->handle($input->monthReference);

            $report->monthReference = $month->value();

            $this->logStep($report, 'validate_reference_month:done', 'info', 'Mês de referência validado', [
                'monthReference' => $report->monthReference,
            ]);
        } catch(Throwable $e) {
            $this->logStep($report, 'validate_reference_month:error', 'error', 'Falha na validação do mês de referência', [
                'exception'      => $e::class,
                'message'        => $e->getMessage(),
                'monthReference' => $input->monthReference,
            ]);

            $report->status = 'validation_failed';

            return $this->buildReport->handle($report);
        }

        // Validações iniciais do arquivo (extensão, tamanho, cabeçalho, etc.)
        try {
            $this->logStep($report, 'validate_file:start', 'info', 'Validando arquivo');
            $this->validateFile->handle($input);
            $this->logStep($report, 'validate_file:done', 'info', 'Arquivo validado com sucesso');
        } catch (Throwable $e) {
            $this->logStep($report, 'validate_file:error', 'error', 'Falha na validação do arquivo', [
                'exception' => $e::class,
                'message'   => $e->getMessage(),
            ]);

             $report->status = 'validation_failed';

             return $this->buildReport->handle($report);
        }

        // Parsing + validação + persistencia por linha
        $this->logStep($report, 'parse:start', 'info', 'Iniciando parsing do CSV');

        $anyRowProcessed = false;

        foreach ($this->parseCsv->handle($input, function($err) use ($report) {
            $report->total++;

            $report->addError(
                line: $err->line,
                message: $err->message,
                code: null,
            );

            $this->logStep($report, 'parse:row_error', 'warning', 'Erro no parsing da linha', [
                'line'           => $err->line,
                'monthReference' => $report->monthReference,
                'message'        => $err->message,
                'context'        => $err->context,
            ]);
        }) as $row) {
            $anyRowProcessed = true;
            $report->total++;

            try {
                // valida linha (campos obrigatorios, formatos, etc.)
                $this->validateRow->handle($row);

                // persiste no banco do S2
                $this->persistRow->handle($row);

                $report->addSuccess();
            } catch(Throwable $e) {
                // Erro por linha não interrompe o processamento
                $report->addError(
                    line: $row->line,
                    message: $e->getMessage(),
                    code: method_exists($e, 'getCode') ? (string)$e->getCode() : null,
                );

                $this->logger->warning('Import produtividade: erro na linha', [
                    'line'              => $row->line,
                    'monthReference'    => $report->monthReference,
                    'exception'         => $e::class,
                    'message'           => $e->getMessage(),
                ]);
            }
        }

        $this->logStep($report, 'parse:done', 'info', 'Parsing finalizado', [
            'total'     => $report->total,
            'success'   => $report->success,
            'failed'    => $report->failed,
        ]);

        // insere `custos_extras`
        if ($anyRowProcessed) {
            try {
                $this->logStep($report, 'costs:start', 'info', 'Atualizando custos de produtividade');
                $this->insertCosts->handle($report->monthReference);
                $this->logStep($report, 'costs:done', 'info', 'Custos atualizados com sucesso');
            } catch(Throwable $e) {
                $this->logStep($report, 'costs:error', 'error', 'Falha ao atualizar custos', [
                    'exception' => $e::class,
                    'message'   => $e->getMessage(),
                ]);

                $report->status = 'processed_but_costs_failed';
                return $this->buildReport->handle($report);
            }
        }

        // Status Final
        if ($report->failed > 0) {
            $report->status = 'processed_with_errors';
        } else {
            $report->status = 'processed';
        }

        $this->logStep($report, 'finish', 'info', 'Importação finalizada', [
            'status'    => $report->status,
            'total'     => $report->total,
            'success'   => $report->success,
            'failed'    => $report->failed,
        ]);

        return $this->buildReport->handle($report);
    }

    private function logStep(ImportReportDTO $report, string $step, string $level, string $message, array $context = []): void
    {
        $report->addLog($step, $level, $message, $context);

        match($level) {
            'debug'     => $this->logger->debug($message, $context),
            'info'      => $this->logger->info($message, $context),
            'warning'   => $this->logger->warning($message, $context),
            'error'     => $this->logger->error($message, $context),
            default     => $this->logger->info($message, $context),
        };
    }
}
