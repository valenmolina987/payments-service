<?php

namespace App\Infrastructure\Persistence\Repositories;

use App\Application\Payment\PaymentRepository;
use App\Domain\Payment\Payment;

use App\Infrastructure\Persistence\Models\EloquentPaymentModel;

class EloquentPaymentRepository implements PaymentRepository
{
    public function save(Payment $payment): void
    {
        EloquentPaymentModel::updateOrCreate(
            ['id' => $payment->id()],
            [
                'amount' => $payment->amount(),
                'status' => $payment->status()->value(),
            ]
        );
    }
}
