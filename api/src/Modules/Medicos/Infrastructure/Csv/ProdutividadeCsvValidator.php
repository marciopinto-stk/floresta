<?php
namespace App\Modules\Medicos\Infrastructure\Csv;

use Illuminate\Http\UploadedFile;

final class ProdutividadeCsvValidator
{
    private const DELIMITER = ';';

    /** Colunas obrigatórias */
    private const REQUIRED_COLUMNS = [
        'DataSolicitacao',
        'Profissional',
        'AccessionNumber',
        'DataLaudo',
        'ProfissionalIdS2',
        'NomeProcedimento',
        'Status'
    ];

    public function validate(UploadedFile $file): array
    {
        $path = $file->getRealPath();

        if (!$path || !is_file($path)) {
            return $this->fail('Não foi possível ler o arquivo enviado.');
        }

        $handle = fopen($path, 'r');
        if (!$handle) {
            return $this->fail('Falha ao abrir o arquivo CSV.');
        }

        try {
            // Lê a primeira linha (header)
            $headerRaw = fgetcsv($handle, 0, self::DELIMITER);

            if (!$headerRaw || count($headerRaw) < 2) {
                // as vezes pode vir separado por virgula, então a linha vira 1 coluna só
                return $this->fail('Cabeçalho inválido. Verifique se o arquivo está separado por ";" e não por ",".');
            }

            $header     = $this->normalizeHeader($headerRaw);
            $missing    = array_values(array_diff(self::REQUIRED_COLUMNS, $header));
            $extra      = array_values(array_diff($header, self::REQUIRED_COLUMNS));

            if (!empty($missing)) {
                return [
                    'ok'                => false,
                    'message'           => 'CSV inválido: colunas obrigatórias ausentes.',
                    'meta'              => $this->fileMeta($file),
                    'header'            => $header,
                    'missing_columns'   => $missing,
                    'extra_columns'     => $extra,
                ];
            }

            $preview    = [];
            $line       = 1;
            $maxPreview = 10;

            while (($row = fgetcsv($handle, 0, self::DELIMITER)) !== false && count($preview) < $maxPreview) {
                $line++;
                if ($this->isEmptyRow($row)) {
                    continue;
                }

                $assoc = $this->rowToAssoc($header, $row);
                $preview[] = [
                    'line'              => $line,
                    'AccessionNumber'   => $assoc['AccessionNumber'] ?? null,
                    'ProfissionalIdS2'  => $assoc['ProfissionalIdS2'] ?? null,
                    'DataLaudo'         => $assoc['DataLaudo'] ?? null,
                    'Status'            => $assoc['Status'] ?? null,
                ];
            }

            return [
                'ok'                => true,
                'message'           => 'CSV válido (estrutura).',
                'meta'              => $this->fileMeta($file),
                'header'            => $header,
                'missing_columns'   => [],
                'extra_columns'     => $extra,
                'preview'           => $preview,
            ];
        } finally {
            fclose($handle);
        }
    }

    private function normalizeHeader(array $headerRaw): array
    {
        $normalized = array_map(function ($h) {
            $h = (string) $h;
            $h = $this->stripBom($h);
            return trim($h);
        }, $headerRaw);

        // Remove colunas vazias acidentais
        $normalized = array_values(array_filter($normalized, fn($h) => $h !== ''));

        return $normalized;
    }

    private function stripBom(string $value): string
    {
        return preg_replace('/^\xEF\xBB\xBF/', '', $value) ?? $value; // UTF-8 BOM: EF BB BF
    }

    private function rowToAssoc(array $header, array $row): array
    {
        $assoc = [];
        foreach ($header as $i => $key) {
            $assoc[$key] = isset($row[$i]) ? trim((string)$row[$i]) : null;
        }

        return $assoc;
    }

    private function isEmptyRow(array $row): bool
    {
        foreach ($row as $cell) {
            if (trim((string)$cell) !== '') {
                return false;
            }
        }
        return true;
    }

    private function fileMeta(UploadedFile $file): array
    {
        return [
            'original_name' => $file->getClientOriginalName(),
            'mime'          => $file->getClientMimeType(),
            'size'          => $file->getSize(),
        ];
    }

    private function fail(string $message): array
    {
        return [
            'ok'        => false,
            'message'   => $message,
        ];
    }
}
