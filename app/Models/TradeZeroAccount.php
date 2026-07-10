<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TradeZeroAccount extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tradezero_accounts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'account',
        'account_status',
        'account_type',
        'available_cash',
        'available_cash_ems',
        'buying_power',
        'equity',
        'is_future_account',
        'leverage',
        'maintenance_deficit',
        'margin_deficit',
        'margin_ratio',
        'margin_requirement',
        'opt_contracts_traded',
        'opt_level',
        'option_cash_total_balance',
        'option_trading_level',
        'overnight_bp',
        'realized',
        'shares_traded',
        'sod_equity',
        'total_commissions',
        'total_locate_costs',
        'unrealized',
        'used_leverage',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'available_cash' => 'decimal:2',
            'available_cash_ems' => 'decimal:2',
            'buying_power' => 'decimal:2',
            'equity' => 'decimal:2',
            'is_future_account' => 'boolean',
            'leverage' => 'decimal:2',
            'maintenance_deficit' => 'decimal:2',
            'margin_deficit' => 'decimal:2',
            'margin_ratio' => 'decimal:2',
            'margin_requirement' => 'decimal:2',
            'opt_contracts_traded' => 'integer',
            'opt_level' => 'integer',
            'option_cash_total_balance' => 'decimal:2',
            'option_trading_level' => 'integer',
            'overnight_bp' => 'decimal:2',
            'realized' => 'decimal:2',
            'shares_traded' => 'integer',
            'sod_equity' => 'decimal:2',
            'total_commissions' => 'decimal:2',
            'total_locate_costs' => 'decimal:2',
            'unrealized' => 'decimal:2',
            'used_leverage' => 'decimal:2',
        ];
    }

    /**
     * Get the user that owns the TradeZero account.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
