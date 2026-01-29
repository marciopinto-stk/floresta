<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Sensrit\Interface\Http\Controllers\SensritTokenController;
use App\Modules\Sensrit\Interface\Http\Controllers\TicketsDashboardController;

Route::middleware('api')->prefix('v1')->group(function () {
    Route::prefix('sensrit')->group(function () {
        Route::post('token', [SensritTokenController::class, 'update']);
        Route::get('token/validate', [SensritTokenController::class, 'validateToken']);

        // Dashboard
        Route::get('tickets/open-vs-closed', [TicketsDashboardController::class, 'openVsClosed']);
        Route::get('tickets/open-by-category', [TicketsDashboardController::class, 'openByCategory']);
        Route::get('tickets/opened-by-day', [TicketsDashboardController::class, 'openedByDayInMonth']);
    });
});
