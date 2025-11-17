<?php

namespace App\Enums;

enum TransactionStatus: string
{
    case PENDING = 'pending';
    case SUCCESS = 'success';
    case FAILED = 'failed';
    case REFUNDED = 'refunded';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pendente',
            self::SUCCESS => 'Sucesso',
            self::FAILED => 'Falha',
            self::REFUNDED => 'Reembolsado',
        };
    }
}

