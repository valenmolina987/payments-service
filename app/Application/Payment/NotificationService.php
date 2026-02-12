<?php

namespace App\Application\Payment;

interface NotificationService
{
    public function send(array $payload): void;
}
