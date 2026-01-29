<?php

namespace App\Modules\Sensrit\Infrastructure\Persistence\Mongo\Repositories;

use App\Modules\Sensrit\Domain\Contracts\Repositories\TicketsDashboardRepositoryContract;
use App\Modules\Sensrit\Infrastructure\Persistence\Mongo\Models\MongoTicketRaw;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use MongoDB\BSON\UTCDateTime;

final class MongoTicketsDashboardRepository implements TicketsDashboardRepositoryContract
{
    public function countOpenByCategory(\Carbon\Carbon $start, \Carbon\Carbon $end): array
    {
        $pipeline = [
            [
                '$match' => [

                    'extracted.created_at' => [
                        '$gte' => $this->toUtc($start),
                        '$lte' => $this->toUtc($end),
                    ],
                ],
            ],
            [
                '$group' => [
                    '_id' => '$extracted.category',
                    'total' => ['$sum' => 1],
                ],
            ],
            [
                '$project' => [
                    '_id' => 0,
                    'key' => [
                        '$ifNull' => ['$_id', 'Sem categoria']
                    ],
                    'label' => [
                        '$ifNull' => ['$_id', 'Sem categoria']
                    ],
                    'total' => 1,
                ],
            ],
            [
                '$sort' => ['total' => -1],
            ],
        ];

        $cursor = MongoTicketRaw::raw(fn ($collection) => $collection->aggregate($pipeline));

        $out = [];
        foreach ($cursor as $row) {
            $out[] = [
                'key'   => (string) ($row['key'] ?? 'Sem categoria'),
                'label' => (string) ($row['label'] ?? 'Sem categoria'),
                'total' => (int) ($row['total'] ?? 0),
            ];
        }

        return $out;
    }

    public function countOpenTotal(Carbon $start, Carbon $end): int
    {
        $pipeline = [
            [
                '$match' => [

                    'extracted.created_at' => [
                        '$gte' => $this->toUtc($start),
                        '$lte' => $this->toUtc($end),
                    ],
                ],
            ],
            ['$count' => 'total'],
        ];

        $cursor = MongoTicketRaw::raw(fn ($collection) => $collection->aggregate($pipeline));

        foreach ($cursor as $row) {
            return (int) ($row['total'] ?? 0);
        }

        return 0;
    }

    public function countOpenedByDay(CarbonImmutable $start, CarbonImmutable $end): array
    {
        $pipeline = [
            [
                '$match' => [

                    'extracted.created_at' => [
                        '$gte' => $this->toUtc($start),
                        '$lt'  => $this->toUtc($end),
                    ],
                ],
            ],
            [
                '$group' => [
                    '_id' => [
                        'year'  => ['$year' => '$extracted.created_at'],
                        'month' => ['$month' => '$extracted.created_at'],
                        'day'   => ['$dayOfMonth' => '$extracted.created_at'],
                    ],
                    'total' => ['$sum' => 1],
                ],
            ],
            [
                '$project' => [
                    '_id' => 0,
                    'date' => [
                        '$dateFromParts' => [
                            'year'  => '$_id.year',
                            'month' => '$_id.month',
                            'day'   => '$_id.day',
                        ],
                    ],
                    'total' => 1,
                ],
            ],
            [
                '$sort' => ['date' => 1],
            ],
        ];

        $cursor = MongoTicketRaw::raw(fn ($collection) => $collection->aggregate($pipeline));

        $out = [];
        foreach ($cursor as $row) {
            $date = isset($row['date']) && $row['date'] instanceof UTCDateTime
                ? CarbonImmutable::createFromTimestampMs($row['date']->toDateTime()->getTimestamp() * 1000)
                : null;

            if ($date) {
                $out[] = [
                    'date'  => $date->toDateString(),
                    'total' => (int) ($row['total'] ?? 0),
                ];
            }
        }

        return $out;
    }

    private function toUtc(Carbon $date): UTCDateTime
    {
        return new UTCDateTime($date->getTimestamp() * 1000);
    }
}
