<?php

namespace Agenciafmd\Pages\Providers;

use Agenciafmd\Pages\Models\Page;
use Agenciafmd\Pages\Models\Category;
use Agenciafmd\Pages\Observers\PageObserver;
use Illuminate\Support\ServiceProvider;

class PageServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->providers();

        $this->setObservers();

        $this->setSearch();

        $this->loadMigrations();

        $this->publish();
    }

    public function register()
    {
        $this->loadConfigs();
    }

    protected function providers()
    {
        $this->app->register(AuthServiceProvider::class);
        $this->app->register(BladeServiceProvider::class);
        $this->app->register(RouteServiceProvider::class);
    }

    protected function setObservers()
    {
        Page::observe(PageObserver::class);
    }

    protected function setSearch()
    {
        $this->app->make('admix-search')
            ->registerModel(Page::class, 'name');
    }

    protected function loadMigrations()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
    }

    protected function publish()
    {
        $this->publishes([
            __DIR__ . '/../config/admix-pages.php' => config_path('admix-pages.php'),
            __DIR__ . '/../config/upload-configs.php' => config_path('upload-configs.php'),
        ], 'admix-pages:configs');


        $factoriesAndSeeders[__DIR__ . '/../Database/Factories/PageFactory.php'] = base_path('database/factories/PageFactory.php');
        $factoriesAndSeeders[__DIR__ . '/../Database/Seeders/PagesTableSeeder.php'] = base_path('database/seeders/PagesTableSeeder.php');
        $factoriesAndSeeders[__DIR__ . '/../Database/Faker/pages/image'] = base_path('database/faker/pages/image');

        $this->publishes($factoriesAndSeeders, 'admix-pages:seeders');
    }

    protected function loadConfigs()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/admix-pages.php', 'admix-pages');
        $this->mergeConfigFrom(__DIR__ . '/../config/gate.php', 'gate');
        $this->mergeConfigFrom(__DIR__ . '/../config/audit-alias.php', 'audit-alias');
        $this->mergeConfigFrom(__DIR__ . '/../config/upload-configs.php', 'upload-configs');
    }
}
