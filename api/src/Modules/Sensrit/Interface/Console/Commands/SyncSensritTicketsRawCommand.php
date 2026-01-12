<?php

namespace App\Modules\Sensrit\Interface\Console\Commands;

use App\Modules\Sensrit\Application\UseCases\SyncTicketsRaw\SyncTicketsRawInput;
use App\Modules\Sensrit\Application\UseCases\SyncTicketsRaw\SyncTicketsRawUseCase;
use Illuminate\Console\Command;

class SyncSensritTicketsRawCommand extends Command
{
    protected $signature = 'sensrit:tickets:sync
        {--since= : Data/hora ISO para buscar atualizações (ex: 2025-12-01T00:00:00Z)}
        {--limit= : Limite de itens (útil em dev)}
        {--dry-run : Não persiste nada, apenas simula}
    ';

    protected $description = 'Sincroniza tickets brutos do Sensrit para o MongoDB';

    public function handle(SyncTicketsRawUseCase $useCase): int
    {
        $input = new SyncTicketsRawInput(
            since: $this->option('since'),
            limit: $this->option('limit') ? (int) $this->option('limit') : null,
            dryRun: (bool) $this->option('dry-run')
        );

        $output = $useCase->execute($input);

        $this->info("Sensrit tickets raw sync concluído.");
        $this->line("Novos: {$output->created}");
        $this->line("Atualizados: {$output->updated}");
        $this->line("Ignorados: {$output->ignored}");
        $this->line("Cursor final (dt_up): " . ($output->cursor ?? '-'));

        return self::SUCCESS;
    }
}
