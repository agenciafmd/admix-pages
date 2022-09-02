<?php

namespace Agenciafmd\Payments\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentRequest extends FormRequest
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
                'nullable',
            ],
            'description' => [
                'required',
            ],
            'code' => [
                'nullable',
            ],
            'status' => [
                'required',
            ],
            'user_id' => [
                'required',
            ],
            'plan_id' => [
                'required',
            ],
            'payment_card_number' => [
                'nullable',
            ],
            'payment_date' => [
                'nullable',
            ],
            'value' => [
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
            'code' => 'codigo de adesão',
            'status' => 'status',
            'user_id' => 'usuario',
            'plan_id' => 'plano',
            'payment_card_number' => 'numero do cartão',
            'payment_date' => 'data de pagamento',
            'value' => 'valor',
        ];
    }

    public function authorize()
    {
        return true;
    }
}
