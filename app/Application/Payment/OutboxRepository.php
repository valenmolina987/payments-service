<?php

namespace App\Application\Payment;

interface OutboxRepository
{
    public function add(
        string $aggregateType,
        string $aggregateId,
        string $eventType,
        array $payload
    ): void;
}
