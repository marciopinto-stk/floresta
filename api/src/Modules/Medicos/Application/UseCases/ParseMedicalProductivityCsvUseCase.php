<?php

namespace App\Modules\Medicos\Application\UseCases;

use App\Modules\Medicos\Application\DTO\ImportMedicalProductivityInputDTO;
use App\Modules\Medicos\Application\DTO\ProductivityRowDTO;
use App\Modules\Medicos\Application\DTO\ProductivityRowErrorDTO;
use App\Modules\Medicos\Domain\Contracts\ParseMedicalProductivityCsvUseCaseContract;
use Carbon\CarbonImmutable;
use Generator;
use RuntimeException;
use SplFileObject;
use Throwable;

final class ParseMedicalProductivityCsvUseCase implements ParseMedicalProductivityCsvUseCaseContract
{
    private const HEADER_ALIASES = [
        // possíveis nomes do CSV -> canônico interno
        'accessionnumber'  => 'id_recepcao_item',
        'id_recepcao_item' => 'id_recepcao_item',

        'datalaudo'        => 'data',
        'datalaudo/hora'   => 'data',
        'data'             => 'data',

        'profissionalids2' => 'medico',
        'usuario'          => 'medico',
        'medico'           => 'medico',
    ];

    public function handle(ImportMedicalProductivityInputDTO $input, ?callable $onRowError = null): iterable
    {
        return $this->stream($input, $onRowError);
    }

    private function stream(ImportMedicalProductivityInputDTO $input, ?callable $onRowError): Generator
    {
        $path = $input->uploadedFilePath;

        if (!is_string($path) || $path === '' || !is_file($path) || !is_readable($path)) {
            $this->reportError(
                $onRowError,
                new ProductivityRowErrorDTO(
                    line: 0,
                    message: 'Arquivo CSV não encontrado ou não é legível.',
                    context: ['path' => $path]
                )
            );
            return;
        }

        $file = new SplFileObject($path, 'r');
        $file->setFlags(
            SplFileObject::READ_CSV
            | SplFileObject::SKIP_EMPTY
            | SplFileObject::DROP_NEW_LINE
        );

        $file->setCSVControl(';');
        $lineNumber = 0;

        // header
        $headerRow = $file->fgetcsv();
        $lineNumber++;

        if (!is_array($headerRow) || $this->isEmptyRow($headerRow)) {
            $this->reportError(
                $onRowError,
                new ProductivityRowErrorDTO($lineNumber, 'CSV sem header ou header vazio.')
            );

            return;
        }

        $map = $this->buildHeaderMap($headerRow);
        if (!$map['id_recepcao_item'] || !$map['data'] || !$map['medico']) {
            $this->reportError(
                $onRowError,
                new ProductivityRowErrorDTO(
                    $lineNumber,
                    'Header inválido: campos obrigatórios ausentes (id_recepcao_item/accessionnumber, data/datalaudo, usuario/profissionalids2).',
                    ['header' => $headerRow]
                )
            );

            return;
        }

        // data rows
        while (!$file->eof()) {
            $row = $file->fgetcsv();
            $lineNumber++;

            if(!is_array($row) || $this->isEmptyRow($row)) {
                continue;
            }

            try {
                $rawAssoc = $this->assocRow($row, $map['raw_keys']);

                $idRecepcaoItem = $this->stringOrNull($row[$map['id_recepcao_item']] ?? null);
                $dataRaw        = $this->stringOrNull($row[$map['data']] ?? null);
                $medicoRaw     = $this->stringOrNull($row[$map['medico']] ?? null);

                // validação mínima (campos obrigatorios)
                if (!$idRecepcaoItem || !$dataRaw || $medicoRaw) {
                    $this->reportError(
                        $onRowError,
                        new ProductivityRowErrorDTO(
                            $lineNumber,
                            'Campos obrigatórios ausentes na linha.',
                            [
                                'id_recepcaro_item' => $idRecepcaoItem,
                                'data'              => $dataRaw,
                                'medico'            => $medicoRaw
                            ],
                            $rawAssoc
                        ),
                    );
                    continue;
                }

                $dataMySql = $this->normalizeDateToMySQl($dataRaw);
                if ($dataMySql === null) {
                    $this->reportError(
                        $onRowError,
                        new ProductivityRowErrorDTO(
                            $lineNumber,
                            'Data inválida (não foi possível normalizar para MySQL).',
                            ['data_raw' => $dataRaw],
                            $rawAssoc
                        )
                    );
                    continue;
                }

                $medico = $this->normalizeMedico($medicoRaw);
                if ($medico === null) {
                    $this->reportError(
                        $onRowError,
                        new ProductivityRowErrorDTO(
                            $lineNumber,
                            'Usuário inválido após normalização.',
                            ['medico_raw' => $medicoRaw],
                            $rawAssoc
                        )
                    );
                    continue;
                }

                yield new ProductivityRowDTO(
                    line: $lineNumber,
                    idRecepcaoItem: $idRecepcaoItem,
                    data: $dataMySql,
                    medico: $medico,
                    raw: $rawAssoc
                );
            } catch (Throwable $e)
            {
                $this->reportError(
                    $onRowError,
                    new ProductivityRowErrorDTO(
                        $lineNumber,
                        'Erro inesperado ao processar linha.',
                        ['exception' => $e->getMessage()],
                        ['row' => $row]
                    )
                );
                continue;
            }
        }
    }

