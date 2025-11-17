<?php

namespace App\Http\Requests\Payment;

use Illuminate\Foundation\Http\FormRequest;

class CreatePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'card_number' => ['required', 'string', 'size:16', 'regex:/^[0-9]+$/'],
            'cvv' => ['required', 'string', 'size:3', 'regex:/^[0-9]+$/'],
            'products' => ['required', 'array', 'min:1'],
            'products.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'products.*.quantity' => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'products.required' => 'At least one product is required',
            'products.min' => 'At least one product is required',
            'products.*.product_id.exists' => 'One or more products are invalid',
            'products.*.quantity.min' => 'Product quantity must be at least 1',
        ];
    }
}

