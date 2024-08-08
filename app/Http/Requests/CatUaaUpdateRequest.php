<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class CatUaaUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'valor' => [
                'required',
                'string',
                Rule::unique('cat_uaa', 'valor')->ignore($this->catUaa),
            ],
            'descripcion' => ['nullable', 'string'],
            'activo' => ['required', 'boolean'],
        ];
    }
}