<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Application\Payment\PaymentRepository;
use App\Application\Payment\OutboxRepository;
use App\Infrastructure\Persistence\Repositories\EloquentPaymentRepository;
use App\Infrastructure\Persistence\Repositories\EloquentOutboxRepository;
use App\Application\Payment\NotificationService;
use App\Infrastructure\Services\MailNotificationService;
use App\Application\Payment\ListPaymentsQuery;
use App\Infrastructure\Persistence\Queries\EloquentListPaymentsQuery;

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
            MailNotificationService::class
        );

        $this->app->bind(
            ListPaymentsQuery::class,
            EloquentListPaymentsQuery::class
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
