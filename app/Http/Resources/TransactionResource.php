<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'client' => new ClientResource($this->whenLoaded('client')),
            'gateway' => [
                'id' => $this->gateway_id,
                'name' => $this->whenLoaded('gateway')?->name,
            ],
            'external_id' => $this->external_id,
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'amount' => $this->amount,
            'amount_formatted' => $this->formatted_amount,
            'card_last_numbers' => $this->card_last_numbers,
            'products' => TransactionProductResource::collection($this->whenLoaded('products')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

