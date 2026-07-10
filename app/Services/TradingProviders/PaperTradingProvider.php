<?php

namespace App\Services\TradingProviders;

use App\Contracts\TradingProviderInterface;
use App\Models\Account;
use App\Models\Order;
use App\Models\Position;
use App\Models\Trade;
use Illuminate\Support\Facades\DB;

class PaperTradingProvider implements TradingProviderInterface
{
    /**
     * Submit an order for execution (instantly fills in paper trading).
     */
    public function placeOrder(Account $account, array $params): Order
    {
        return DB::transaction(function () use ($account, $params) {
            $symbol = strtoupper($params['symbol']);
            $side = ucfirst(strtolower($params['side'] ?? 'Buy')); // 'Buy', 'Sell', 'Short', 'Cover'
            $quantity = (int) $params['quantity'];
            $orderType = ucfirst(strtolower($params['order_type'] ?? 'Market')); // 'Market', 'Limit'
            $limitPrice = isset($params['limit_price']) ? (float) $params['limit_price'] : null;
            $legs = $params['legs'] ?? null;
            $clientOrderId = $params['clientOrderId'] ?? ('paper-ord-' . $account->user_id . '-' . time() . '-' . rand(1000, 9999));

            // Default execution fill price
            $fillPrice = 1326.15; // default copper price fallback
            if ($orderType === 'Limit' && $limitPrice) {
                $fillPrice = $limitPrice;
            } elseif (isset($params['legs']) && is_array($params['legs'])) {
                // Option Strategy net debit
                $fillPrice = isset($params['net_debit']) ? (float)$params['net_debit'] : 3.00;
            }

            // Create Order
            $order = Order::create([
                'account_id' => $account->id,
                'user_id' => $account->user_id,
                'symbol' => $symbol,
                'side' => $side,
                'quantity' => $quantity,
                'filled_quantity' => $quantity,
                'order_type' => $orderType,
                'security_type' => $params['security_type'] ?? 'Stock',
                'limit_price' => $limitPrice,
                'status' => 'Filled',
                'legs' => $legs,
                'provider' => 'paper',
                'client_order_id' => $clientOrderId,
            ]);

            // Create Trade fill record
            Trade::create([
                'order_id' => $order->id,
                'symbol' => $symbol,
                'qty' => $quantity,
                'fill_price' => $fillPrice,
                'commission' => 0.00,
                'executed_at' => now(),
            ]);

            // Math direction: Buy / Cover is +quantity, Sell / Short is -quantity
            $netChange = in_array($side, ['Buy', 'Cover']) ? $quantity : -$quantity;
            $totalCost = $quantity * $fillPrice;

            // Deduct / add cash balance
            // Buying or covering costs cash (-balance). Selling or shorting credits cash (+balance).
            if (in_array($side, ['Buy', 'Cover'])) {
                $account->balance -= $totalCost;
            } else {
                $account->balance += $totalCost;
            }
            $account->save();

            // Find or create position
            $position = Position::where('account_id', $account->id)
                ->where('symbol', $symbol)
                ->first();

            if (!$position) {
                if ($netChange !== 0) {
                    Position::create([
                        'account_id' => $account->id,
                        'user_id' => $account->user_id,
                        'symbol' => $symbol,
                        'quantity' => $netChange,
                        'avg_price' => $fillPrice,
                    ]);
                }
            } else {
                $existingQty = $position->quantity;
                $existingAvg = (float) $position->avg_price;
                $newQty = $existingQty + $netChange;

                if ($newQty == 0) {
                    $position->delete();
                } else {
                    // Average price changes only when adding to a position (matching signs)
                    $isAdding = ($existingQty > 0 && $netChange > 0) || ($existingQty < 0 && $netChange < 0);
                    if ($isAdding) {
                        $newAvg = (($existingQty * $existingAvg) + ($netChange * $fillPrice)) / $newQty;
                    } else {
                        $newAvg = $existingAvg;
                    }

                    $position->update([
                        'quantity' => $newQty,
                        'avg_price' => abs($newAvg),
                    ]);
                }
            }

            // Recalculate account equity
            $this->updateAccountEquity($account);

            return $order;
        });
    }

    /**
     * Cancel an active order (instantly filled in paper mode, so returns error message).
     */
    public function cancelOrder(Account $account, string $clientOrderId): ?array
    {
        $order = Order::where('account_id', $account->id)
            ->where('client_order_id', $clientOrderId)
            ->first();

        if (!$order) {
            return null;
        }

        if ($order->status === 'Filled') {
            return [
                'success' => false,
                'message' => 'Cannot cancel a filled paper trading order.'
            ];
        }

        $order->update(['status' => 'Cancelled']);

        return [
            'success' => true,
            'clientOrderId' => $clientOrderId,
            'orderStatus' => 'Cancelled'
        ];
    }

