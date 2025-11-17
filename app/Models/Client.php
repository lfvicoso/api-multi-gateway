<?php

namespace App\Models;

use Database\Factories\ClientFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory, SoftDeletes;

    protected static function newFactory()
    {
        return ClientFactory::new();
    }

    protected $fillable = [
        'name',
        'email',
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}

