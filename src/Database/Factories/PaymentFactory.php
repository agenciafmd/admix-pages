<?php

namespace Agenciafmd\Payments\Database\Factories;

use Agenciafmd\Payments\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition()
    {
        return [
            'is_active' => $this->faker->optional(0.3, 1)
                ->randomElement([0]),
            'name' => $this->faker->sentence(),
            'description' => config('local-payments.wysiwyg') ? '<p>' . collect($this->faker->paragraphs(5, false))->implode('</p><p>') . '</p>' : $this->faker->paragraphs(5, true),
        ];
    }
}
