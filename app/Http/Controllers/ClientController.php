<?php

namespace App\Http\Controllers;

use App\Http\Resources\ClientResource;
use App\Http\Resources\TransactionResource;
use App\Models\Client;
use Illuminate\Http\JsonResponse;

class ClientController extends Controller
{
    public function index(): JsonResponse
    {
        $clients = Client::all();

        return response()->json(ClientResource::collection($clients));
    }

    public function show(Client $client): JsonResponse
    {
        $client->load('transactions.products.product', 'transactions.gateway');

        return response()->json([
            'client' => new ClientResource($client),
            'transactions' => TransactionResource::collection($client->transactions),
        ]);
    }
}

