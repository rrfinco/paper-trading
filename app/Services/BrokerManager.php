<?php

namespace App\Services;

use App\Contracts\TradingProviderInterface;
use App\Models\Account;
use InvalidArgumentException;

class BrokerManager
{
    /**
     * Resolve the trading provider driver for a specific account.
     *
     * @param Account $account
     * @return TradingProviderInterface
     */
    public function driver(Account $account): TradingProviderInterface
    {
        $provider = strtolower($account->provider);

        $method = 'create' . ucfirst($provider) . 'Driver';

        if (method_exists($this, $method)) {
            return $this->$method();
        }

        throw new InvalidArgumentException("Trading provider [{$provider}] is not supported.");
    }

    /**
     * Create Paper Trading Driver instance.
     */
    protected function createPaperDriver(): TradingProviderInterface
    {
        return app(\App\Services\TradingProviders\PaperTradingProvider::class);
    }

    /**
     * Create TradeZero Broker Driver instance.
     */
    protected function createTradezeroDriver(): TradingProviderInterface
    {
        return app(\App\Services\TradingProviders\TradeZeroProvider::class);
    }
}
