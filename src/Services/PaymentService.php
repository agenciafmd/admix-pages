<?php

namespace Agenciafmd\Payments\Services;

use Agenciafmd\Payments\Models\Payment;
use Illuminate\Support\Collection;

class PaymentService
{
    public function lists(): Collection
    {
        return Payment::pluck('name', 'id');
    }
}
