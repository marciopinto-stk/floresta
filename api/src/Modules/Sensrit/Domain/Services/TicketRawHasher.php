<?php

namespace App\Modules\Sensrit\Domain\Services;

class TicketRawHasher
{
    public function hash(array $payload): string
    {
        // Canoniza o payload pra hash estável (ordena chaves recursivamente)
        $normalized = $this->ksortRecursive($payload);

        // JSON estável
        $json = json_encode($normalized, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        return hash('sha256', $json ?: '');
    }

    private function ksortRecursive(array $data): array
    {
        foreach ($data as $k => $v) {
            if (is_array($v)) {
                $data[$k] = $this->ksortRecursive($v);
            }
        }

        ksort($data);

        return $data;
    }
}
