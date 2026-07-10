<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Trade extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'symbol',
        'qty',
        'fill_price',
        'commission',
        'executed_at',
    ];

    protected function casts(): array
    {
        return [
            'qty' => 'integer',
            'fill_price' => 'decimal:4',
            'commission' => 'decimal:2',
            'executed_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
