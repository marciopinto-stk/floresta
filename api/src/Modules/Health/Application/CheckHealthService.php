<?php

namespace App\Modules\Health\Application;

use Carbon\Carbon;

class CheckHealthService
{
    public function execute(): array
    {
        return [
            'status'    => 'ok',
            'app'       => 'api-floresta',
            'timestamp' => Carbon::now()->toIso8601String(),
            [
    'upload_max_filesize' => ini_get('upload_max_filesize'),
    'post_max_size' => ini_get('post_max_size'),
    'memory_limit' => ini_get('memory_limit'),
    'max_input_time' => ini_get('max_input_time'),
    'max_execution_time' => ini_get('max_execution_time'),
            ],
        ];
    }
}
