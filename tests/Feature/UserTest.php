<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    private function getAdminToken(): string
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        return $admin->createToken('test-token')->plainTextToken;
    }

    public function test_admin_can_list_users(): void
    {
        User::factory()->count(3)->create();

        $response = $this->withHeader('Authorization', "Bearer {$this->getAdminToken()}")
            ->getJson('/api/users');

        $response->assertStatus(200);
        $response->assertJsonCount(4); // 3 created + 1 admin
    }

    public function test_admin_can_create_user(): void
    {
        $response = $this->withHeader('Authorization', "Bearer {$this->getAdminToken()}")
            ->postJson('/api/users', [
                'email' => 'newuser@example.com',
                'password' => 'password123',
                'role' => UserRole::USER->value,
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('users', [
            'email' => 'newuser@example.com',
            'role' => UserRole::USER->value,
        ]);
    }

    public function test_manager_can_manage_users(): void
    {
        $manager = User::factory()->create(['role' => UserRole::MANAGER]);
        $token = $manager->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/users');

        $response->assertStatus(200);
    }

    public function test_finance_cannot_manage_users(): void
    {
        $finance = User::factory()->create(['role' => UserRole::FINANCE]);
        $token = $finance->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/users');

        $response->assertStatus(403);
    }
}

