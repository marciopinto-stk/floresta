<?php

namespace App\Modules\Sensrit\Interface\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

final class OpenByCategoryRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'month' => ['nullable', 'integer', 'between:1,12'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
