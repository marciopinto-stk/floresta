<?php

namespace App\Modules\Medicos\Application\DTO;

final readonly class ExecutorUserDTO
{
    public function __construct(
        public int|string $id,
        public string $name,
        public ?string $email = null,
    ) {}
}
