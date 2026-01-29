<?php

namespace App\Modules\Sensrit\Application\UseCases\Dashboard;

use App\Modules\Sensrit\Domain\Contracts\Repositories\TicketsDashboardRepositoryContract;
use Carbon\Carbon;

class TicketsOpenByCategoryUseCase
{

    public function __construct(
        private readonly TicketsDashboardRepositoryContract $repository
    ) {}

    public function execute(?int $month = null): array
    {
        $month  = $month ?: (int) now()->format('n');
        $year   = (int) now()->format('Y');

        $start  = Carbon::create($year, $month, 1)->startOfMonth();
        $end    = (clone $start)->endOfMonth();

        $prevStart  = (clone $start)->subMonth()->startOfMonth();
        $prevEnd    = (clone $start)->subMonth()->endOfMonth();

        $categories = $this->repository->countOpenByCategory($start, $end);
        $total      = array_sum(array_map(fn ($row) => (int) $row['total'], $categories));

        $previousTotal = $this->repository->countOpenTotal($prevStart, $prevEnd);

        $changePct = 0.0;
        if ($previousTotal > 0) {
            $changePct = ($total - $previousTotal) / $previousTotal;
        }

        return [
            'month'         => $start->format('Y-m'),
            'total'         => $total,
            'changePct'     => round($changePct, 4),
            'previousTotal' => $previousTotal,
            'categories'    => $categories,
        ];
    }
}
