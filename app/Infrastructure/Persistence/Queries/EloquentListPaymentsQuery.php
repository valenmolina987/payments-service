<?php

namespace App\Infrastructure\Persistence\Queries;

use App\Application\Payment\ListPaymentsQuery;
use Illuminate\Support\Facades\DB;

class EloquentListPaymentsQuery implements ListPaymentsQuery
{
    public function execute(): array
    {
        return DB::table('payments')
            ->leftJoin('outbox_messages', function ($join) {
                $join->on('payments.id', '=', 'outbox_messages.aggregate_id')
                     ->where('outbox_messages.event_type', '=', 'PaymentSucceeded');
            })
            ->select(
                'payments.id as payment_id',
                'payments.amount',
                'payments.status as payment_status',
                DB::raw("
                    CASE
                        WHEN outbox_messages.failed = 1 THEN 'FAILED'
                        WHEN outbox_messages.processed_at IS NOT NULL THEN 'SENT'
                        ELSE 'PENDING'
                    END as notification_status
                ")
            )
            ->get()
            ->toArray();
    }
}
