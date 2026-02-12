<?php

namespace App\Modules\Health\Interface\Http;

use App\Modules\Health\Application\CheckHealthService;
use Symfony\Component\HttpFoundation\JsonResponse;

class HealthController
{
    public function __invoke(CheckHealthService $service): JsonResponse
    {
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
        return response()->json(
            $service->execute(),
            
            200
        );
    }
}
