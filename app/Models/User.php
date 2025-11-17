<?php

namespace App\Models;

use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected static function newFactory()
    {
        return UserFactory::new();
    }

    protected $fillable = [
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'role' => UserRole::class,
    ];

    public function isAdmin(): bool
    {
        return $this->role === UserRole::ADMIN;
    }

    public function isManager(): bool
    {
        return $this->role === UserRole::MANAGER;
    }

    public function isFinance(): bool
    {
        return $this->role === UserRole::FINANCE;
    }

    public function canManageUsers(): bool
    {
        return $this->role->canManageUsers();
    }

    public function canManageProducts(): bool
    {
        return $this->role->canManageProducts();
    }

    public function canProcessRefunds(): bool
    {
        return $this->role->canProcessRefunds();
    }

    public function canManageGateways(): bool
    {
        return $this->role->canManageGateways();
    }
}

