<?php

use App\Modules\Medicos\Interface\Http\Controllers\ProdutividadeImportController;
use Illuminate\Support\Facades\Route;

Route::middleware('api')->prefix('v1')->group(function () {
    Route::prefix('medicos')->group(function () {
        Route::prefix('produtividade')->group(function() {
            Route::post('validar', [ProdutividadeImportController::class, 'validateFile']);
        });
    });
});
