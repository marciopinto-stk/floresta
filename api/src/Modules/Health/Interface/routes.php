<?php

use App\Modules\Health\Interface\Http\HealthController;
use Illuminate\Support\Facades\Route;

Route::middleware('api')->prefix('v1')->group(function () {
    Route::prefix('health')->group(function() {
        Route::get('/', HealthController::class);
    });
});
