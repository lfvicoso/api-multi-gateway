<?php

namespace App\Http\Requests\User;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['sometimes', 'email', Rule::unique('users')->ignore($this->route('user'))],
            'password' => ['sometimes', 'string', 'min:8'],
            'role' => ['sometimes', Rule::enum(UserRole::class)],
        ];
    }
}

