<?php

namespace App\Modules\Medicos\Providers;

use App\Modules\Medicos\Application\UseCases\BuildImportReportUseCase;
use App\Modules\Medicos\Application\UseCases\ImportMedicalProductivityOrchestratorUseCase;
use App\Modules\Medicos\Application\UseCases\LoadProductivityExceptionsUseCase;
use App\Modules\Medicos\Application\UseCases\ResolveProductFromRecepcaoItemUseCase;
use App\Modules\Medicos\Application\UseCases\ValidateMedicalProductivityFileUseCase;
use App\Modules\Medicos\Application\UseCases\ValidateReferenceMonthUseCase;
use App\Modules\Medicos\Domain\Contracts\BuildImportReportUseCaseContract;
use App\Modules\Medicos\Domain\Contracts\LoadProductivityExceptionsUseCaseContract;
use App\Modules\Medicos\Domain\Contracts\Repositories\MedicosOptionsRepositoryContract;
use App\Modules\Medicos\Domain\Contracts\Repositories\ResolveProductFromRecepcaoItemRepositoryContract;
use App\Modules\Medicos\Domain\Contracts\ResolveProductFromRecepcaoItemUseCaseContract;
use App\Modules\Medicos\Domain\Contracts\ValidateMedicalProductivityFileUseCaseContract;
use App\Modules\Medicos\Domain\Contracts\ValidateReferenceMonthUseCaseContract;
use App\Modules\Medicos\Infrastructure\Repositories\MedicosOptionsRepository;
use App\Modules\Medicos\Infrastructure\Repositories\ResolveProductFromRecepcaoItemRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Psr\Log\LoggerInterface;

final class MedicosServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // UseCases
        $this->app->bind(BuildImportReportUseCaseContract::class, BuildImportReportUseCase::class);
        $this->app->bind(ValidateReferenceMonthUseCaseContract::class, ValidateReferenceMonthUseCase::class);
        $this->app->bind(ValidateMedicalProductivityFileUseCaseContract::class, ValidateMedicalProductivityFileUseCase::class);
        $this->app->bind(LoadProductivityExceptionsUseCaseContract::class, LoadProductivityExceptionsUseCase::class);
        $this->app->bind(ResolveProductFromRecepcaoItemUseCaseContract::class, ResolveProductFromRecepcaoItemUseCase::class);
        $this->app->bind(ResolveProductFromRecepcaoItemRepositoryContract::class, ResolveProductFromRecepcaoItemRepository::class);
        $this->app->bind(MedicosOptionsRepositoryContract::class, MedicosOptionsRepository::class);

        $this->app->when(ResolveProductFromRecepcaoItemRepository::class)
            ->needs(\Illuminate\Database\ConnectionInterface::class)
            ->give(fn () => DB::connection('mysql_legacy'));

        // Adicionando o canal para logs de produtividade médica
        $this->app->when(ImportMedicalProductivityOrchestratorUseCase::class)
            ->needs(LoggerInterface::class)
            ->give(fn () => Log::channel('produtividade_medica'));
    }

    public function boot(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../../../config/medicos.php', 'medicos');
        $this->loadRoutesFrom(__DIR__ . '/../Interface/routes.php');
    }
}
