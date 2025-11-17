<?php

namespace App\Services;

use App\Models\Gateway;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Gateway1Service implements GatewayServiceInterface
{
    private Gateway $gateway;
    private ?string $token = null;

    public function __construct(Gateway $gateway)
    {
        $this->gateway = $gateway;
    }

    public function authenticate(): array
    {
        try {
            $credentials = $this->gateway->credentials ?? [];
            $email = $credentials['email'] ?? 'dev@betalent.tech';
            $token = $credentials['token'] ?? 'FEC9BB078BF338F464F96B48089EB498';

            $response = Http::post("{$this->gateway->url}/login", [
                'email' => $email,
                'token' => $token,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $this->token = $data['token'] ?? null;
                
                return [
                    'success' => true,
                    'token' => $this->token,
                ];
            }

            return [
                'success' => false,
                'message' => 'Authentication failed',
            ];
        } catch (\Exception $e) {
            Log::error('Gateway1 authentication error', [
                'error' => $e->getMessage(),
                'gateway' => $this->gateway->id,
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function createTransaction(array $data): array
    {
        if (!$this->token) {
            $auth = $this->authenticate();
            if (!$auth['success']) {
                return $auth;
            }
        }

        try {
            $response = Http::withToken($this->token)
                ->post("{$this->gateway->url}/transactions", [
                    'amount' => $data['amount'],
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'cardNumber' => $data['card_number'],
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
            Log::error('Gateway1 transaction error', [
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
        if (!$this->token) {
            $auth = $this->authenticate();
            if (!$auth['success']) {
                return $auth;
            }
        }

        try {
            $response = Http::withToken($this->token)
                ->post("{$this->gateway->url}/transactions/{$externalId}/charge_back");

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
            Log::error('Gateway1 refund error', [
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

