<?php

namespace Agenciafmd\Pages\Providers;

use Agenciafmd\Pages\Livewire\Pages;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class LivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('agenciafmd.pages.livewire.pages.page.index', Pages\Page\Index::class);
        Livewire::component('agenciafmd.pages.livewire.pages.page.component', Pages\Page\Component::class);
    }

    public function register(): void
    {
        //
    }
}
