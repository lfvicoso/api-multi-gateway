<?php

namespace App\Services;

interface GatewayServiceInterface
{
    public function authenticate(): array;
    
    public function createTransaction(array $data): array;
    
    public function refundTransaction(string $externalId): array;
}

