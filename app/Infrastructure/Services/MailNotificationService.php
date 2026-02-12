<?php

namespace App\Infrastructure\Services;

use App\Application\Payment\NotificationService;
use App\Mail\PaymentSucceededMail;
use Illuminate\Support\Facades\Mail;
use Exception;

class MailNotificationService implements NotificationService
{
    public function send(array $payload): void
    {
        if (random_int(1, 5) === 1) {
            throw new Exception('Servicio de email temporalmente no disponible');
        }

        Mail::to($payload['email'])
            ->send(new PaymentSucceededMail(
                paymentId: $payload['payment_id'],
                amount: $payload['amount']
            ));
    }
}
