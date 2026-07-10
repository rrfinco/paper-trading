<?php

namespace App\Services\TradingProviders;

use App\Contracts\TradingProviderInterface;
use App\Models\Account;
use App\Models\Order;
use App\Models\Position;
use App\Models\Trade;
use App\Services\TradeZeroService;
use Exception;
use Illuminate\Support\Facades\Log;

class TradeZeroProvider implements TradingProviderInterface
{
    protected TradeZeroService $tradeZeroService;

    public function __construct(TradeZeroService $tradeZeroService)
    {
        $this->tradeZeroService = $tradeZeroService;
    }

    /**
     * Submit an order to TradeZero API.
     */
    public function placeOrder(Account $account, array $params): Order
    {
        $user = $account->user;
        $apiKey = $user->tradezero_key_id;
        $apiSecret = $user->tradezero_secret_key;

        if (empty($apiKey) || empty($apiSecret)) {
            throw new Exception("TradeZero credentials are not configured for this user.");
        }

        $symbol = strtoupper($params['symbol']);
        $quantity = (int) $params['quantity'];
        $securityType = $params['security_type'] ?? 'Stock';
        $side = $params['side'] ?? null;
        if ($side) {
            $side = ucfirst(strtolower($side));
        }
        $orderType = ucfirst(strtolower($params['order_type'] ?? 'Market'));
        $clientOrderId = $params['clientOrderId'] ?? (($securityType === 'Mleg' ? 'mleg-' : 'ord-') . $user->id . '-' . time() . '-' . rand(1000, 9999));

        $orderParams = [
            'clientOrderId' => $clientOrderId,
            'securityType' => $securityType,
            'symbol' => $symbol,
            'orderQuantity' => $quantity,
            'orderType' => $orderType,
            'timeInForce' => $params['time_in_force'] ?? 'Day',
        ];

        if ($orderType === 'Limit' && isset($params['limit_price'])) {
            $orderParams['limitPrice'] = (float) $params['limit_price'];
        }

        if (isset($params['route'])) {
            $orderParams['route'] = $params['route'];
        }

        if ($securityType === 'Mleg') {
            $legs = $params['legs'];
            // Sort legs by strike price ascending
            usort($legs, function($a, $b) {
                $strikeA = 0;
                $strikeB = 0;
                if (preg_match('/^([A-Z]{1,6})(\d{6})([CP])(\d{8})$/', $a['symbol'], $m)) {
                    $strikeA = (int) $m[4];
                }
                if (preg_match('/^([A-Z]{1,6})(\d{6})([CP])(\d{8})$/', $b['symbol'], $m)) {
                    $strikeB = (int) $m[4];
                }
                return $strikeA - $strikeB;
            });
            $orderParams['legs'] = $legs;
        } else {
            $orderParams['side'] = $side;
            $orderParams['openClose'] = 'Open';
        }

        // Place order via TradeZeroService
        $response = $this->tradeZeroService->placeOrder(
            $account->name, // the account code (e.g. TZ-DEMO-PAPER-1)
            $orderParams,
            $apiKey,
            $apiSecret
        );

        if (is_null($response)) {
            throw new Exception("TradeZero API failed to place order.");
        }

        $status = $response['orderStatus'] ?? 'Filled';
        $priceAvg = $response['priceAvg'] ?? ($orderParams['limitPrice'] ?? 127.80);

        // Save generic Order in local DB
        $order = Order::create([
            'account_id' => $account->id,
            'user_id' => $user->id,
            'symbol' => $symbol,
            'side' => $side ?? 'Buy',
            'quantity' => $quantity,
            'filled_quantity' => $status === 'Filled' ? $quantity : 0,
            'order_type' => $orderType,
            'security_type' => $securityType,
            'limit_price' => $orderParams['limitPrice'] ?? null,
            'status' => $status,
            'legs' => $orderParams['legs'] ?? null,
            'provider' => 'tradezero',
            'broker_order_id' => $response['brokerOrderId'] ?? null,
            'client_order_id' => $clientOrderId,
        ]);

        if ($status === 'Filled') {
            Trade::create([
                'order_id' => $order->id,
                'symbol' => $symbol,
                'qty' => $quantity,
                'fill_price' => $priceAvg,
                'commission' => 0.00,
                'executed_at' => now(),
            ]);

            // Sync positions local cache
            $this->getPositions($account);
        }

        return $order;
    }

