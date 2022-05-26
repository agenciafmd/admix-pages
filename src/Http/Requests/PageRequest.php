<?php

namespace Agenciafmd\Pages\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PageRequest extends FormRequest
{
    protected $errorBag = 'admix';

    public function rules()
    {
        $rules = [
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

        return $rules;
    }

    public function attributes()
    {
        return [
            'is_active' => 'ativo',
            'name' => 'nome',
            'description' => 'descrição',
        ];
    }

    public function authorize()
    {
        return true;
    }
}
