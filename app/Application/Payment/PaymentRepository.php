<?php

namespace App\Application\Payment;

use App\Domain\Payment\Payment;

interface PaymentRepository
{
    public function save(Payment $payment): void;
}
