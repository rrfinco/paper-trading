<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_id',
        'user_id',
        'symbol',
        'side',
        'quantity',
        'filled_quantity',
        'order_type',
        'security_type',
        'limit_price',
        'stop_price',
        'status',
        'legs',
        'provider',
        'broker_order_id',
        'client_order_id',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'filled_quantity' => 'integer',
            'limit_price' => 'decimal:4',
            'stop_price' => 'decimal:4',
            'legs' => 'array',
        ];
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function trades(): HasMany
    {
        return $this->hasMany(Trade::class);
    }
}
