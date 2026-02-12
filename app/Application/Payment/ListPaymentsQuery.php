<?php

namespace App\Application\Payment;

interface ListPaymentsQuery
{
    public function execute(): array;
}
