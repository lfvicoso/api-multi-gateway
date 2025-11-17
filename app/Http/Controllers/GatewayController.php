<?php

namespace App\Http\Controllers;

use App\Http\Requests\Gateway\UpdateGatewayPriorityRequest;
use App\Http\Requests\Gateway\UpdateGatewayStatusRequest;
use App\Http\Resources\GatewayResource;
use App\Models\Gateway;
use Illuminate\Http\JsonResponse;

class GatewayController extends Controller
{
    public function index(): JsonResponse
    {
        $gateways = Gateway::orderedByPriority()->get();

        return response()->json(GatewayResource::collection($gateways));
    }

    public function show(Gateway $gateway): JsonResponse
    {
        return response()->json(new GatewayResource($gateway));
    }

    public function updateStatus(Gateway $gateway, UpdateGatewayStatusRequest $request): JsonResponse
    {
        $gateway->update([
            'is_active' => $request->is_active,
        ]);

        return response()->json([
            'message' => 'Gateway status updated successfully',
            'data' => new GatewayResource($gateway->fresh()),
        ]);
    }

    public function updatePriority(Gateway $gateway, UpdateGatewayPriorityRequest $request): JsonResponse
    {
        $gateway->update([
            'priority' => $request->priority,
        ]);

        return response()->json([
            'message' => 'Gateway priority updated successfully',
            'data' => new GatewayResource($gateway->fresh()),
        ]);
    }
}

