<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product' => new ProductResource($this->whenLoaded('product')),
            'product_id' => $this->product_id,
            'quantity' => $this->quantity,
            'unit_price' => $this->unit_price,
            'unit_price_formatted' => number_format($this->unit_price / 100, 2, ',', '.'),
            'total_price' => $this->total_price,
            'total_price_formatted' => $this->formatted_total_price,
        ];
    }
}

