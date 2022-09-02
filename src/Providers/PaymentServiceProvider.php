<?php

namespace Agenciafmd\Payments\Providers;

use Agenciafmd\Payments\Models\Payment;
use Agenciafmd\Payments\Observers\PaymentObserver;
use Illuminate\Support\ServiceProvider;

class PaymentServiceProvider extends ServiceProvider
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
        Payment::observe(PaymentObserver::class);
    }

    protected function setSearch()
    {
        $this->app->make('admix-search')
            ->registerModel(Payment::class, 'name');
    }

    protected function loadMigrations()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
    }

    protected function publish()
    {
        $this->publishes([
            __DIR__ . '/../config/local-payments.php' => config_path('local-payments.php'),
            __DIR__ . '/../config/upload-configs.php' => config_path('upload-configs.php'),
        ], 'local-payments:configs');


        $factoriesAndSeeders[__DIR__ . '/../Database/Factories/PaymentFactory.php'] = base_path('database/factories/PaymentFactory.php');
        $factoriesAndSeeders[__DIR__ . '/../Database/Seeders/PaymentsTableSeeder.php'] = base_path('database/seeders/PaymentsTableSeeder.php');
        $factoriesAndSeeders[__DIR__ . '/../Database/Faker/payments/image'] = base_path('database/faker/payments/image');

        $this->publishes($factoriesAndSeeders, 'local-payments:seeders');
    }

    protected function loadConfigs()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/local-payments.php', 'local-payments');
        $this->mergeConfigFrom(__DIR__ . '/../config/gate.php', 'gate');
        $this->mergeConfigFrom(__DIR__ . '/../config/audit-alias.php', 'audit-alias');
        $this->mergeConfigFrom(__DIR__ . '/../config/upload-configs.php', 'upload-configs');
    }
}
