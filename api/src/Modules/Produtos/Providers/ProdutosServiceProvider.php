<?php

namespace App\Modules\Produtos\Providers;

use App\Modules\Produtos\Domain\Contracts\Repositories\ProdutosOptionsRepositoryContract;
use App\Modules\Produtos\Infrastructure\Repositories\ProdutosOptionsRepository;
use Illuminate\Support\ServiceProvider;

final class ProdutosServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ProdutosOptionsRepositoryContract::class, ProdutosOptionsRepository::class);
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../Interface/routes.php');
    }
}
