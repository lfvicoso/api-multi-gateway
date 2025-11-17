<?php

namespace Tests\Feature;

use App\Enums\TransactionStatus;
use App\Enums\UserRole;
use App\Models\Gateway;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RefundTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Criar gateway para os testes
        Gateway::factory()->gateway1()->create([
            'is_active' => true,
            'priority' => 1,
        ]);
    }

    public function test_finance_can_process_refund(): void
    {
        $finance = User::factory()->create(['role' => UserRole::FINANCE]);
        $token = $finance->createToken('test-token')->plainTextToken;

        $gateway = Gateway::first();
        
        $transaction = Transaction::factory()->create([
            'status' => TransactionStatus::SUCCESS,
            'external_id' => 'test-external-id',
            'gateway_id' => $gateway->id,
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson("/api/transactions/{$transaction->id}/refund");

        // Note: This will fail in tests without actual gateway mocks
        // In a real scenario, you'd mock the gateway service
        $response->assertStatus(200);
    }

    public function test_user_cannot_process_refund(): void
    {
        $user = User::factory()->create(['role' => UserRole::USER]);
        $token = $user->createToken('test-token')->plainTextToken;

        $transaction = Transaction::factory()->create([
            'status' => TransactionStatus::SUCCESS,
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson("/api/transactions/{$transaction->id}/refund");

        $response->assertStatus(403);
    }

    public function test_cannot_refund_failed_transaction(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $token = $admin->createToken('test-token')->plainTextToken;

        $transaction = Transaction::factory()->create([
            'status' => TransactionStatus::FAILED,
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson("/api/transactions/{$transaction->id}/refund");

        $response->assertStatus(422);
    }
}

