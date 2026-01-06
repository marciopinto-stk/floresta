<?php

namespace App\Modules\Financeiro\Providers;

use Illuminate\Support\ServiceProvider;

class FinanceiroServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../../../config/financeiro.php', 'financeiro');
        $this->loadRoutesFrom(__DIR__ . '/../Interface/routes.php');
    }
}
