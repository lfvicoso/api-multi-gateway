<?php

namespace App\Services;

use App\Models\Gateway;

class GatewayFactory
{
    public static function create(Gateway $gateway): GatewayServiceInterface
    {
        return match ($gateway->type) {
            'gateway1' => new Gateway1Service($gateway),
            'gateway2' => new Gateway2Service($gateway),
            default => throw new \InvalidArgumentException("Unknown gateway type: {$gateway->type}"),
        };
    }
}

