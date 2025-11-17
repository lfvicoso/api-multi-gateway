<?php

namespace App\Models;

use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected static function newFactory()
    {
        return ProductFactory::new();
    }

    protected $fillable = [
        'name',
        'amount',
        'is_active',
        'description',
    ];

    protected $casts = [
        'amount' => 'integer',
        'is_active' => 'boolean',
    ];

    public function transactionProducts()
    {
        return $this->hasMany(TransactionProduct::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getFormattedAmountAttribute(): string
    {
        return number_format($this->amount / 100, 2, ',', '.');
    }
}

