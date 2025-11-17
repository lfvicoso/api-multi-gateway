<?php

namespace Tests\Unit;

use App\Models\Gateway;
use App\Services\GatewayFactory;
use App\Services\GatewayServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GatewayFactoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_gateway1_service(): void
    {
        $gateway = Gateway::factory()->create(['type' => 'gateway1']);

        $service = GatewayFactory::create($gateway);

        $this->assertInstanceOf(GatewayServiceInterface::class, $service);
    }

    public function test_can_create_gateway2_service(): void
    {
        $gateway = Gateway::factory()->create(['type' => 'gateway2']);

        $service = GatewayFactory::create($gateway);

        $this->assertInstanceOf(GatewayServiceInterface::class, $service);
    }

    public function test_throws_exception_for_invalid_gateway_type(): void
    {
        $gateway = Gateway::factory()->create(['type' => 'invalid']);

        $this->expectException(\InvalidArgumentException::class);

        GatewayFactory::create($gateway);
    }
}

