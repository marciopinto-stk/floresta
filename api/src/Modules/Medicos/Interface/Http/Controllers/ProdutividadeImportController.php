<?php

namespace App\Modules\Medicos\Interface\Http\Controllers;

use App\Modules\Medicos\Infrastructure\Csv\ProdutividadeCsvValidator;
use App\Modules\Medicos\Interface\Http\Requests\ProdutividadeImportRequest;
use Illuminate\Http\JsonResponse;

final class ProdutividadeImportController {
    public function __construct(
        private readonly ProdutividadeCsvValidator $validator,
        //private readonly ImportarProdutividadeUseCase $useCase
    ) {}

    public function validateFile(ProdutividadeImportRequest $request): JsonResponse
    {
        $file   = $request->file('file');

        logger()->info('PHP-FPM INI CHECK', [
    'upload_max_filesize' => ini_get('upload_max_filesize'),
    'post_max_size' => ini_get('post_max_size'),
    'memory_limit' => ini_get('memory_limit'),
    'max_input_time' => ini_get('max_input_time'),
    'max_execution_time' => ini_get('max_execution_time'),
]);

logger()->info('FILES RAW', [
    'php_files' => $_FILES ?? null,
    'content_length' => $_SERVER['CONTENT_LENGTH'] ?? null,
    'content_type' => $_SERVER['CONTENT_TYPE'] ?? null,
]);
        $report = $this->validator->validate($file);
        $status = $report['ok'] ? 200 : 422;

        return response()->json($report, $status);

    }
    /*
    public function store(ProdutividadeImportRequest $request): JsonResponse
    {
        $result = $this->useCase->execute(
            file: $request->file('file'),
            competencia: $request->competencia(),
            dryRun: $request->dryRun(),
            indempotencyKey: $request->idempotencyKey(),
            userId: (int) optional($request->user())->id,
        );

        return response()->json($result, 201);
    }
        */
}
