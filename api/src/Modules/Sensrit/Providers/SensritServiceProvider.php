<?php

namespace App\Modules\Sensrit\Providers;

use App\Modules\Sensrit\Domain\Contracts\Clients\SensritTicketsClientContract;
use App\Modules\Sensrit\Domain\Contracts\Repositories\SensritTokenRepositoryContract;
use App\Modules\Sensrit\Domain\Contracts\Repositories\SyncStateRepositoryContract;
use App\Modules\Sensrit\Domain\Contracts\Repositories\TicketRawRepositoryContract;
use App\Modules\Sensrit\Domain\Contracts\Repositories\TicketsDashboardRepositoryContract;
use App\Modules\Sensrit\Infrastructure\Clients\Http\SensritTicketsHttpClient;
use App\Modules\Sensrit\Infrastructure\Persistence\Mongo\Repositories\MongoSensritTokenRepository;
use App\Modules\Sensrit\Infrastructure\Persistence\Mongo\Repositories\MongoSyncStateRepository;
use App\Modules\Sensrit\Infrastructure\Persistence\Mongo\Repositories\MongoTicketRawRepository;
use App\Modules\Sensrit\Infrastructure\Persistence\Mongo\Repositories\MongoTicketsDashboardRepository;
use App\Modules\Sensrit\Interface\Console\Commands\RebuildTicketsExtractedCommand;
use App\Modules\Sensrit\Interface\Console\Commands\SyncSensritTicketsRawCommand;
use Illuminate\Support\ServiceProvider;

class SensritServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Clients
        $this->app->bind(SensritTicketsClientContract::class, SensritTicketsHttpClient::class);

        // Repositories
        $this->app->bind(TicketRawRepositoryContract::class, MongoTicketRawRepository::class);
        $this->app->bind(SyncStateRepositoryContract::class, MongoSyncStateRepository::class);
        $this->app->bind(SensritTokenRepositoryContract::class, MongoSensritTokenRepository::class);
        $this->app->bind(TicketsDashboardRepositoryContract::class, MongoTicketsDashboardRepository::class);
    }

    public function boot(): void
    {
        if (file_exists(__DIR__ . '/../Interface/Http/routes.php')) {
            $this->loadRoutesFrom(__DIR__ . '/../Interface/Http/routes.php');
        }

        // Commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                SyncSensritTicketsRawCommand::class,
                RebuildTicketsExtractedCommand::class,
            ]);
        }
    }
}
