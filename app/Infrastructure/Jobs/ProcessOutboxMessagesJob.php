<?php

namespace App\Infrastructure\Jobs;

use App\Application\Payment\NotificationService;
use App\Infrastructure\Persistence\Models\EloquentOutboxMessageModel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessOutboxMessagesJob implements ShouldQueue
{
    use Dispatchable, Queueable, SerializesModels;

    public function handle(NotificationService $notificationService): void
    {
        DB::transaction(function () use ($notificationService) {

            $messages = EloquentOutboxMessageModel::whereNull('processed_at')
                ->where('failed', false)
                ->orderBy('created_at')
                ->lockForUpdate()
                ->limit(20)
                ->get();

            foreach ($messages as $message) {

                try {
                    $notificationService->send($message->payload);

                    $message->update([
                        'processed_at' => now(),
                    ]);

                } catch (\Throwable $e) {

                    Log::error($e->getMessage());

                    $currentAttempts = $message->attempts + 1;
                
                    Log::info('Intentos', [$currentAttempts]);

                    if ($currentAttempts >= 5) {
                
                        $message->update([
                            'attempts' => $currentAttempts,
                            'failed' => true,
                            'failed_at' => now(),
                            'error_message' => $e->getMessage(),
                        ]);
                
                        continue;
                    }
                
                    $message->update([
                        'attempts' => $currentAttempts,
                    ]);
                }                
            }
        });
    }
}
