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
            'name' => $this->faker->sentence(),
            'description' => config('admix-pages.wysiwyg') ? '<p>' . collect($this->faker->paragraphs(5, false))->implode('</p><p>') . '</p>' : $this->faker->paragraphs(5, true),
        ];
    }
}