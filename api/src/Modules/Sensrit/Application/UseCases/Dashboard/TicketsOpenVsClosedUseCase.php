<?php

namespace App\Modules\Sensrit\Application\UseCases\Dashboard;

use MongoDB\BSON\UTCDateTime;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use App\Modules\Sensrit\Infrastructure\Persistence\Mongo\Models\MongoTicketRaw;
use Illuminate\Support\Facades\Log;

class TicketsOpenVsClosedUseCase
{
    private const CREATED_FIELD = 'extracted.created_at';
    private const CLOSED_FIELD  = 'extracted.closed_at';

    public function execute(string $from, string $to, ?int $companyId = null): array
    {
        $fromDate = CarbonImmutable::parse($from)->startOfDay();
        $toDate   = CarbonImmutable::parse($to)->endOfDay();

        // ✅ Mongo precisa de UTCDateTime (BSON)
        $fromBson = new UTCDateTime($fromDate->getTimestamp() * 1000);
        $toBson   = new UTCDateTime($toDate->getTimestamp() * 1000);

        $rawAgg = MongoTicketRaw::raw(function ($collection) use ($fromBson, $toBson, $companyId) {
            $match = [
                '$or' => [
                    [self::CREATED_FIELD => ['$gte' => $fromBson, '$lte' => $toBson]],
                    [self::CLOSED_FIELD  => ['$gte' => $fromBson, '$lte' => $toBson]],
                ],
            ];

            if (!is_null($companyId)) {
                $match['extracted.company_id'] = $companyId;
            }

            return $collection->aggregate([
                ['$match' => $match],
                [
                    '$facet' => [
                        'opened' => [
                            ['$match' => [self::CREATED_FIELD => ['$ne' => null]]],
                            [
                                '$group' => [
                                    '_id' => [
                                        '$dateToString' => [
                                            'format' => '%Y-%m-%d',
                                            'date'   => '$' . self::CREATED_FIELD,
                                        ],
                                    ],
                                    'total' => ['$sum' => 1],
                                ],
                            ],
                        ],
                        'closed' => [
                            ['$match' => [self::CLOSED_FIELD => ['$ne' => null]]],
                            [
                                '$group' => [
                                    '_id' => [
                                        '$dateToString' => [
                                            'format' => '%Y-%m-%d',
                                            'date'   => '$' . self::CLOSED_FIELD,
                                        ],
                                    ],
                                    'total' => ['$sum' => 1],
                                ],
                            ],
                        ],
                    ],
                ],
            ]);
        });

        $first = collect($rawAgg)->first() ?? ['opened' => [], 'closed' => []];

        $openedMap = collect($first['opened'] ?? [])
            ->map(fn ($row) => is_object($row) ? (array) $row : $row)
            ->mapWithKeys(function (array $row) {
                $key = $row['_id'] ?? $row['id'] ?? null;
                if (!$key) return [];
                return [(string) $key => (int) ($row['total'] ?? 0)];
            });

        $closedMap = collect($first['closed'] ?? [])
            ->map(fn ($row) => is_object($row) ? (array) $row : $row)
            ->mapWithKeys(function (array $row) {
                $key = $row['_id'] ?? $row['id'] ?? null;
                if (!$key) return [];
                return [(string) $key => (int) ($row['total'] ?? 0)];
            });


        $days = $this->buildDays($fromDate, $toDate);

        $data = $days->map(function (string $day) use ($openedMap, $closedMap) {
            return [
                'date'   => $day,
                'opened' => (int) ($openedMap[$day] ?? 0),
                'closed' => (int) ($closedMap[$day] ?? 0),
            ];
        })->values();

        return [
            'range' => [
                'from' => $fromDate->toDateString(),
                'to'   => $toDate->toDateString(),
            ],
            'totals' => [
                'opened' => (int) $data->sum('opened'),
                'closed' => (int) $data->sum('closed'),
            ],
            'data' => $data,
        ];
    }

    private function buildDays(CarbonImmutable $from, CarbonImmutable $to): Collection
    {
        $days = [];
        $cursor = $from->startOfDay();
        $end = $to->startOfDay();

        while ($cursor->lte($end)) {
            $days[] = $cursor->toDateString();
            $cursor = $cursor->addDay();
        }

        return collect($days);
    }
}

