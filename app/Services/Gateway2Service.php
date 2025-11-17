<?php

namespace App\Services;

use App\Models\Gateway;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Gateway2Service implements GatewayServiceInterface
{
    private Gateway $gateway;

    public function __construct(Gateway $gateway)
    {
        $this->gateway = $gateway;
    }

    public function authenticate(): array
    {
        // Gateway 2 doesn't require authentication endpoint
        return ['success' => true];
    }

    public function createTransaction(array $data): array
    {
        try {
            $credentials = $this->gateway->credentials ?? [];
            $token = $credentials['token'] ?? 'tk_f2198cc671b5289fa856';
            $secret = $credentials['secret'] ?? '3d15e8ed6131446ea7e3456728b1211f';

            $response = Http::withHeaders([
                'Gateway-Auth-Token' => $token,
                'Gateway-Auth-Secret' => $secret,
            ])->post("{$this->gateway->url}/transacoes", [
                'valor' => $data['amount'],
                'nome' => $data['name'],
                'email' => $data['email'],
                'numeroCartao' => $data['card_number'],
                'cvv' => $data['cvv'],
            ]);

            $responseData = $response->json();

            if ($response->successful()) {
                return [
                    'success' => true,
                    'external_id' => $responseData['id'] ?? null,
                    'status' => 'success',
                    'response' => $responseData,
                ];
            }

            return [
                'success' => false,
                'message' => $responseData['message'] ?? 'Transaction failed',
                'response' => $responseData,
            ];
        } catch (\Exception $e) {
            Log::error('Gateway2 transaction error', [
                'error' => $e->getMessage(),
                'gateway' => $this->gateway->id,
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function refundTransaction(string $externalId): array
    {
        try {
            $credentials = $this->gateway->credentials ?? [];
            $token = $credentials['token'] ?? 'tk_f2198cc671b5289fa856';
            $secret = $credentials['secret'] ?? '3d15e8ed6131446ea7e3456728b1211f';

            $response = Http::withHeaders([
                'Gateway-Auth-Token' => $token,
                'Gateway-Auth-Secret' => $secret,
            ])->post("{$this->gateway->url}/transacoes/reembolso", [
                'id' => $externalId,
            ]);

            $responseData = $response->json();

            if ($response->successful()) {
                return [
                    'success' => true,
                    'response' => $responseData,
                ];
            }

            return [
                'success' => false,
                'message' => $responseData['message'] ?? 'Refund failed',
                'response' => $responseData,
            ];
        } catch (\Exception $e) {
            Log::error('Gateway2 refund error', [
                'error' => $e->getMessage(),
                'gateway' => $this->gateway->id,
                'external_id' => $externalId,
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
}