    /**
     * Cancel an active order on TradeZero.
     */
    public function cancelOrder(Account $account, string $clientOrderId): ?array
    {
        $user = $account->user;
        $apiKey = $user->tradezero_key_id;
        $apiSecret = $user->tradezero_secret_key;

        $response = $this->tradeZeroService->cancelOrder(
            $account->name,
            $clientOrderId,
            $apiKey,
            $apiSecret
        );

        if ($response) {
            // Update local order status
            Order::where('account_id', $account->id)
                ->where('client_order_id', $clientOrderId)
                ->update(['status' => 'Cancelled']);
        }

        return $response;
    }

    /**
     * Fetch positions from TradeZero and sync local cache.
     */
    public function getPositions(Account $account): array
    {
        $user = $account->user;
        $apiKey = $user->tradezero_key_id;
        $apiSecret = $user->tradezero_secret_key;

        $response = $this->tradeZeroService->fetchAccountPositions($account->name, $apiKey, $apiSecret);
        $positions = $response['positions'] ?? [];

        // Sync local cache
        Position::where('account_id', $account->id)->delete();

        $formatted = [];
        foreach ($positions as $pos) {
            $qty = (int) ($pos['quantity'] ?? $pos['qty'] ?? 0);
            if ($qty === 0) continue;

            $side = $pos['side'] ?? 'Long';
            $avgPrice = (float) ($pos['avgPrice'] ?? $pos['averagePrice'] ?? 0.00);
            $currentPrice = (float) ($pos['close'] ?? $pos['lastPrice'] ?? $avgPrice);
            $unrealized = (float) ($pos['unrealized'] ?? 0.00);
            $unrealizedPct = (float) ($pos['unrealizedPct'] ?? 0.00);

            // Store in generic positions database
            Position::create([
                'account_id' => $account->id,
                'user_id' => $user->id,
                'symbol' => $pos['symbol'],
                'quantity' => $side === 'Short' ? -$qty : $qty,
                'avg_price' => $avgPrice,
            ]);

            $formatted[] = [
                'symbol' => $pos['symbol'],
                'quantity' => $qty,
                'avgPrice' => $avgPrice,
                'close' => $currentPrice,
                'marketValue' => (float) ($pos['marketValue'] ?? ($currentPrice * $qty)),
                'unrealized' => $unrealized,
                'unrealizedPct' => $unrealizedPct,
                'side' => $side,
                'securityType' => $pos['securityType'] ?? 'Stock',
            ];
        }

        return $formatted;
    }

    /**
     * Fetch active orders from TradeZero.
     */
    public function getOrders(Account $account): array
    {
        $user = $account->user;
        $apiKey = $user->tradezero_key_id;
        $apiSecret = $user->tradezero_secret_key;

        $response = $this->tradeZeroService->fetchAccountOrders($account->name, $apiKey, $apiSecret);
        $orders = $response['orders'] ?? [];

        return array_map(function ($ord) {
            return [
                'clientOrderId' => $ord['clientOrderId'] ?? '',
                'symbol' => $ord['symbol'] ?? '',
                'side' => $ord['side'] ?? 'Buy',
                'orderQuantity' => (int) ($ord['orderQuantity'] ?? 0),
                'orderType' => $ord['orderType'] ?? 'Market',
                'limitPrice' => isset($ord['limitPrice']) ? (float) $ord['limitPrice'] : null,
                'orderStatus' => $ord['orderStatus'] ?? 'New',
                'priceAvg' => (float) ($ord['priceAvg'] ?? 0.00),
                'created' => $ord['created'] ?? '',
                'securityType' => $ord['securityType'] ?? 'Stock',
                'legs' => $ord['legs'] ?? null,
            ];
        }, $orders);
    }

