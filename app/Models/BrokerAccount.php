<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BrokerAccount extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'account_id',
        'api_key',
        'api_secret',
        'status',
        'account_type',
        'buying_power',
        'cash_balance',
        'equity',
        'trading_permissions',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'api_key' => 'encrypted',
            'api_secret' => 'encrypted',
            'trading_permissions' => 'array',
            'buying_power' => 'decimal:2',
            'cash_balance' => 'decimal:2',
            'equity' => 'decimal:2',
        ];
    }

    /**
     * Get the user that owns the broker account.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
