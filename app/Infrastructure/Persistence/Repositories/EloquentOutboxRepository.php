<?php

namespace App\Infrastructure\Persistence\Repositories;

use App\Application\Payment\OutboxRepository;
use Illuminate\Support\Str;

use App\Infrastructure\Persistence\Models\EloquentOutboxMessageModel;

class EloquentOutboxRepository implements OutboxRepository
{
    public function add(
        string $aggregateType,
        string $aggregateId,
        string $eventType,
        array $payload
    ): void {
        EloquentOutboxMessageModel::create([
            'id' => Str::uuid()->toString(),
            'aggregate_type' => $aggregateType,
            'aggregate_id' => $aggregateId,
            'event_type' => $eventType,
            'payload' => $payload,
            'attempts' => 0,
            'processed_at' => null,
        ]);
    }
}
