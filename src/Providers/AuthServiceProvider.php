<?php

namespace Agenciafmd\Payments\Providers;

use Agenciafmd\Payments\Models\Payment;
use Agenciafmd\Payments\Policies\PaymentPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Payment::class => PaymentPolicy::class,
    ];

    public function boot()
    {
        $this->registerPolicies();
    }
}
