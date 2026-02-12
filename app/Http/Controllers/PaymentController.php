<?php

namespace App\Http\Controllers;

use App\Application\Payment\ProcessPaymentUseCase;
use App\Http\Requests\StorePaymentRequest;
use Illuminate\Http\JsonResponse;
use App\Application\Payment\ListPaymentsQuery;

class PaymentController extends Controller
{
    public function __construct(
        private ProcessPaymentUseCase $useCase
    ) {}

    public function store(StorePaymentRequest $request): JsonResponse
    {
        $paymentId = $this->useCase->execute(
            amount: (float) $request->input('amount')
        );

        return response()->json([
            'payment_id' => $paymentId,
            'status' => 'SUCCESS'
        ], 201);
    }

    public function index(ListPaymentsQuery $query): JsonResponse
    {
        $payments = $query->execute();

        return response()->json($payments);
    }
}
