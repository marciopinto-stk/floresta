<?php

namespace Modules\Financeiro\Interface\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProcessarNotasRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_recepcao'=> ['required', 'integer', 'min:1'],
        ];
    }
}
