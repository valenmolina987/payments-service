<?php

namespace App\Domain\Payment;

final class PaymentStatus
{
    private const PENDING = 'PENDING';
    private const SUCCESS = 'SUCCESS';

    private function __construct(private string $value) {}

    public static function pending(): self
    {
        return new self(self::PENDING);
    }

    public static function success(): self
    {
        return new self(self::SUCCESS);
    }

    public function isPending(): bool
    {
        return $this->value === self::PENDING;
    }

    public function value(): string
    {
        return $this->value;
    }
}
