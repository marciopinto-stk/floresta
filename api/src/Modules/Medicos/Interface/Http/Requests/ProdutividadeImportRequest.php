<?php

namespace App\Modules\Medicos\Interface\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

final class ProdutividadeImportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file'              => ['required', 'file', 'mimes:csv,txt', 'max:20480'], // 20MB
            //'competencia'       => ['nullable', 'date_format: Y-m'],
            //'dry-run'           => ['nullable', 'boolean'],
            //'indempotency_key'  => ['nullable', 'string', 'max:120'],
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        logger()->warning('ProdutividadeImportRequest failed', [
            'errors' => $validator->errors()->toArray(),
            'hasFile' => $this->hasFile('file'),
            'files' => array_keys($this->allFiles()),
            'php_files' => $_FILES ?? null,
        ]);

        throw new HttpResponseException(
            response()->json([
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422)
        );
    }

    public function dryRun(): bool
    {
        return (bool) $this->boolean('dry_run');
    }

    public function competencia(): ?string
    {
        return $this->input('competencia');
    }

    public function idempotencyKey(): ?string
    {
        return $this->input('idempotency_key');
    }
}
