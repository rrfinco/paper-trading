<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'tradezero_key_id',
        'tradezero_secret_key',
        'tradezero_account_id',
        'tradezero_response',
        'tradezero_account_details',
    ];

    /**
     * Check if the user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'tradezero_secret_key' => 'encrypted',
            'tradezero_response' => 'array',
            'tradezero_account_details' => 'array',
        ];
    }

    /**
     * Get the broker account associated with the user.
     */
    public function brokerAccount(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(BrokerAccount::class);
    }

    /**
     * Get the TradeZero accounts linked to the user.
     */
    public function tradeZeroAccounts(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TradeZeroAccount::class);
    }

    /**
     * Get the TradeZero orders placed by the user.
     */
    public function tradeZeroOrders(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TradeZeroOrder::class);
    }

    /**
     * Get the TradeZero locates requested by the user.
     */
    public function tradeZeroLocates(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TradeZeroLocate::class);
    }

    /**
     * Get the generic accounts owned by the user.
     */
    public function accounts(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Account::class);
    }

    /**
     * Get the generic orders placed by the user.
     */
    public function orders(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get the generic positions held by the user.
     */
    public function positions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Position::class);
    }
}