    /**
     * Fetch positions formatted for the frontend.
     */
    public function getPositions(Account $account): array
    {
        $positions = Position::where('account_id', $account->id)->get();

        return $positions->map(function ($pos) {
            $qty = abs($pos->quantity);
            $side = $pos->quantity > 0 ? 'Long' : 'Short';
            $avgPrice = (float) $pos->avg_price;
            $currentPrice = $avgPrice; // Offline fallback price

            // Calculate unrealized P&L
            $unrealized = ($currentPrice - $avgPrice) * ($pos->quantity > 0 ? 1 : -1) * $qty;
            $unrealizedPct = $avgPrice > 0 ? ($unrealized / ($avgPrice * $qty)) * 100 : 0.00;

            return [
                'symbol' => $pos->symbol,
                'quantity' => $qty,
                'avgPrice' => $avgPrice,
                'close' => $currentPrice,
                'marketValue' => $currentPrice * $qty,
                'unrealized' => $unrealized,
                'unrealizedPct' => $unrealizedPct,
                'side' => $side,
                'securityType' => str_contains($pos->symbol, '17') || strlen($pos->symbol) > 8 ? 'Option' : 'Stock'
            ];
        })->toArray();
    }

    /**
     * Fetch orders formatted for frontend.
     */
    public function getOrders(Account $account): array
    {
        $orders = Order::where('account_id', $account->id)
            ->latest()
            ->take(30)
            ->get();

        return $orders->map(function ($ord) {
            return [
                'clientOrderId' => $ord->client_order_id,
                'symbol' => $ord->symbol,
                'side' => $ord->side,
                'orderQuantity' => $ord->quantity,
                'orderType' => $ord->order_type,
                'limitPrice' => $ord->limit_price ? (float) $ord->limit_price : null,
                'orderStatus' => $ord->status,
                'priceAvg' => $ord->limit_price ? (float) $ord->limit_price : 1326.15,
                'created' => $ord->created_at->toIso8601String(),
                'securityType' => str_contains($ord->symbol, '17') || strlen($ord->symbol) > 8 ? 'Option' : 'Stock',
                'legs' => $ord->legs
            ];
        })->toArray();
    }

    /**
     * Fetch balance snapshot metrics.
     */
    public function getBalance(Account $account): array
    {
        $this->updateAccountEquity($account);

        $positionsCount = Position::where('account_id', $account->id)->count();
        $openOrdersCount = Order::where('account_id', $account->id)
            ->whereIn('status', ['new', 'pending', 'PartiallyFilled'])
            ->count();

        // Basic calculation
        $buyingPower = $account->balance * 4.00; // 4x intraday leverage
        $unrealized = 0.00; // In simple offline mode, current price = average entry price

        return [
            'account' => $account->name,
            'accountStatus' => 'Active',
            'accountType' => 'Paper',
            'buyingPower' => (float) $buyingPower,
            'optionTradingLevel' => 2,
            'marginRatio' => 100.00,
            'marginRequirement' => 0.00,
            'sodEquity' => (float) $account->equity,
            'optionCashTotalBalance' => 0.00,
            'totalCommissions' => 0.00,
            'totalLocateCosts' => 0.00,
            'marginDeficit' => 0.00,
            'accountValue' => (float) $account->equity,
            'availableCash' => (float) $account->balance,
            'dayPnl' => 0.00,
            'dayRealized' => 0.00,
            'dayUnrealized' => $unrealized,
            'exposure' => 0.00,
            'usedLeverage' => 0.00,
            'allowedLeverage' => 4.00,
            'positionsCount' => $positionsCount,
            'openOrdersCount' => $openOrdersCount,
            'positions' => $this->getPositions($account),
            'orders' => $this->getOrders($account)
        ];
    }

    /**
     * Recalculate equity based on position valuations.
     */
    protected function updateAccountEquity(Account $account): void
    {
        $positions = Position::where('account_id', $account->id)->get();
        $holdingsValue = 0.00;

        foreach ($positions as $pos) {
            // Valuation = quantity * price.
            // Positive quantity represents Long (+cash value), negative represents Short (liability).
            $price = (float) $pos->avg_price;
            $holdingsValue += $pos->quantity * $price;
        }

        $account->equity = $account->balance + $holdingsValue;
        $account->save();
    }
}
