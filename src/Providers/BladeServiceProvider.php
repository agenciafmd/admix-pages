<?php

namespace Agenciafmd\Pages\Providers;

use Illuminate\Support\ServiceProvider;

class BladeServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->setMenu();

        $this->loadViews();

        $this->loadTranslations();

        $this->publish();
    }

    public function register()
    {
        //
    }

    protected function setMenu()
    {
        $this->app->make('admix-menu')
            ->push((object)[
                'view' => 'agenciafmd/pages::partials.menus.item',
                'ord' => config('admix-pages.sort', 1),
            ]);
    }

    protected function loadViews()
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'agenciafmd/pages');
    }

    protected function loadTranslations()
    {
        $this->loadJsonTranslationsFrom(__DIR__ . '/../resources/lang');
    }

    protected function publish()
    {
        $this->publishes([
            __DIR__ . '/../resources/views' => base_path('resources/views/vendor/agenciafmd/pages'),
        ], 'admix-pages:views');
    }
}
