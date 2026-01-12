<?php

namespace App\Modules\Sensrit\Application\UseCases\ValidateSensritToken;

class ValidateSensritTokenOutput
{
    public function __construct(
        public bool $hasToken,
        public ?bool $isValid,
        public ?int $statusCode,   
        public ?string $message,
    ) {}
}
