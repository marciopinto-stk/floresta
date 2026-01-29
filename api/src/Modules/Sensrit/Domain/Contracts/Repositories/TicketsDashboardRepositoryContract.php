<?php

namespace App\Modules\Sensrit\Domain\Contracts\Repositories;

use Carbon\Carbon;

interface TicketsDashboardRepositoryContract
{
    public function countOpenByCategory(Carbon $start, Carbon $end): array;

    public function countOpenTotal(Carbon $start, Carbon $end): int;
}
