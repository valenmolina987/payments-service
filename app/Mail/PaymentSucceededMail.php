<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class PaymentSucceededMail extends Mailable
{
    public function __construct(
        public string $paymentId,
        public float $amount
    ) {}

    public function build()
    {
        return $this->subject('Pago exitoso')
                    ->view('emails.payment_succeeded')
                    ->with([
                        'paymentId' => $this->paymentId,
                        'amount' => $this->amount,
                    ]);
    }
}
