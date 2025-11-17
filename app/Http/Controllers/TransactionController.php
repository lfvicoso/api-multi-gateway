<?php

namespace App\Http\Controllers;

use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class TransactionController extends Controller
{
    public function __construct(
        private PaymentService $paymentService
    ) {}

    public function index(): JsonResponse
    {
        $transactions = Transaction::with(['client', 'gateway', 'products.product'])
            ->latest()
            ->get();

        return response()->json(TransactionResource::collection($transactions));
    }

    public function show(Transaction $transaction): JsonResponse
    {
        $transaction->load(['client', 'gateway', 'products.product']);

        return response()->json(new TransactionResource($transaction));
    }

    public function refund(Transaction $transaction): JsonResponse
    {
        try {
            $transaction = $this->paymentService->processRefund($transaction);
            $transaction->load(['client', 'gateway', 'products.product']);

            return response()->json([
                'message' => 'Refund processed successfully',
                'data' => new TransactionResource($transaction),
            ]);
        } catch (\Exception $e) {
            Log::error('Refund processing error', [
                'error' => $e->getMessage(),
                'transaction_id' => $transaction->id,
            ]);

            return response()->json([
                'message' => 'Refund processing failed',
                'error' => $e->getMessage(),
            ], 422);
        }
    }
}

