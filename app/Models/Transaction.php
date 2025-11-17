<?php

namespace App\Models;

use App\Enums\TransactionStatus;
use Database\Factories\TransactionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    protected static function newFactory()
    {
        return TransactionFactory::new();
    }

    protected $fillable = [
        'client_id',
        'gateway_id',
        'external_id',
        'status',
        'amount',
        'card_last_numbers',
        'gateway_response',
    ];

    protected $casts = [
        'amount' => 'integer',
        'status' => TransactionStatus::class,
        'gateway_response' => 'array',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function gateway()
    {
        return $this->belongsTo(Gateway::class);
    }

    public function products()
    {
        return $this->hasMany(TransactionProduct::class);
    }

    public function getFormattedAmountAttribute(): string
    {
        return number_format($this->amount / 100, 2, ',', '.');
    }

    public function isSuccessful(): bool
    {
        return $this->status === TransactionStatus::SUCCESS;
    }

    public function isRefundable(): bool
    {
        return $this->status === TransactionStatus::SUCCESS;
    }
}

