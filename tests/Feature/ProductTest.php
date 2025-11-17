<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    private function getManagerToken(): string
    {
        $manager = User::factory()->create(['role' => UserRole::MANAGER]);
        return $manager->createToken('test-token')->plainTextToken;
    }

    public function test_manager_can_list_products(): void
    {
        Product::factory()->count(3)->create();

        $response = $this->withHeader('Authorization', "Bearer {$this->getManagerToken()}")
            ->getJson('/api/products');

        $response->assertStatus(200);
        $response->assertJsonCount(3);
    }

    public function test_manager_can_create_product(): void
    {
        $response = $this->withHeader('Authorization', "Bearer {$this->getManagerToken()}")
            ->postJson('/api/products', [
                'name' => 'Test Product',
                'amount' => 10000, // R$ 100.00
                'description' => 'Test Description',
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('products', [
            'name' => 'Test Product',
            'amount' => 10000,
        ]);
    }

    public function test_user_cannot_manage_products(): void
    {
        $user = User::factory()->create(['role' => UserRole::USER]);
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/products', [
                'name' => 'Test Product',
                'amount' => 10000,
            ]);

        $response->assertStatus(403);
    }
}

