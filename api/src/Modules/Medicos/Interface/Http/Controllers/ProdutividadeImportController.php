<?php

namespace App\Modules\Medicos\Interface\Http\Controllers;

use App\Modules\Medicos\Application\DTO\ExecutorUserDTO;
use App\Modules\Medicos\Application\DTO\ImportMedicalProductivityInputDTO;
use App\Modules\Medicos\Application\UseCases\ImportMedicalProductivityOrchestratorUseCase;
use App\Modules\Medicos\Interface\Http\Requests\ProdutividadeImportRequest;
use Illuminate\Support\Facades\Storage;

final class ProdutividadeImportController {
    public function __construct(
        private readonly ImportMedicalProductivityOrchestratorUseCase $orchestrator,
    ) {}

    public function import(ProdutividadeImportRequest $request)
    {
        $file = $request->file('file');

        $originalExt = strtolower((string) $file->getClientOriginalExtension());
        $ext    = $originalExt !== '' ? $originalExt : 'csv';
        $base   = pathinfo($file->hashName(), PATHINFO_FILENAME);
        $stored = $file->storeAs('imports', $base . '.' . $ext);

        // enquanto não existe usuario logado
        $executor = new ExecutorUserDTO(
            id: 0,
            name: 'system-import'
        );
        // Quando o login for implementado
        // $executor = new ExecutorUserDTO(
        //     id: $request->user()->id,
        //     name: $request->user()->name,
        //     type: 'user'
        // );

        $competencia    = $request->competencia();
        $exceptionsRaw  = $request->input('exceptions', '[]');
        $exceptions     = is_string($exceptionsRaw)
            ? json_decode($exceptionsRaw, true) ?? []
            : (is_array($exceptionsRaw) ? $exceptionsRaw : []);

        $input = new ImportMedicalProductivityInputDTO(
            monthReference: (string) ($competencia ?? ''),
            uploadedFilePath: Storage::disk('local')->path($stored),
            executor: $executor,
            originalFilename: $file->getClientOriginalName(),
            dryRun: $request->dryRun(),
            exceptions: $exceptions,
        );

        $report = $this->orchestrator->handle($input);

        $httpStatus = match ($report->status ?? null) {
            'validation_failed' => 422,
            'processing_failed' => 500,
            default => 200,
        };

        return response()->json([
            'ok' => ($report->status ?? null) !== 'validation_failed',
            'data' => $report,
        ], $httpStatus);
    }
}
