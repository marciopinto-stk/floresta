<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Sensrit\Interface\Http\Controllers\SensritTokenController;

Route::prefix('api/sensrit')->group(function () {
    Route::post('token', [SensritTokenController::class, 'update']);
    Route::get('token/validate', [SensritTokenController::class, 'validateToken']);
});
