<?php

use App\Modules\Financeiro\Interface\Http\Controllers\InvoiceController;
use Illuminate\Support\Facades\Route;

Route::middleware('api')->prefix('v1')->group(function () {
    Route::prefix('financeiro')->group(function () {
        Route::get('/', fn () => response()->json(['message' => 'Financeiro Module API']));
        Route::get('/notas', [InvoiceController::class, 'buscarItensSemNotaPorRecepcao']);
        Route::post('/notas/processar', [InvoiceController::class, 'processarNotas']);
        //Route::get('recepcoes{id_recepcao}/notas/count', [InvoiceController::class, 'contarNotasPorRecepcao']);
        Route::get('/notas/processar/recepcao', [InvoiceController::class, 'processarNotasPorRecepcao']);
        Route::get('/notas/emitir/recepcao', [InvoiceController::class, 'emitirNotasPorRecepcao']);
    });
});