    /**
     * Fetch TradeZero snapshot balance details.
     */
    public function getBalance(Account $account): array
    {
        $user = $account->user;
        $apiKey = $user->tradezero_key_id;
        $apiSecret = $user->tradezero_secret_key;

        // Fetch concurrently in parallel!
        $data = $this->tradeZeroService->fetchDashboardDataParallel($account->name, $apiKey, $apiSecret);

        if (is_null($data)) {
            Log::error("Failed to sync TradeZero balances for account [{$account->name}]");
            return [];
        }

        $accountDetails = $data['account'];
        $pnl = $data['pnl'];
        $positions = $data['positions'];
        $orders = $data['orders'];

        // Sync local generic account balances in DB
        $account->update([
            'balance' => (float) ($pnl['availableCash'] ?? 0.00),
            'equity' => (float) ($pnl['accountValue'] ?? 0.00),
        ]);

        // Sync local positions database table cache
        Position::where('account_id', $account->id)->delete();
        $formattedPositions = [];
        foreach ($positions as $pos) {
            $qty = (int) ($pos['quantity'] ?? $pos['qty'] ?? 0);
            if ($qty === 0) continue;

            $side = $pos['side'] ?? 'Long';
            $avgPrice = (float) ($pos['avgPrice'] ?? $pos['averagePrice'] ?? 0.00);
            $currentPrice = (float) ($pos['close'] ?? $pos['lastPrice'] ?? $avgPrice);
            $unrealized = (float) ($pos['unrealized'] ?? 0.00);
            $unrealizedPct = (float) ($pos['unrealizedPct'] ?? 0.00);

            Position::create([
                'account_id' => $account->id,
                'user_id' => $user->id,
                'symbol' => $pos['symbol'],
                'quantity' => $side === 'Short' ? -$qty : $qty,
                'avg_price' => $avgPrice,
            ]);

            $formattedPositions[] = [
                'symbol' => $pos['symbol'],
                'quantity' => $qty,
                'avgPrice' => $avgPrice,
                'close' => $currentPrice,
                'marketValue' => (float) ($pos['marketValue'] ?? ($currentPrice * $qty)),
                'unrealized' => $unrealized,
                'unrealizedPct' => $unrealizedPct,
                'side' => $side,
                'securityType' => $pos['securityType'] ?? 'Stock',
            ];
        }

        // Sync and format orders
        $workingStatuses = ['New', 'PendingNew', 'Accepted', 'PartiallyFilled'];
        $openOrdersCount = 0;
        $formattedOrders = [];
        foreach ($orders as $ord) {
            $status = $ord['orderStatus'] ?? 'New';
            if (in_array($status, $workingStatuses)) {
                $openOrdersCount++;
            }
            $formattedOrders[] = [
                'clientOrderId' => $ord['clientOrderId'] ?? '',
                'symbol' => $ord['symbol'] ?? '',
                'side' => $ord['side'] ?? 'Buy',
                'orderQuantity' => (int) ($ord['orderQuantity'] ?? 0),
                'orderType' => $ord['orderType'] ?? 'Market',
                'limitPrice' => isset($ord['limitPrice']) ? (float) $ord['limitPrice'] : null,
                'orderStatus' => $status,
                'priceAvg' => (float) ($ord['priceAvg'] ?? 0.00),
                'created' => $ord['created'] ?? '',
                'securityType' => $ord['securityType'] ?? 'Stock',
                'legs' => $ord['legs'] ?? null,
            ];
        }

        return [
            'account' => $account->name,
            'accountStatus' => $accountDetails['accountStatus'] ?? 'Active',
            'accountType' => $accountDetails['accountType'] ?? 'Paper',
            'buyingPower' => (float) ($accountDetails['buyingPower'] ?? $accountDetails['bp'] ?? 0.00),
            'optionTradingLevel' => isset($accountDetails['optionTradingLevel']) ? (int) $accountDetails['optionTradingLevel'] : null,
            'marginRatio' => (float) ($accountDetails['marginRatio'] ?? 100.00),
            'marginRequirement' => (float) ($accountDetails['marginRequirement'] ?? 0.00),
            'sodEquity' => (float) ($accountDetails['sodEquity'] ?? 0.00),
            'optionCashTotalBalance' => (float) ($accountDetails['optionCashTotalBalance'] ?? 0.00),
            'totalCommissions' => (float) ($accountDetails['totalCommissions'] ?? 0.00),
            'totalLocateCosts' => (float) ($accountDetails['totalLocateCosts'] ?? 0.00),
            'marginDeficit' => (float) ($accountDetails['marginDeficit'] ?? 0.00),
            'accountValue' => (float) ($pnl['accountValue'] ?? 0.00),
            'availableCash' => (float) ($pnl['availableCash'] ?? 0.00),
            'dayPnl' => (float) ($pnl['dayPnl'] ?? 0.00),
            'dayRealized' => (float) ($pnl['dayRealized'] ?? 0.00),
            'dayUnrealized' => (float) ($pnl['dayUnrealized'] ?? 0.00),
            'exposure' => (float) ($pnl['exposure'] ?? 0.00),
            'usedLeverage' => (float) ($pnl['usedLeverage'] ?? 0.00),
            'allowedLeverage' => (float) ($pnl['allowedLeverage'] ?? $accountDetails['leverage'] ?? 4.00),
            'positionsCount' => count($formattedPositions),
            'openOrdersCount' => $openOrdersCount,
            'positions' => $formattedPositions,
            'orders' => $formattedOrders
        ];
    }

}
