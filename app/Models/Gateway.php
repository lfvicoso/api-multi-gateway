<?php

namespace App\Models;

use Database\Factories\GatewayFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Gateway extends Model
{
    use HasFactory, SoftDeletes;

    protected static function newFactory()
    {
        return GatewayFactory::new();
    }

    protected $fillable = [
        'name',
        'url',
        'is_active',
        'priority',
        'type',
        'credentials',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'priority' => 'integer',
        'credentials' => 'array',
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrderedByPriority($query)
    {
        return $query->orderBy('priority', 'asc');
    }
}

