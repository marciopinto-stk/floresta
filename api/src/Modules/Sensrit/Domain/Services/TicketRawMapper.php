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

        $createdRaw = $raw['dt_cad'] ?? $raw['grid_date'] ?? $raw['dt_cad_request'] ?? null;
        $updatedRaw = $raw['dt_up'] ?? null;
        $closedRaw  = $raw['grid_date_f'] ?? null; // confirmar semântica depois

        $slaDueRaw = $raw['grid_sla_time'] ?? $raw['sla'] ?? null;

        $statusRaw    = $raw['status'] ?? null;
        $waitingRaw   = $raw['grid_waiting'] ?? null;
        $reasonStatus = $raw['grid_reason_status'] ?? null;

        $reopenedCount = isset($raw['grid_reopened_count']) ? (int) $raw['grid_reopened_count'] : 0;

        $statusNorm = $this->normalizeStatus($statusRaw, $waitingRaw, $reasonStatus);
        $isClosed   = $this->isClosed($statusNorm, $closedRaw);

        $createdAt = $this->toUtcDate($createdRaw);
        $updatedAt = $this->toUtcDate($updatedRaw);
        $closedAt  = $this->toUtcDate($closedRaw);
        $slaDueAt  = $this->toUtcDate($slaDueRaw);

        $resolutionSeconds = ($createdAt && $closedAt)
            ? $closedAt->getTimestamp() - $createdAt->getTimestamp()
            : null;

        // grid_time_spent vem como "HH:MM:SS"
        $timeSpentSeconds = $this->hmsToSeconds($raw['grid_time_spent'] ?? null);

        // stop_time parece ser segundos
        $stopTimeSeconds = isset($raw['stop_time']) ? (int) $raw['stop_time'] : null;

        $extracted = [
            // ids
            'ticket_id' => isset($raw['id_tickets']) ? (int) $raw['id_tickets'] : null,
            'human_id'  => $raw['grid_id'] ?? $raw['real_id'] ?? null,

            // empresa/tenant
            'company_id'   => isset($raw['fk_id_company']) ? (int) $raw['fk_id_company'] : null,
            'company_name' => $raw['grid_company'] ?? null,

            // pessoas/grupos
            'technician' => $raw['grid_service_technician'] ?? $raw['nametech'] ?? null,
            'tech_group' => $raw['grid_tech_group'] ?? null,

            // assunto
            'subject' => $raw['grid_subject'] ?? null,

            // status
            'status_raw'    => $statusRaw,
            'waiting'       => $waitingRaw,
            'status_reason' => $reasonStatus,
            'status_norm'   => $statusNorm,
            'is_closed'     => $isClosed,
            'reopened_count'=> $reopenedCount,

            // datas (strings para cursor/log)
            'created_at_external' => $this->toIso($createdRaw),
            'updated_at_external' => $this->toIso($updatedRaw),
            'closed_at_external'  => $this->toIso($closedRaw),

            // datas (Date type para analytics)
            'created_at' => $createdAt,
            'updated_at' => $updatedAt,
            'closed_at'  => $closedAt,

            // SLA
            'sla_due_at'           => $slaDueAt,
            'sla_duration_minutes' => isset($raw['sla_duration']) ? (int) $raw['sla_duration'] : null,
            'is_sla_breached'      => ($slaDueAt && $closedAt) ? $closedAt > $slaDueAt : null,

            // tempos
            'resolution_time_seconds' => $resolutionSeconds,
            'time_spent_seconds'      => $timeSpentSeconds,
            'stop_time_seconds'       => $stopTimeSeconds,

            // dimensões
            'priority_id'   => isset($raw['fk_id_priority']) ? (int) $raw['fk_id_priority'] : null,
            'urgency_id'    => isset($raw['fk_id_urgency']) ? (int) $raw['fk_id_urgency'] : null,
            'impact_id'     => isset($raw['fk_id_impact']) ? (int) $raw['fk_id_impact'] : null,
            'complexity_id' => isset($raw['fk_id_complexity']) ? (int) $raw['fk_id_complexity'] : null,

            'category' => $raw['grid_category'] ?? null,
            'service'  => $raw['grid_catalog_service'] ?? null,
            'task'     => $raw['grid_catalog_task'] ?? null,

            // tags
            'tags' => is_array($raw['grid_tags'] ?? null) ? array_values($raw['grid_tags']) : [],
            'tags_string' => $raw['tags_string'] ?? null,
        ];

        return [
            'payload'   => $payload,
            'extracted' => $extracted,
        ];
    }

    private function toIso(mixed $value): ?string
    {
        $dt = $this->toUtcDate($value);
        return $dt ? $dt->toIso8601String() : null;
    }

    private function toUtcDate(mixed $value): ?Carbon
    {
        if (!$value || (!is_string($value) && !($value instanceof \DateTimeInterface))) {
            return null;
        }

        try {
            return Carbon::parse($value)->utc();
        } catch (\Throwable) {
            return null;
        }
    }

    private function normalizeStatus(?string $status, ?string $waiting, ?string $reason): string
    {
        $v = strtolower(trim($status ?? $waiting ?? $reason ?? ''));

        if ($v === '') return 'unknown';

        // Ajuste conforme a taxonomia de vocês:
        if (str_contains($v, 'resolved')) return 'resolved';
        if (str_contains($v, 'closed')) return 'closed';
        if (str_contains($v, 'cancel')) return 'canceled';
        if (str_contains($v, 'in progress') || str_contains($v, 'em atendimento')) return 'in_progress';
        if (str_contains($v, 'aguard') || str_contains($v, 'waiting')) return 'waiting';

        return 'open';
    }

    private function isClosed(string $statusNorm, mixed $closedRaw): bool
    {
        if ($this->toUtcDate($closedRaw)) return true;
        return in_array($statusNorm, ['resolved', 'closed'], true);
    }

    private function hmsToSeconds(mixed $value): ?int
    {
        if (!$value || !is_string($value)) return null;

        $parts = explode(':', $value);
        if (count($parts) !== 3) return null;

        [$h, $m, $s] = $parts;
        if (!is_numeric($h) || !is_numeric($m) || !is_numeric($s)) return null;

        return ((int)$h * 3600) + ((int)$m * 60) + (int)$s;
    }
}