    private function buildHeaderMap(array $headerRow): array
    {
        $rawKeys = [];
        $indexes = [
            'id_recepcao_items' => null,
            'data'              => null,
            'medico'            => null,
        ];

        foreach ($headerRow as $i => $col) {
            $key = $this->normalizeHeaderKey((string) $col);
            $rawKey[$i] = $key;

            $canonical = self::HEADER_ALIASES[$key] ?? null;
            if ($canonical && array_key_exists($canonical, $indexes) && $indexes[$canonical] === null) {
                $indexes[$canonical] = $i;
            }
        }

        return [
            ...$indexes,
            'raw_keys' => $rawKeys,
        ];
    }

    private function normalizeHeaderKey(string $value): string
    {
        $v = trim($value);
        $v = str_replace(["\xEF\xBB\xBF"], '', $v); // remove BOM
        $v = mb_strtolower($v);
        $v = preg_replace('/\s+/', '', $v) ?? $v;

        return $v;
    }

    private function assocRow(array $row, array $rawKeys): array
    {
        $assoc = [];
        foreach ($row as $i => $value) {
            $k = $rawKeys[$i] ?? (string) $i;
            $assoc[$k] = $value;
        }
        return $assoc;
    }

    private function isEmptyRow(array $row): bool
    {
        foreach ($row as $v) {
            if ($this->stringOrNull($v) !== null) {
                return false;
            }
        }
        return true;
    }

    private function stringOrNull(mixed $value): ?string
    {
        if ($value === null) return null;
        $v = trim((string) $value);
        return $v === '' ? null : $v;
    }

    private function normalizeDateToMySql(string $value): ?string
    {
        $value = trim($value);

        $formats = [
            'd/m/Y H:i',
            'd/m/Y H:i:s',
            'Y-m-d H:i:s',
            'Y-m-d H:i',
            'd-m-Y H:i',
            'd-m-Y H:i:s',
        ];

        foreach ($formats as $fmt) {
            try {
                $dt = CarbonImmutable::createFromFormat($fmt, $value);
                return $dt->format('Y-m-d H:i:s');
            } catch (Throwable) {
                // tenta próximo
            }
        }

        return null;
    }

    private function normalizeMedico(string $value): ?string
    {
        $v = trim($value);
        $v = preg_replace('/\s+/', '', $v) ?? $v;

        if ($v === '') return null;

        /** TO-DO - Verificar a regra de ID do Médico e implementar na normalização */
        // se vier "000123" => "123"
        //if (ctype_digit($v)) {
        //    $v2 = ltrim($v, '0');
        //    return $v2 === '' ? '0' : $v2;
        //}

        return $v;
    }

    private function reportError(?callable $onRowError, ProductivityRowErrorDTO $error): void
    {
        if ($onRowError) {
            $onRowError($error);
        }
    }
}
