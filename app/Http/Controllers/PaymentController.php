<?php

namespace App\Http\Controllers;

use App\Http\Requests\Payment\CreatePaymentRequest;
use App\Http\Resources\TransactionResource;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function __construct(
        private PaymentService $paymentService
    ) {}

    public function store(CreatePaymentRequest $request): JsonResponse
    {
        try {
            $transaction = $this->paymentService->processPayment([
                'name' => $request->name,
                'email' => $request->email,
                'card_number' => $request->card_number,
                'cvv' => $request->cvv,
                'products' => $request->products,
            ]);

            $transaction->load(['client', 'gateway', 'products.product']);

            return response()->json([
                'message' => 'Payment processed successfully',
                'data' => new TransactionResource($transaction),
            ], 201);
        } catch (\Exception $e) {
            Log::error('Payment processing error', [
                'error' => $e->getMessage(),
                'request' => $request->all(),
            ]);

            return response()->json([
                'message' => 'Payment processing failed',
                'error' => $e->getMessage(),
            ], 422);
        }
    }
}

