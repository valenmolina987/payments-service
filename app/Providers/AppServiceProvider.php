<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Application\Payment\PaymentRepository;
use App\Application\Payment\OutboxRepository;
use App\Infrastructure\Persistence\Repositories\EloquentPaymentRepository;
use App\Infrastructure\Persistence\Repositories\EloquentOutboxRepository;
use App\Application\Payment\NotificationService;
use App\Infrastructure\Services\FakeNotificationService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            PaymentRepository::class,
            EloquentPaymentRepository::class
        );
    
        $this->app->bind(
            OutboxRepository::class,
            EloquentOutboxRepository::class
        );

        $this->app->bind(
            NotificationService::class,
            FakeNotificationService::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
