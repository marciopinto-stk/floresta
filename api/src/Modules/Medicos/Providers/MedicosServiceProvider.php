<?php

namespace App\Modules\Medicos\Providers;

use App\Modules\Medicos\Application\UseCases\ValidateMedicalProductivityFileUseCase;
use App\Modules\Medicos\Application\UseCases\ValidateReferenceMonthUseCase;
use App\Modules\Medicos\Domain\Contracts\ValidateMedicalProductivityFileUseCaseContract;
use App\Modules\Medicos\Domain\Contracts\ValidateReferenceMonthUseCaseContract;
use Illuminate\Support\ServiceProvider;

final class MedicosServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // UseCases
        $this->app->bind(ValidateReferenceMonthUseCaseContract::class, ValidateReferenceMonthUseCase::class);
        $this->app->bind(ValidateMedicalProductivityFileUseCaseContract::class, ValidateMedicalProductivityFileUseCase::class);
    }

    public function boot(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../../../config/medicos.php', 'medicos');
        $this->loadRoutesFrom(__DIR__ . '/../Interface/routes.php');
    }
}
