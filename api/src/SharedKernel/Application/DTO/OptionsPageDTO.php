<?php

namespace App\SharedKernel\Application\DTO;

final class OptionsPageDTO
{
    public function __construct(
        public readonly array $data,
        public readonly int $page,
        public readonly int $limit,
        public readonly int $total,
    ) {}

    public function hasNext(): bool
    {
        return ($this->page * $this->limit) < $this->total;
    }

    public function toArray(): array
    {
        return [
            'data' => $this->data,
            'pagination' => [
                'page'      => $this->page,
                'limit'     => $this->limit,
                'total'     => $this->total,
                'hasNext'   => $this->hasNext(),
            ],
        ];
    }
}
