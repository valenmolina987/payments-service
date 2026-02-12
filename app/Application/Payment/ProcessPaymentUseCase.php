<?php

namespace App\Application\Payment;

use App\Domain\Payment\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final class ProcessPaymentUseCase
{
    public function __construct(
        private PaymentRepository $paymentRepository,
        private OutboxRepository $outboxRepository
    ) {}

    public function execute(float $amount): void
    {
        DB::transaction(function () use ($amount) {

            $payment = Payment::create(Str::uuid()->toString(), $amount);
            $payment->markAsSuccess();

            $this->paymentRepository->save($payment);

            $this->outboxRepository->add(
                'payment',
                $payment->id(),
                'PaymentSucceeded',
                [
                    'payment_id' => $payment->id(),
                    'amount' => $payment->amount()
                ]
            );
        });
    }
}
