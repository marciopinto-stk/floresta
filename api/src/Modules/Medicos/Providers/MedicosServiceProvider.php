<?php

namespace App\Modules\Medicos\Providers;

use App\Modules\Medicos\Application\UseCases\LoadProductivityExceptionsUseCase;
use App\Modules\Medicos\Application\UseCases\ResolveProductFromRecepcaoItemUseCase;
use App\Modules\Medicos\Application\UseCases\ValidateMedicalProductivityFileUseCase;
use App\Modules\Medicos\Application\UseCases\ValidateReferenceMonthUseCase;
use App\Modules\Medicos\Domain\Contracts\LoadProductivityExceptionsUseCaseContract;
use App\Modules\Medicos\Domain\Contracts\Repositories\ResolveProductFromRecepcaoItemRepositoryContract;
use App\Modules\Medicos\Domain\Contracts\ResolveProductFromRecepcaoItemUseCaseContract;
use App\Modules\Medicos\Domain\Contracts\ValidateMedicalProductivityFileUseCaseContract;
use App\Modules\Medicos\Domain\Contracts\ValidateReferenceMonthUseCaseContract;
use App\Modules\Medicos\Infrastructure\Repositories\ResolveProductFromRecepcaoItemRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;

final class MedicosServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // UseCases
        $this->app->bind(ValidateReferenceMonthUseCaseContract::class, ValidateReferenceMonthUseCase::class);
        $this->app->bind(ValidateMedicalProductivityFileUseCaseContract::class, ValidateMedicalProductivityFileUseCase::class);
        $this->app->bind(LoadProductivityExceptionsUseCaseContract::class, LoadProductivityExceptionsUseCase::class);
        $this->app->bind(ResolveProductFromRecepcaoItemUseCaseContract::class, ResolveProductFromRecepcaoItemUseCase::class);
        $this->app->bind(ResolveProductFromRecepcaoItemRepositoryContract::class, ResolveProductFromRecepcaoItemRepository::class);

        $this->app->when(ResolveProductFromRecepcaoItemRepository::class)
            ->needs(\Illuminate\Database\ConnectionInterface::class)
            ->give(fn () => DB::connection('mysql_legacy'));
    }

    public function boot(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../../../config/medicos.php', 'medicos');
        $this->loadRoutesFrom(__DIR__ . '/../Interface/routes.php');
    }
}
