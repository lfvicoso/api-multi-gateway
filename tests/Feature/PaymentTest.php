<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Gateway;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Criar gateways para os testes
        Gateway::factory()->gateway1()->create([
            'is_active' => true,
            'priority' => 1,
        ]);
        
        Gateway::factory()->gateway2()->create([
            'is_active' => true,
            'priority' => 2,
        ]);
    }

    public function test_can_process_payment_with_multiple_products(): void
    {
        $product1 = Product::factory()->create(['amount' => 10000]); // R$ 100.00
        $product2 = Product::factory()->create(['amount' => 5000]); // R$ 50.00

        $response = $this->postJson('/api/payments', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'card_number' => '5569000000006063',
            'cvv' => '010',
            'products' => [
                ['product_id' => $product1->id, 'quantity' => 2],
                ['product_id' => $product2->id, 'quantity' => 1],
            ],
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'message',
            'data' => [
                'id',
                'status',
                'amount',
                'client',
                'products',
            ],
        ]);

        $this->assertDatabaseHas('transactions', [
            'status' => 'success',
            'amount' => 25000, // 2 * 10000 + 1 * 5000
        ]);

        $this->assertDatabaseHas('transaction_products', [
            'product_id' => $product1->id,
            'quantity' => 2,
        ]);

        $this->assertDatabaseHas('transaction_products', [
            'product_id' => $product2->id,
            'quantity' => 1,
        ]);
    }

    public function test_cannot_process_payment_with_invalid_product(): void
    {
        $response = $this->postJson('/api/payments', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'card_number' => '5569000000006063',
            'cvv' => '010',
            'products' => [
                ['product_id' => 999, 'quantity' => 1],
            ],
        ]);

        $response->assertStatus(422);
    }

    public function test_cannot_process_payment_with_inactive_product(): void
    {
        $product = Product::factory()->create(['is_active' => false]);

        $response = $this->postJson('/api/payments', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'card_number' => '5569000000006063',
            'cvv' => '010',
            'products' => [
                ['product_id' => $product->id, 'quantity' => 1],
            ],
        ]);

        $response->assertStatus(422);
    }

    public function test_creates_client_if_not_exists(): void
    {
        $product = Product::factory()->create();

        $this->postJson('/api/payments', [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'card_number' => '5569000000006063',
            'cvv' => '010',
            'products' => [
                ['product_id' => $product->id, 'quantity' => 1],
            ],
        ]);

        $this->assertDatabaseHas('clients', [
            'email' => 'jane@example.com',
            'name' => 'Jane Doe',
        ]);
    }

    public function test_uses_existing_client_if_exists(): void
    {
        $client = Client::factory()->create(['email' => 'existing@example.com']);
        $product = Product::factory()->create();

        $this->postJson('/api/payments', [
            'name' => 'Different Name',
            'email' => 'existing@example.com',
            'card_number' => '5569000000006063',
            'cvv' => '010',
            'products' => [
                ['product_id' => $product->id, 'quantity' => 1],
            ],
        ]);

        $this->assertDatabaseCount('clients', 1);
        $this->assertDatabaseHas('clients', [
            'id' => $client->id,
            'email' => 'existing@example.com',
        ]);
    }
}

