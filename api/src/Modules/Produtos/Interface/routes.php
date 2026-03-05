<?php

use App\Modules\Produtos\Interface\Http\Controllers\ProdutosOptionsController;
use Illuminate\Support\Facades\Route;

Route::middleware('api')->prefix('v1')->group(function () {
    Route::prefix('produtos')->group(function () {
        Route::get('options', [ProdutosOptionsController::class, 'index']);
    });
});
