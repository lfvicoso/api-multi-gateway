<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Gateway;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GatewayTest extends TestCase
{
    use RefreshDatabase;

    private function getAdminToken(): string
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        return $admin->createToken('test-token')->plainTextToken;
    }

    public function test_admin_can_list_gateways(): void
    {
        Gateway::factory()->count(2)->create();

        $response = $this->withHeader('Authorization', "Bearer {$this->getAdminToken()}")
            ->getJson('/api/gateways');

        $response->assertStatus(200);
        $response->assertJsonCount(2);
    }

    public function test_admin_can_update_gateway_status(): void
    {
        $gateway = Gateway::factory()->create(['is_active' => true]);

        $response = $this->withHeader('Authorization', "Bearer {$this->getAdminToken()}")
            ->patchJson("/api/gateways/{$gateway->id}/status", [
                'is_active' => false,
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('gateways', [
            'id' => $gateway->id,
            'is_active' => false,
        ]);
    }

    public function test_user_cannot_manage_gateways(): void
    {
        $user = User::factory()->create(['role' => UserRole::USER]);
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/gateways');

        $response->assertStatus(403);
    }
}

