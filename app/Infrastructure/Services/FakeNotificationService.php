<?php

namespace App\Infrastructure\Services;

use App\Application\Payment\NotificationService;
use Exception;
use Illuminate\Support\Facades\Log;

class FakeNotificationService implements NotificationService
{
    public function send(array $payload): void
    {
        if (random_int(1, 3) === 1) {
            throw new Exception('El servicio de notificaciones esta caido');
        }

        Log::info('Notificacion enviada', $payload);
    }
}
