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
        $path = $input->uploadedFilePath ?? null;

        if (!is_string($path) || trim($path) === '') {
            throw ValidationException::withMessages([
                'file' => ['Arquivo inválido: caminho do arquivo ausente.'],
            ]);
        }

        if (!file_exists($path) || !is_file($path)) {
            throw ValidationException::withMessages([
                'file' => ['Arquivo inválido: nenhum arquivo foi enviado.'],
            ]);
        }

        $this->validateExtension($path);
        $this->validateMaxSize($path);
        $this->validateNotEmpty($path);
        $this->validateRequiredHeaders($path);
    }

    private function validateExtension(string $path): void
    {
        $ext    = strtolower((string) pathinfo($path, PATHINFO_EXTENSION));

        if (!in_array($ext, ['csv', 'txt'], true)) {
            throw ValidationException::withMessages([
                'file' => ['Arquivo inválido: envie um arquivo .csv.'],
            ]);
        }
    }

    private function validateMaxSize(string $path): void
    {
        $maxKb      = (int) config('medicos.productivity.max_upload_kb', 20480);
        $sizeBytes  = filesize($path);

        if ($sizeBytes === false) {
            throw ValidationException::withMessages([
                'file' => ["Não foi possível ler o tamanho do arquivo."],
            ]);
        }

        $sizeKb = (int) ceil($sizeBytes / 1024);

        if ($sizeBytes > 0 && $sizeKb > $maxKb) {
            throw ValidationException::withMessages([
                'file' => ["Arquivo excede o tamanho máximo permitido ({$maxKb}KB)."],
            ]);
        }
    }

    private function validateNotEmpty(string $path): void
    {
        $size = filesize($path);

        if ($size === false || (int) $size === 0) {
            throw ValidationException::withMessages([
                'file' => ['Arquivo vazio: o CSV não possui conteúdo.'],
            ]);
        }

        $handle = fopen($path, 'rb');
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

    private function validateRequiredHeaders(string $path): void
    {
        $delimiter = (string) config('medicos.productivity.delimiter', ';');

        $requiredHeaders = (array) config('medicos.productivity.required_headers', [
            'AccessionNumber',
            'DataLaudo',
            'ProfissionalIdS2',
        ]);

        $fh = fopen($path, 'rb');
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
