<?php

namespace App\Services;

use App\Enums\TransactionStatus;
use App\Models\Client;
use App\Models\Gateway;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    public function processPayment(array $data): Transaction
    {
        return DB::transaction(function () use ($data) {
            // Get or create client
            $client = Client::firstOrCreate(
                ['email' => $data['email']],
                ['name' => $data['name']]
            );

            // Calculate total amount from products
            $products = Product::whereIn('id', array_column($data['products'], 'product_id'))
                ->active()
                ->get()
                ->keyBy('id');

            $totalAmount = 0;
            $transactionProducts = [];

            foreach ($data['products'] as $item) {
                $product = $products->get($item['product_id']);
                
                if (!$product) {
                    throw new \Exception("Product {$item['product_id']} not found or inactive");
                }

                $quantity = $item['quantity'];
                $unitPrice = $product->amount;
                $totalPrice = $unitPrice * $quantity;
                $totalAmount += $totalPrice;

                $transactionProducts[] = [
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'total_price' => $totalPrice,
                ];
            }

            if ($totalAmount <= 0) {
                throw new \Exception('Total amount must be greater than zero');
            }

            // Get active gateways ordered by priority
            $gateways = Gateway::active()->orderedByPriority()->get();

            if ($gateways->isEmpty()) {
                throw new \Exception('No active gateways available');
            }

            // Try each gateway in priority order
            $transaction = null;
            $lastError = null;

            foreach ($gateways as $gateway) {
                try {
                    $gatewayService = GatewayFactory::create($gateway);

                    $gatewayResponse = $gatewayService->createTransaction([
                        'amount' => $totalAmount,
                        'name' => $client->name,
                        'email' => $client->email,
                        'card_number' => $data['card_number'],
                        'cvv' => $data['cvv'],
                    ]);

                    if ($gatewayResponse['success']) {
                        // Create successful transaction
                        $transaction = Transaction::create([
                            'client_id' => $client->id,
                            'gateway_id' => $gateway->id,
                            'external_id' => $gatewayResponse['external_id'],
                            'status' => TransactionStatus::SUCCESS,
                            'amount' => $totalAmount,
                            'card_last_numbers' => substr($data['card_number'], -4),
                            'gateway_response' => $gatewayResponse['response'] ?? null,
                        ]);

                        // Create transaction products
                        foreach ($transactionProducts as $tp) {
                            $transaction->products()->create($tp);
                        }

                        Log::info('Payment processed successfully', [
                            'transaction_id' => $transaction->id,
                            'gateway_id' => $gateway->id,
                        ]);

                        break;
                    } else {
                        $lastError = $gatewayResponse['message'] ?? 'Unknown error';
                        Log::warning('Gateway payment failed', [
                            'gateway_id' => $gateway->id,
                            'error' => $lastError,
                        ]);
                    }
                } catch (\Exception $e) {
                    $lastError = $e->getMessage();
                    Log::error('Gateway payment exception', [
                        'gateway_id' => $gateway->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            // If no gateway succeeded, create failed transaction
            if (!$transaction) {
                $transaction = Transaction::create([
                    'client_id' => $client->id,
                    'gateway_id' => null,
                    'external_id' => null,
                    'status' => TransactionStatus::FAILED,
                    'amount' => $totalAmount,
                    'card_last_numbers' => substr($data['card_number'], -4),
                    'gateway_response' => ['error' => $lastError],
                ]);

                foreach ($transactionProducts as $tp) {
                    $transaction->products()->create($tp);
                }

                throw new \Exception($lastError ?? 'All gateways failed');
            }

            return $transaction;
        });
    }

    public function processRefund(Transaction $transaction): Transaction
    {
        if (!$transaction->isRefundable()) {
            throw new \Exception('Transaction is not refundable');
        }

        if (!$transaction->gateway) {
            throw new \Exception('Transaction has no gateway associated');
        }

        if (!$transaction->external_id) {
            throw new \Exception('Transaction has no external ID');
        }

        $gatewayService = GatewayFactory::create($transaction->gateway);

        $refundResponse = $gatewayService->refundTransaction($transaction->external_id);

        if (!$refundResponse['success']) {
            throw new \Exception($refundResponse['message'] ?? 'Refund failed');
        }

        $transaction->update([
            'status' => TransactionStatus::REFUNDED,
            'gateway_response' => array_merge(
                $transaction->gateway_response ?? [],
                ['refund' => $refundResponse['response'] ?? null]
            ),
        ]);

        Log::info('Refund processed successfully', [
            'transaction_id' => $transaction->id,
            'gateway_id' => $transaction->gateway_id,
        ]);

        return $transaction->fresh();
    }
}

