<?php

namespace Agenciafmd\Pages\Providers;

use Illuminate\Support\ServiceProvider;

class BladeServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->setMenu();

        $this->loadViews();

        $this->loadTranslations();

        $this->publish();
    }

    public function register(): void
    {
        //
    }

    protected function setMenu(): void
    {
        $this->app->make('admix-menu')
            ->push((object) [
                'view' => 'agenciafmd/pages::partials.menus.item',
                'ord' => config('admix-pages.sort', 1),
            ]);
    }

    protected function loadViews(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'agenciafmd/pages');
    }

    protected function loadTranslations(): void
    {
        $this->loadJsonTranslationsFrom(__DIR__ . '/../resources/lang');
    }

    protected function publish(): void
    {
        $this->publishes([
            __DIR__ . '/../resources/views' => base_path('resources/views/vendor/agenciafmd/pages'),
        ], 'admix-pages:views');
    }
}
