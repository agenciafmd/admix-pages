<?php

namespace Agenciafmd\Pages\Livewire\Pages\Page;

use Agenciafmd\Pages\Models\Page;
use Livewire\Attributes\Validate;
use Livewire\Form as LivewireForm;

class Form extends LivewireForm
{
    public Page $page;

    #[Validate]
    public bool $is_active = true;

    #[Validate]
    public string $name = '';

    #[Validate]
    public ?string $description = '';

    public function setModel(Page $page): void
    {
        $this->page = $page;
        if ($page->exists) {
            $this->is_active = $page->is_active;
            $this->name = $page->name;
            $this->description = $page->description;
        }
    }

    public function rules(): array
    {
        return [
            'is_active' => [
                'boolean',
            ],
            'name' => [
                'required',
                'max:255',
            ],
            'description' => [
                'nullable',
            ],
        ];
    }

    public function validationAttributes(): array
    {
        return [
            'is_active' => __('admix-pages::fields.is_active'),
            'name' => __('admix-pages::fields.name'),
            'description' => __('admix-pages::fields.description'),
        ];
    }

    public function save(): bool
    {
        $this->validate(rules: $this->rules(), attributes: $this->validationAttributes());
        $this->page->fill($this->except('page'));

        return $this->page->save();
    }
}
