<?php

use App\Modules\Medicos\Interface\Http\Controllers\MedicosOptionsController;
use App\Modules\Medicos\Interface\Http\Controllers\ProdutividadeImportController;
use Illuminate\Support\Facades\Route;

Route::middleware('api')->prefix('v1')->group(function () {
    Route::prefix('medicos')->group(function () {
        Route::get('options', [MedicosOptionsController::class, 'index']);
        Route::prefix('produtividade')->group(function() {
            Route::post('importar', [ProdutividadeImportController::class, 'import']);


        });
    });
});
