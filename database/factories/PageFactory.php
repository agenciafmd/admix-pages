<?php

namespace Agenciafmd\Pages\Database\Factories;

use Agenciafmd\Pages\Models\Page;
use Illuminate\Database\Eloquent\Factories\Factory;

class PageFactory extends Factory
{
    protected $model = Page::class;

    public function definition(): array
    {
        return [
            'is_active' => $this->faker->optional(0.3, 1)
                ->randomElement([0]),
            'name' => $this->faker->sentence(3),
            'description' => $this->faker->paragraphs(5, true),
        ];
    }
}
