<?php

namespace App\Modules\Medicos\Providers;

use Illuminate\Support\ServiceProvider;

class MedicosServiceProvider extends ServiceProvider
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
