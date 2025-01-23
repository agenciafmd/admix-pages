<?php

namespace Agenciafmd\Pages\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PageRequest extends FormRequest
{
    protected $errorBag = 'admix';

    public function rules(): array
    {
        return [
            'is_active' => [
                'required',
                'boolean',
            ],
            'name' => [
                'required',
                'max:150',
            ],
            'description' => [
                'required',
            ],
        ];
    }

    public function attributes(): array
    {
        return [
            'is_active' => 'ativo',
            'name' => 'nome',
            'description' => 'descrição',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
