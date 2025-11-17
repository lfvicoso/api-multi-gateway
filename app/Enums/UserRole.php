<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'ADMIN';
    case MANAGER = 'MANAGER';
    case FINANCE = 'FINANCE';
    case USER = 'USER';

    public function label(): string
    {
        return match ($this) {
            self::ADMIN => 'Administrador',
            self::MANAGER => 'Gerente',
            self::FINANCE => 'Financeiro',
            self::USER => 'Usuário',
        };
    }

    public function canManageUsers(): bool
    {
        return in_array($this, [self::ADMIN, self::MANAGER]);
    }

    public function canManageProducts(): bool
    {
        return in_array($this, [self::ADMIN, self::MANAGER, self::FINANCE]);
    }

    public function canProcessRefunds(): bool
    {
        return in_array($this, [self::ADMIN, self::FINANCE]);
    }

    public function canManageGateways(): bool
    {
        return $this === self::ADMIN;
    }
}

