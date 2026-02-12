<?php

namespace App\Domain\Payment;

use DomainException;

final class Payment
{
    public function __construct(
        private string $id,
        private float $amount,
        private PaymentStatus $status
    ) {}

    public static function create(string $id, float $amount): self
    {
        return new self($id, $amount, PaymentStatus::pending());
    }

    public function markAsSuccess(): void
    {
        if (!$this->status->isPending()) {
            throw new DomainException('Estado de transacción inválido.');
        }

        $this->status = PaymentStatus::success();
    }

    public function id(): string { return $this->id; }
    public function amount(): float { return $this->amount; }
    public function status(): PaymentStatus { return $this->status; }
}
