<?php

namespace App\Modules\Sensrit\Interface\Console\Commands;

use App\Modules\Sensrit\Domain\Services\TicketRawMapper;
use App\Modules\Sensrit\Infrastructure\Persistence\Mongo\Models\MongoTicketRaw;
use DateTimeInterface;
use Illuminate\Console\Command;

class RebuildTicketsExtractedCommand extends Command
{
    protected $signature = 'sensrit:tickets:rebuild-extracted
        {--companyId= : Filtra por extracted.company_id}
        {--only-missing : Rebuild somente se extracted.created_at (Date) estiver ausente}
        {--force : Rebuild sempre, mesmo se já existir}
        {--limit=0 : Limita quantidade de documentos (0 = sem limite)}
        {--chunk=500 : Tamanho do chunk para processamento}
        {--dry-run : Não grava nada, apenas simula}
    ';

    protected $description = 'Rebuild do campo extracted para todos os documentos em sensrit_tickets_raw, a partir do payload';

    public function handle(TicketRawMapper $mapper): int
    {
        $companyId    = $this->option('companyId');
        $onlyMissing  = (bool) $this->option('only-missing');
        $force        = (bool) $this->option('force');
        $limit        = (int) $this->option('limit');
        $chunk        = max(1, (int) $this->option('chunk'));
        $dryRun       = (bool) $this->option('dry-run');

        if ($onlyMissing && $force) {
            $this->error('Use apenas um: --only-missing OU --force');
            return self::FAILURE;
        }

        $query = MongoTicketRaw::query();

        if ($companyId !== null && $companyId !== '') {
            $query->where('extracted.company_id', (int) $companyId);
        }

        if ($onlyMissing) {
            $query->where(function ($q) {
                $q->whereNull('extracted.created_at')
                  ->orWhere('extracted.created_at', 'exists', false);
            });
        }

        if ($limit > 0) {
            $query->limit($limit);
        }

        $total = $query->count();

        if ($total === 0) {
            $this->info('Nenhum documento encontrado para processar.');
            return self::SUCCESS;
        }

        $this->info("Documentos a processar: {$total}");
        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $processed  = 0;
        $updated    = 0;
        $skipped    = 0;
        $failed     = 0;
        $cursor     = $query->orderBy('_id')->cursor();
        $batch      = [];

        foreach ($cursor as $doc) {
            $processed++;

            try {
                $payload = (array) ($doc->payload ?? []);

                if (empty($payload)) {
                    $skipped++;
                    $bar->advance();
                    continue;
                }

                if (!$force && !$onlyMissing) {
                    $currentCreatedAt = data_get($doc, 'extracted.created_at');
                    if ($currentCreatedAt instanceof DateTimeInterface || $currentCreatedAt instanceof UTCDateTime) {
                        $skipped++;
                        $bar->advance();
                        continue;
                    }
                }

                $mapped = $mapper->map($payload);
                $newExtracted = $mapped['extracted'] ?? [];

                $batch[] = [
                    'updateOne' => [
                        ['_id' => $doc->_id],
                        ['$set' => [
                            'extracted' => $newExtracted,
                            'updated_at' => now(),
                        ]],
                    ],
                ];

                if (count($batch) >= $chunk) {
                    $updated += $this->flushBatch($batch, $dryRun);
                    $batch = [];
                }
            } catch (\Throwable $e) {
                $failed++;

                $this->newLine();
                $this->error("Falhou doc _id={$doc->_id}: {$e->getMessage()}");
            }

            $bar->advance();
        }

        if (count($batch) > 0) {
            $updated += $this->flushBatch($batch, $dryRun);
        }

        $bar->finish();
        $this->newLine();

        $this->info("Processados: {$processed}");
        $this->info("Atualizados: {$updated}" . ($dryRun ? " (dry-run)" : ""));
        $this->info("Ignorados:   {$skipped}");
        $this->info("Falhas:      {$failed}");

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }

    /**
     * @param array<int, array<string, mixed>> $batch
     */
    private function flushBatch(array $batch, bool $dryRun): int
    {
        if ($dryRun) {
            return count($batch);
        }

        $result = MongoTicketRaw::raw(function ($collection) use ($batch) {
            return $collection->bulkWrite($batch);
        });

        if (is_object($result) && method_exists($result, 'getModifiedCount')) {
            return (int) $result->getModifiedCount();
        }

        return count($batch);
    }
}
