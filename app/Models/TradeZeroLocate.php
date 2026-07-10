<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TradeZeroLocate extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tradezero_locates';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'account',
        'symbol',
        'quantity',
        'quote_req_id',
        'locate_status',
        'locate_price',
        'locate_type',
        'available_quantity',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'locate_status' => 'integer',
            'locate_price' => 'decimal:4',
            'locate_type' => 'integer',
            'available_quantity' => 'integer',
        ];
    }

    /**
     * Get the user that requested the locate.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
