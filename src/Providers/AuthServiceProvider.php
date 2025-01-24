<?php

namespace Agenciafmd\Pages\Providers;

use Agenciafmd\Pages\Models\Page;
use Agenciafmd\Pages\Policies\PagePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Page::class => PagePolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }

    public function register(): void
    {
        $this->loadConfigs();
    }

    public function loadConfigs(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/gate.php', 'gate');
    }
}
