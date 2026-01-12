<?php

namespace App\Modules\Sensrit\Domain\Services;

use Carbon\Carbon;

class TicketRawMapper
{
    /**
     * @param array<string,mixed> $raw
     * @return array{payload: array, extracted: array}
     */
    public function map(array $raw): array
    {
        $payload = $raw;

        $extracted = [
            'ticket_id'     => isset($raw['id_tickets']) ? (int) $raw['id_tickets'] : null,
            'human_id'      => $raw['grid_id'] ?? $raw['real_id'] ?? null,
            'status'        => $raw['status'] ?? $raw['grid_waiting'] ?? $raw['grid_reason_status'] ?? null,
            'reason_status' => $raw['grid_reason_status'] ?? null,

            'technician' => $raw['grid_service_technician'] ?? null,
            'tech_group' => $raw['grid_tech_group'] ?? null,

            'company_id'    => isset($raw['fk_id_company']) ? (int) $raw['fk_id_company'] : null,
            'company_name' => $raw['grid_company'] ?? null,

            'subject' => $raw['grid_subject'] ?? null,

            // Datas externas (ISO quando possível)
            'created_at_external'   => $this->toIso($raw['dt_cad'] ?? $raw['grid_date'] ?? $raw['dt_cad_request'] ?? null),
            'updated_at_external'   => $this->toIso($raw['dt_up'] ?? null),
            'closed_at_external'    => $this->toIso($raw['grid_date_f'] ?? null),
        ];

        return [
            'payload'   => $payload,
            'extracted' => $extracted,
        ];
    }

    private function toIso(mixed $value): ?string
    {
        if (!$value || !is_string($value)) {
            return null;
        }

        try {
            // Aceita "2025-12-16T11:40:58.000Z" e "2025-08-12 14:50:20"
            return Carbon::parse($value)->utc()->toIso8601String();
        } catch (\Throwable) {
            return null;
        }
    }
}
