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
        ];
    }
}
