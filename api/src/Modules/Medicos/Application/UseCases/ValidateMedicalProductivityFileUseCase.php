<?php

namespace App\Modules\Medicos\Application\UseCases;

use App\Modules\Medicos\Application\DTO\ImportMedicalProductivityInputDTO;
use App\Modules\Medicos\Domain\Contracts\ValidateMedicalProductivityFileUseCaseContract;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;

final class ValidateMedicalProductivityFileUseCase implements ValidateMedicalProductivityFileUseCaseContract
{
    public function handle(ImportMedicalProductivityInputDTO $input): void
    {
        $file = $input->file ?? null;

        if (!$file instanceof UploadedFile) {
            throw ValidationException::withMessages([
                'file' => ['Arquivo inválido: nenhum arquivo foi enviado.'],
            ]);
        }

        if (!$file->isValid()) {
            throw ValidationException::withMessages([
                'file' => ['The file failed to upload'],
            ]);
        }

        $this->validateExtensionAndMime($file);
        $this->validateMaxSize($file);
        $this->validateNotEmpty($file);
        $this->validateRequiredHeaders($file);
    }

    private function validateExtensionAndMime(UploadedFile $file): void
    {
        $ext    = strtolower((string) $file->getClientOriginalExtension());
        $mime   = strtolower((string) $file->getMimeType());

        // CSV costuma variar (dependendo do SO/cliente):
        // - text/csv (ideal)
        // - text/plain
        // - application/vnd.ms-excel (comum em uploads do Windows/Excel)
        $allowedMimes = ['text/csv', 'text/plain', 'application/vnd.ms-excel'];

        if ($ext !== 'csv') {
            throw ValidationException::withMessages([
                'file' => ['Arquivo inválido: envie um arquivo .csv.'],
            ]);
        }

        // caso seja enviado o mime vazio, não vamos bloquear
        if ($mime !== '' && !in_array($mime, $allowedMimes, true)) {
            throw ValidationException::withMessages([
                'file' => ['Arquivo inválido: tipo MIME não suportado para CSV.'],
            ]);
        }
    }

    private function validateMaxSize(UploadedFile $file): void
    {
        $maxKb      = (int) config('medicos.productivity.max_upload_kb', 2048);
        $sizeBytes  = (int) $file->getSize();
        $sizeKb     = (int) ceil($sizeBytes / 1024);

        if ($sizeBytes > 0 && $sizeKb > $maxKb) {
            throw ValidationException::withMessages([
                'file' => ["Arquivo excede o tamanho máximo permitido ({$maxKb}KB)."],
            ]);
        }
    }

    private function validateNotEmpty(UploadedFile $file): void
    {
        if ((int) $file->getSize() === 0) {
            throw ValidationException::withMessages([
                'file' => ['Arquivo vazio: o CSV não possui conteúdo.'],
            ]);
        }

        $handle = fopen($file->getRealPath(), 'rb');
        if ($handle === false) {
            throw ValidationException::withMessages([
                'file' => ['Não foi possível ler o arquivo enviado.'],
            ]);
        }

        $firstLine = fgets($handle);
        fclose($handle);

        if ($firstLine === false || trim($firstLine) === '') {
            throw ValidationException::withMessages([
                'file' => ['Arquivo vazio: o CSV não possui cabeçalho.'],
            ]);
        }
    }

    private function validateRequiredHeaders(UploadedFile $file): void
    {
        $delimiter = (string) config('medicos.productivity.delimiter', ';');

        // Pelo UC, essas são as mínimas para processar produtividade
        $requiredHeaders = (array) config('medicos.productivity.required_headers', [
            'AccessionNumber',
            'DataLaudo',
            'ProfissionalIdS2',
        ]);

        $fh = fopen($file->getRealPath(), 'rb');
        if ($fh === false) {
            throw ValidationException::withMessages([
                'file' => ['Não foi possível ler o arquivo enviado.'],
            ]);
        }

        $header = fgetcsv($fh, 0, $delimiter);
        fclose($fh);

        if (!is_array($header) || $header === []) {
            throw ValidationException::withMessages([
                'file' => ['Arquivo inválido: cabeçalho CSV ausente ou malformado.'],
            ]);
        }

        // Normaliza: trim + remove BOM UTF-8 do primeiro campo
        $header = array_map(static fn ($v) => trim((string) $v), $header);
        if (isset($header[0])) {
            $header[0] = preg_replace('/^\xEF\xBB\xBF/', '', $header[0]) ?? $header[0];
        }

        $missing = [];
        foreach ($requiredHeaders as $col) {
            if (!in_array($col, $header, true)) {
                $missing[] = $col;
            }
        }

        if ($missing !== []) {
            throw ValidationException::withMessages([
                'file' => ['Arquivo inválido: colunas obrigatórias ausentes: ' . implode(', ', $missing) . '.'],
            ]);
        }
    }
}
