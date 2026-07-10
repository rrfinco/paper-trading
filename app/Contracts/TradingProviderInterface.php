<?php

namespace App\Contracts;

use App\Models\Account;
use App\Models\Order;

interface TradingProviderInterface
{
    /**
     * Submit an order for execution.
     *
     * @param Account $account
     * @param array $params
     * @return Order
     */
    public function placeOrder(Account $account, array $params): Order;

    /**
     * Cancel an active working order.
     *
     * @param Account $account
     * @param string $clientOrderId
     * @return array|null
     */
    public function cancelOrder(Account $account, string $clientOrderId): ?array;

    /**
     * Fetch active open positions for the account.
     *
     * @param Account $account
     * @return array
     */
    public function getPositions(Account $account): array;

    /**
     * Fetch active working or executed orders for the account.
     *
     * @param Account $account
     * @return array
     */
    public function getOrders(Account $account): array;

    /**
     * Fetch detailed snapshot balance metrics for the account.
     *
     * @param Account $account
     * @return array
     */
    public function getBalance(Account $account): array;
}
