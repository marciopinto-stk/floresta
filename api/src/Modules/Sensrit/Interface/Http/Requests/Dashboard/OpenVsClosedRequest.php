<?php

namespace App\Modules\Sensrit\Interface\Http\Requests\Dashboard;

use Carbon\CarbonImmutable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class OpenVsClosedRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'from'      => ['nullable', 'date'],
            'to'        => [ 'nullable', 'date',],
            'companyId' => ['nullable', 'integer'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $from = $this->input('from');
        $to   = $this->input('to');

        // Se não mandar nada: últimos 7 dias (incluindo hoje)
        if (!$from && !$to) {
            $today = CarbonImmutable::now('UTC')->startOfDay();
            $this->merge([
                'from' => $today->subDays(6)->toDateString(),
                'to'   => $today->toDateString(),
            ]);
            return;
        }

        // Se mandar só um, completa o outro andando 7 dias pra trás (lá ele)
        if ($from && !$to) {
            $this->merge(['to' => CarbonImmutable::parse($from, 'UTC')->toDateString()]);
            return;
        }

        if (!$from && $to) {
            $toDate = CarbonImmutable::parse($to, 'UTC')->startOfDay();
            $this->merge(['from' => $toDate->subDays(6)->toDateString()]);
        }
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $from = $this->input('from');
            $to   = $this->input('to');

            if (!$from || !$to) {
                return;
            }

            try {
                $fromDate = CarbonImmutable::parse($from, 'UTC')->startOfDay();
                $toDate   = CarbonImmutable::parse($to, 'UTC')->startOfDay();
            } catch (\Throwable) {
                return;
            }

            if ($toDate->lt($fromDate)) {
                $validator->errors()->add('to', 'A data final deve ser maior ou igual à data inicial.');
                return;
            }

            $diffDays = $fromDate->diffInDays($toDate) + 1; // inclusivo

            if ($diffDays > 7) {
                $validator->errors()->add('to', 'O range máximo permitido é de 7 dias.');
            }
        });
    }
}
