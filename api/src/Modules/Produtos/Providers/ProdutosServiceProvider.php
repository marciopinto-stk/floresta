<?php

namespace App\Modules\Produtos\Providers;

use Illuminate\Support\ServiceProvider;

final class ProdutosServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../Interface/routes.php');
    }
}
