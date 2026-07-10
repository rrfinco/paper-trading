<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TradeZeroService
{
    protected string $baseUrl;
    protected bool $sandboxMode;

    public function __construct()
    {
        $this->baseUrl = env('TRADEZERO_API_URL', 'https://webapi.tradezero.com');
        $this->sandboxMode = (bool) env('TRADEZERO_SANDBOX_MODE', true);
    }

    /**
     * Fetch all accounts associated with the API keys.
     *
     * @param string $apiKey
     * @param string $apiSecret
     * @return array|null
     */
    public function fetchAccountsList(string $apiKey, string $apiSecret): ?array
    {
        if (empty($apiKey) || empty($apiSecret)) {
            return null;
        }

        if (str_starts_with(strtoupper($apiKey), 'INVALID')) {
            return null;
        }

        // Detect if we should use sandbox mode based on the key
        $useSandbox = $this->sandboxMode && (
            str_contains(strtolower($apiKey), 'demo') ||
            str_contains(strtolower($apiKey), 'mock') ||
            str_contains(strtolower($apiKey), 'test')
        );

        if ($useSandbox) {
            // Simulate Paper vs Live based on key content
            $isLive = str_contains(strtolower($apiKey), 'live');

            return [
                'accounts' => [
                    [
                        "account" => $isLive ? 'TZ-DEMO-LIVE-1' : 'TZ-DEMO-PAPER-1',
                        "accountStatus" => "Active",
                        "accountType" => $isLive ? 'Live' : 'Paper',
                        "availableCash" => 112000.00,
                        "availableCashEMS" => 0.00,
                        "buyingPower" => 450000.00,
                        "equity" => 118432.18,
                        "isFutureAccount" => false,
                        "leverage" => 4.00,
                        "maintenanceDeficit" => 0.00,
                        "marginDeficit" => 0.00,
                        "marginRatio" => 100.00,
                        "marginRequirement" => 0.00,
                        "optContractsTraded" => 12,
                        "optLevel" => 2,
                        "optionCashTotalBalance" => 30000.00,
                        "optionTradingLevel" => 2,
                        "overnightBp" => 220000.00,
                        "realized" => 450.50,
                        "sharesTraded" => 5000,
                        "sodEquity" => 117981.68,
                        "totalCommissions" => 75.25,
                        "totalLocateCosts" => 15.00,
                        "unrealized" => 120.50,
                        "usedLeverage" => 2.10
                    ],
                    [
                        "account" => $isLive ? 'TZ-DEMO-LIVE-2' : 'TZ-DEMO-PAPER-2',
                        "accountStatus" => "Active",
                        "accountType" => $isLive ? 'Live' : 'Paper',
                        "availableCash" => 60000.00,
                        "availableCashEMS" => 0.00,
                        "buyingPower" => 250000.00,
                        "equity" => 64000.00,
                        "isFutureAccount" => false,
                        "leverage" => 4.00,
                        "maintenanceDeficit" => 0.00,
                        "marginDeficit" => 0.00,
                        "marginRatio" => 100.00,
                        "marginRequirement" => 0.00,
                        "optContractsTraded" => 5,
                        "optLevel" => 2,
                        "optionCashTotalBalance" => 15000.00,
                        "optionTradingLevel" => 2,
                        "overnightBp" => 120000.00,
                        "realized" => 150.00,
                        "sharesTraded" => 2500,
                        "sodEquity" => 63850.00,
                        "totalCommissions" => 25.00,
                        "totalLocateCosts" => 0.00,
                        "unrealized" => -50.00,
                        "usedLeverage" => 1.50
                    ]
                ]
            ];
        }

        try {
            // Live HTTP API call
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'TZ-API-KEY-ID' => $apiKey,
                'TZ-API-SECRET-KEY' => $apiSecret,
            ])->get("{$this->baseUrl}/v1/api/accounts");

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('TradeZero accounts request failed', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
        } catch (\Exception $e) {
            Log::error('TradeZero Accounts API Exception: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Fetch detailed account metrics for a single TradeZero account.
     *
     * @param string $accountId
     * @param string $apiKey
     * @param string $apiSecret
     * @return array|null
     */
    public function fetchAccountDetails(string $accountId, string $apiKey, string $apiSecret): ?array
    {
        // Detect if we should use sandbox mode based on the key
        $useSandbox = $this->sandboxMode && (
            str_contains(strtolower($apiKey), 'demo') ||
            str_contains(strtolower($apiKey), 'mock') ||
            str_contains(strtolower($apiKey), 'test')
        );

        if ($useSandbox) {
            $isSecond = str_ends_with($accountId, '-2');
            $isLive = str_contains(strtolower($apiKey), 'live');

            return [
                "account" => $accountId,
                "accountStatus" => "Active",
                "accountType" => $isLive ? 'Live' : 'Paper',
                "availableCash" => $isSecond ? 60000.00 : 112000.00,
                "availableCashEMS" => 0.00,
                "buyingPower" => $isSecond ? 250000.00 : 450000.00,
                "equity" => $isSecond ? 64000.00 : 118432.18,
                "isFutureAccount" => false,
                "leverage" => 4.00,
                "maintenanceDeficit" => 0.00,
                "marginDeficit" => 0.00,
                "marginRatio" => 100.00,
                "marginRequirement" => 0.00,
                "optContractsTraded" => $isSecond ? 5 : 12,
                "optLevel" => 2,
                "optionCashTotalBalance" => $isSecond ? 15000.00 : 30000.00,
                "optionTradingLevel" => 2,
                "overnightBp" => $isSecond ? 120000.00 : 220000.00,
                "realized" => $isSecond ? 150.00 : 450.50,
                "sharesTraded" => $isSecond ? 2500 : 5000,
                "sodEquity" => $isSecond ? 63850.00 : 117981.68,
                "totalCommissions" => $isSecond ? 25.00 : 75.25,
                "totalLocateCosts" => $isSecond ? 0.00 : 15.00,
                "unrealized" => $isSecond ? -50.00 : 120.50,
                "usedLeverage" => $isSecond ? 1.50 : 2.10
            ];
        }

        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'TZ-API-KEY-ID' => $apiKey,
                'TZ-API-SECRET-KEY' => $apiSecret,
            ])->get("{$this->baseUrl}/v1/api/account/{$accountId}");

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('TradeZero account details request failed', [
                'accountId' => $accountId,
                'status' => $response->status(),
                'body' => $response->body()
            ]);
        } catch (\Exception $e) {
            Log::error('TradeZero Account Details API Exception: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Place an order for a single TradeZero account.
     *
     * @param string $accountId
     * @param array $orderParams
     * @param string $apiKey
     * @param string $apiSecret
     * @return array|null
     */
    public function placeOrder(string $accountId, array $orderParams, string $apiKey, string $apiSecret): ?array
    {
        // Detect if we should use sandbox mode based on the key
        $useSandbox = $this->sandboxMode && (
            str_contains(strtolower($apiKey), 'demo') ||
            str_contains(strtolower($apiKey), 'mock') ||
            str_contains(strtolower($apiKey), 'test')
        );

        if ($useSandbox) {
            $isMleg = isset($orderParams['securityType']) && $orderParams['securityType'] === 'Mleg';
            // Mock success response
            return [
                "clientOrderId" => $orderParams['clientOrderId'] ?? ('mock-order-' . uniqid()),
                "orderStatus" => "Filled",
                "priceAvg" => $orderParams['limitPrice'] ?? 127.80,
                "executed" => true,
                "legCount" => $isMleg ? count($orderParams['legs'] ?? []) : 0,
            ];
        }

        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'TZ-API-KEY-ID' => $apiKey,
                'TZ-API-SECRET-KEY' => $apiSecret,
            ])->post("{$this->baseUrl}/v1/api/accounts/{$accountId}/order", $orderParams);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('TradeZero place order request failed', [
                'accountId' => $accountId,
                'status' => $response->status(),
                'body' => $response->body()
            ]);
        } catch (\Exception $e) {
            Log::error('TradeZero Place Order API Exception: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Check Easy-To-Borrow status of a symbol.
     */
    public function checkEasyToBorrow(string $accountId, string $symbol, string $apiKey, string $apiSecret): ?array
    {
        $useSandbox = $this->sandboxMode && (
            str_contains(strtolower($apiKey), 'demo') ||
            str_contains(strtolower($apiKey), 'mock') ||
            str_contains(strtolower($apiKey), 'test')
        );

        if ($useSandbox) {
            $symbolUpper = strtoupper($symbol);
            $isEtb = !in_array($symbolUpper, ['AMC', 'GME', 'BBBY']);
            return ['isEasyToBorrow' => $isEtb];
        }

        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'TZ-API-KEY-ID' => $apiKey,
                'TZ-API-SECRET-KEY' => $apiSecret,
            ])->get("{$this->baseUrl}/v1/api/accounts/{$accountId}/is-easy-to-borrow/symbol/" . strtoupper($symbol));

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('TradeZero check ETB request failed', [
                'symbol' => $symbol,
                'status' => $response->status(),
                'body' => $response->body()
            ]);
        } catch (\Exception $e) {
            Log::error('TradeZero check ETB API Exception: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Request a locate quote.
     */
    public function requestLocateQuote(string $accountId, string $symbol, int $quantity, string $quoteReqId, string $apiKey, string $apiSecret): ?array
    {
        $useSandbox = $this->sandboxMode && (
            str_contains(strtolower($apiKey), 'demo') ||
            str_contains(strtolower($apiKey), 'mock') ||
            str_contains(strtolower($apiKey), 'test')
        );

        if ($useSandbox) {
            $userId = auth()->id();
            if ($userId) {
                // Pre-Borrow quote (locateType 3)
                \App\Models\TradeZeroLocate::create([
                    'user_id' => $userId,
                    'account' => $accountId,
                    'symbol' => strtoupper($symbol),
                    'quantity' => $quantity,
                    'quote_req_id' => $quoteReqId,
                    'locate_status' => 65, // Offered
                    'locate_price' => 0.0350,
                    'locate_type' => 3,
                    'available_quantity' => 0
                ]);

                // Single Use quote (locateType 4)
                \App\Models\TradeZeroLocate::create([
                    'user_id' => $userId,
                    'account' => $accountId,
                    'symbol' => strtoupper($symbol),
                    'quantity' => $quantity,
                    'quote_req_id' => $quoteReqId . '.SU',
                    'locate_status' => 65, // Offered
                    'locate_price' => 0.0090,
                    'locate_type' => 4,
                    'available_quantity' => 0
                ]);
            }

            return ['locateQuoteSent' => 'true'];
        }

        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'TZ-API-KEY-ID' => $apiKey,
                'TZ-API-SECRET-KEY' => $apiSecret,
            ])->post("{$this->baseUrl}/v1/api/accounts/locates/quote", [
                'account' => $accountId,
                'symbol' => strtoupper($symbol),
                'quantity' => $quantity,
                'quoteReqID' => $quoteReqId
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('TradeZero locate quote request failed', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
        } catch (\Exception $e) {
            Log::error('TradeZero locate quote API Exception: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Poll history for priced offers.
     */
    public function fetchLocatesHistory(string $accountId, string $apiKey, string $apiSecret): ?array
    {
        $useSandbox = $this->sandboxMode && (
            str_contains(strtolower($apiKey), 'demo') ||
            str_contains(strtolower($apiKey), 'mock') ||
            str_contains(strtolower($apiKey), 'test')
        );

        if ($useSandbox) {
            $userId = auth()->id();
            $locates = \App\Models\TradeZeroLocate::where('user_id', $userId)
                ->where('account', $accountId)
                ->latest()
                ->take(30)
                ->get();

            $history = $locates->map(function ($l) {
                return [
                    'quoteReqID' => $l->quote_req_id,
                    'symbol' => $l->symbol,
                    'quantity' => $l->quantity,
                    'locatePrice' => (float) $l->locate_price,
                    'locateType' => $l->locate_type,
                    'locateStatus' => $l->locate_status,
                    'text' => $l->locate_status === 50 ? 'Filled' : ($l->locate_status === 65 ? 'Offered' : 'Expired')
                ];
            })->toArray();

            return ['locateHistory' => $history];
        }

        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'TZ-API-KEY-ID' => $apiKey,
                'TZ-API-SECRET-KEY' => $apiSecret,
            ])->get("{$this->baseUrl}/v1/api/accounts/{$accountId}/locates/history");

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('TradeZero fetch locate history failed', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
        } catch (\Exception $e) {
            Log::error('TradeZero fetch locate history API Exception: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Accept locate quote.
     */
    public function acceptLocateQuote(string $accountId, string $quoteReqId, string $apiKey, string $apiSecret): ?array
    {
        $useSandbox = $this->sandboxMode && (
            str_contains(strtolower($apiKey), 'demo') ||
            str_contains(strtolower($apiKey), 'mock') ||
            str_contains(strtolower($apiKey), 'test')
        );

        if ($useSandbox) {
            $userId = auth()->id();
            $locate = \App\Models\TradeZeroLocate::where('user_id', $userId)
                ->where('account', $accountId)
                ->where('quote_req_id', $quoteReqId)
                ->first();

            if ($locate) {
                $locate->update([
                    'locate_status' => 50, // Filled
                    'available_quantity' => $locate->quantity
                ]);

                // Auto expire other quotes from same locate request ID
                $baseId = str_replace('.SU', '', $quoteReqId);
                \App\Models\TradeZeroLocate::where('user_id', $userId)
                    ->where('account', $accountId)
                    ->whereIn('quote_req_id', [$baseId, $baseId . '.SU'])
                    ->where('id', '!=', $locate->id)
                    ->update(['locate_status' => 67]); // Expired
            }

            return ['success' => true];
        }

        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'TZ-API-KEY-ID' => $apiKey,
                'TZ-API-SECRET-KEY' => $apiSecret,
            ])->post("{$this->baseUrl}/v1/api/accounts/locates/accept", [
                'accountId' => $accountId,
                'quoteReqID' => $quoteReqId
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('TradeZero accept locate quote failed', [
                'quoteReqId' => $quoteReqId,
                'status' => $response->status(),
                'body' => $response->body()
            ]);
        } catch (\Exception $e) {
            Log::error('TradeZero accept locate quote API Exception: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Cancel a locate quote request.
     */
    public function cancelLocateQuote(string $accountId, string $quoteReqId, string $apiKey, string $apiSecret): ?array
    {
        $useSandbox = $this->sandboxMode && (
            str_contains(strtolower($apiKey), 'demo') ||
            str_contains(strtolower($apiKey), 'mock') ||
            str_contains(strtolower($apiKey), 'test')
        );

        if ($useSandbox) {
            $userId = auth()->id();
            $baseId = str_replace('.SU', '', $quoteReqId);
            
            \App\Models\TradeZeroLocate::where('user_id', $userId)
                ->where('account', $accountId)
                ->whereIn('quote_req_id', [$baseId, $baseId . '.SU'])
                ->update(['locate_status' => 67]); // Expired/Cancelled

            return ['success' => true];
        }

        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'TZ-API-KEY-ID' => $apiKey,
                'TZ-API-SECRET-KEY' => $apiSecret,
            ])->delete("{$this->baseUrl}/v1/api/accounts/locates/cancel/accounts/{$accountId}/quoteReqID/{$quoteReqId}");

            if ($response->successful()) {
                $userId = auth()->id();
                $baseId = str_replace('.SU', '', $quoteReqId);
                
                \App\Models\TradeZeroLocate::where('user_id', $userId)
                    ->where('account', $accountId)
                    ->whereIn('quote_req_id', [$baseId, $baseId . '.SU'])
                    ->update(['locate_status' => 67]); // Expired/Cancelled

                return $response->json();
            }

            Log::error('TradeZero cancel locate quote failed', [
                'accountId' => $accountId,
                'quoteReqId' => $quoteReqId,
                'status' => $response->status(),
                'body' => $response->body()
            ]);
        } catch (\Exception $e) {
            Log::error('TradeZero cancel locate quote API Exception: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Return/sell back borrowed shares from locate inventory.
     */
    public function sellBackLocate(string $accountId, array $params, string $apiKey, string $apiSecret): ?array
    {
        $useSandbox = $this->sandboxMode && (
            str_contains(strtolower($apiKey), 'demo') ||
            str_contains(strtolower($apiKey), 'mock') ||
            str_contains(strtolower($apiKey), 'test')
        );

        $symbol = strtoupper($params['symbol'] ?? '');
        $quantity = (int) ($params['quantity'] ?? 0);
        $quoteReqId = $params['quoteReqID'] ?? '';
        $locateType = $params['locateType'] ?? 'Unknown';

        if ($useSandbox) {
            $userId = auth()->id();
            $locate = \App\Models\TradeZeroLocate::where('user_id', $userId)
                ->where('account', $accountId)
                ->where('symbol', $symbol)
                ->where('quote_req_id', $quoteReqId)
                ->where('locate_status', 50)
                ->first();

            if ($locate) {
                if ($locate->available_quantity < $quantity) {
                    return null;
                }
                $locate->decrement('available_quantity', $quantity);
                return ['success' => true];
            }
            return null;
        }

        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'TZ-API-KEY-ID' => $apiKey,
                'TZ-API-SECRET-KEY' => $apiSecret,
            ])->post("{$this->baseUrl}/v1/api/accounts/locates/sell", [
                'account' => $accountId,
                'locateType' => $locateType,
                'quantity' => $quantity,
                'quoteReqID' => $quoteReqId,
                'symbol' => $symbol
            ]);

            if ($response->successful()) {
                $userId = auth()->id();
                $locate = \App\Models\TradeZeroLocate::where('user_id', $userId)
                    ->where('account', $accountId)
                    ->where('symbol', $symbol)
                    ->where('quote_req_id', $quoteReqId)
                    ->where('locate_status', 50)
                    ->first();
                if ($locate) {
                    $locate->decrement('available_quantity', min($locate->available_quantity, $quantity));
                }
                return $response->json();
            }

            Log::error('TradeZero sell back locates failed', [
                'accountId' => $accountId,
                'params' => $params,
                'status' => $response->status(),
                'body' => $response->body()
            ]);
        } catch (\Exception $e) {
            Log::error('TradeZero sell back locates API Exception: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Fetch locate inventory.
     */
    public function fetchLocatesInventory(string $accountId, string $apiKey, string $apiSecret): ?array
    {
        $useSandbox = $this->sandboxMode && (
            str_contains(strtolower($apiKey), 'demo') ||
            str_contains(strtolower($apiKey), 'mock') ||
            str_contains(strtolower($apiKey), 'test')
        );

        if ($useSandbox) {
            $userId = auth()->id();
            $locates = \App\Models\TradeZeroLocate::where('user_id', $userId)
                ->where('account', $accountId)
                ->where('locate_status', 50)
                ->where('available_quantity', '>', 0)
                ->get();

            $inventory = $locates->map(function ($l) {
                return [
                    'symbol' => $l->symbol,
                    'available' => $l->available_quantity,
                    'locatePrice' => (float) $l->locate_price,
                    'locateType' => $l->locate_type,
                    'quoteReqID' => $l->quote_req_id
                ];
            })->toArray();

            return ['locateInventory' => $inventory];
        }


        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'TZ-API-KEY-ID' => $apiKey,
                'TZ-API-SECRET-KEY' => $apiSecret,
            ])->get("{$this->baseUrl}/v1/api/accounts/{$accountId}/locates/inventory");

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('TradeZero fetch locate inventory failed', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
        } catch (\Exception $e) {
            Log::error('TradeZero fetch locate inventory API Exception: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Fetch details of a single order by clientOrderId.
     */
    public function fetchOrderDetails(string $accountId, string $clientOrderId, string $apiKey, string $apiSecret): ?array
    {
        $useSandbox = $this->sandboxMode && (
            str_contains(strtolower($apiKey), 'demo') ||
            str_contains(strtolower($apiKey), 'mock') ||
            str_contains(strtolower($apiKey), 'test')
        );

        if ($useSandbox) {
            $userId = auth()->id();
            $order = \App\Models\Order::where('user_id', $userId)
                ->where('client_order_id', $clientOrderId)
                ->first();

            if ($order) {
                $legs = [];
                if ($order->security_type === 'Mleg' && is_array($order->legs)) {
                    foreach ($order->legs as $leg) {
                        $legs[] = [
                            'symbol' => $leg['symbol'] ?? '',
                            'side' => $leg['side'] ?? 'Buy',
                            'ratio' => $leg['ratio'] ?? 1,
                            'openClose' => $leg['openClose'] ?? 'Open',
                            'qty' => $order->quantity * ($leg['ratio'] ?? 1),
                            'lastQty' => $order->quantity * ($leg['ratio'] ?? 1),
                            'lvsQty' => 0,
                            'lastPx' => (float) $order->limit_price,
                            'avgPx' => (float) $order->limit_price,
                            'cxlQty' => 0
                        ];
                    }
                }

                return [
                    'clientOrderId' => $order->client_order_id,
                    'orderStatus' => $order->status,
                    'securityType' => $order->security_type,
                    'symbol' => $order->symbol,
                    'side' => $order->side,
                    'orderQuantity' => $order->quantity,
                    'orderType' => $order->order_type,
                    'limitPrice' => (float) $order->limit_price,
                    'legCount' => count($legs),
                    'legs' => $legs
                ];
            }

            return null;
        }

        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'TZ-API-KEY-ID' => $apiKey,
                'TZ-API-SECRET-KEY' => $apiSecret,
            ])->get("{$this->baseUrl}/v1/api/accounts/{$accountId}/orders/{$clientOrderId}");

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('TradeZero fetch order details failed', [
                'clientOrderId' => $clientOrderId,
                'status' => $response->status(),
                'body' => $response->body()
            ]);
        } catch (\Exception $e) {
            Log::error('TradeZero fetch order details API Exception: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Fetch account P&L.
     */
    public function fetchAccountPnl(string $accountId, string $apiKey, string $apiSecret): ?array
    {
        $useSandbox = $this->sandboxMode && (
            str_contains(strtolower($apiKey), 'demo') ||
            str_contains(strtolower($apiKey), 'mock') ||
            str_contains(strtolower($apiKey), 'test')
        );

        if ($useSandbox) {
            $user = auth()->user() ?: \App\Models\User::where('tradezero_key_id', $apiKey)->first();
            $positionsRes = $user ? $this->getPositionsMock($accountId, $user) : ['positions' => []];
            $positions = $positionsRes['positions'] ?? [];
            $dynamicUnrealized = 0.00;
            foreach ($positions as $pos) {
                $dynamicUnrealized += $pos['unrealized'];
            }

            $dbAcc = \App\Models\TradeZeroAccount::where('account', $accountId)->first();
            $realized = $dbAcc ? (float) $dbAcc->realized : 450.50;
            $equity = $dbAcc ? (float) $dbAcc->equity : 118432.18;
            $cash = $dbAcc ? (float) $dbAcc->available_cash : 112000.00;

            return [
                "accountValue" => $equity + $dynamicUnrealized + $realized,
                "availableCash" => $cash,
                "dayPnl" => $realized + $dynamicUnrealized,
                "dayRealized" => $realized,
                "dayUnrealized" => $dynamicUnrealized,
                "exposure" => 12780.00,
                "usedLeverage" => $dbAcc ? (float) $dbAcc->used_leverage : 2.10,
                "allowedLeverage" => $dbAcc ? (float) $dbAcc->leverage : 4.00
            ];
        }

        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'TZ-API-KEY-ID' => $apiKey,
                'TZ-API-SECRET-KEY' => $apiSecret,
            ])->get("{$this->baseUrl}/v1/api/accounts/{$accountId}/pnl");

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('TradeZero fetch account pnl failed', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
        } catch (\Exception $e) {
            Log::error('TradeZero fetch account pnl API Exception: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Fetch open positions.
     */
    public function fetchAccountPositions(string $accountId, string $apiKey, string $apiSecret): ?array
    {
        $useSandbox = $this->sandboxMode && (
            str_contains(strtolower($apiKey), 'demo') ||
            str_contains(strtolower($apiKey), 'mock') ||
            str_contains(strtolower($apiKey), 'test')
        );

        if ($useSandbox) {
            $user = auth()->user() ?: \App\Models\User::where('tradezero_key_id', $apiKey)->first();
            if ($user) {
                return $this->getPositionsMock($accountId, $user);
            }
            return ['positions' => []];
        }

        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'TZ-API-KEY-ID' => $apiKey,
                'TZ-API-SECRET-KEY' => $apiSecret,
            ])->get("{$this->baseUrl}/v1/api/accounts/{$accountId}/positions");

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('TradeZero fetch positions failed', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
        } catch (\Exception $e) {
            Log::error('TradeZero fetch positions API Exception: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Fetch dynamic open positions mock for sandbox.
     */
    public function getPositionsMock(string $accountId, $user): array
    {
        $orders = \App\Models\Order::where('user_id', $user->id)
            ->where('status', 'Filled')
            ->get();
            
        $positionsMap = [];
        
        foreach ($orders as $order) {
            if ($order->security_type === 'Mleg' && is_array($order->legs)) {
                foreach ($order->legs as $leg) {
                    $sym = $leg['symbol'];
                    $side = $leg['side'];
                    $ratio = (int) ($leg['ratio'] ?? 1);
                    $qty = $order->quantity * $ratio * 100;
                    
                    if (!isset($positionsMap[$sym])) {
                        $positionsMap[$sym] = [
                            'symbol' => $sym,
                            'quantity' => 0,
                            'avgPrice' => $order->price_avg,
                            'securityType' => 'Option'
                        ];
                    }
                    
                    if ($side === 'Buy') {
                        $positionsMap[$sym]['quantity'] += $qty;
                    } else {
                        $positionsMap[$sym]['quantity'] -= $qty;
                    }
                }
            } else {
                $sym = $order->symbol;
                $side = $order->side;
                $qty = $order->quantity;
                
                if (!isset($positionsMap[$sym])) {
                    $positionsMap[$sym] = [
                        'symbol' => $sym,
                        'quantity' => 0,
                        'avgPrice' => $order->price_avg,
                        'securityType' => 'Stock'
                    ];
                }
                
                if (in_array(strtolower($side), ['buy', 'cover'])) {
                    $positionsMap[$sym]['quantity'] += $qty;
                } else {
                    $positionsMap[$sym]['quantity'] -= $qty;
                }
            }
        }
        
        $positions = [];
        foreach ($positionsMap as $sym => $pos) {
            if ($pos['quantity'] !== 0) {
                $qty = abs($pos['quantity']);
                $side = $pos['quantity'] > 0 ? 'Long' : 'Short';
                $currentPrice = (float) $pos['avgPrice'];
                $unrealPnl = ($currentPrice - $pos['avgPrice']) * ($pos['quantity'] > 0 ? 1 : -1) * $qty;
                
                $positions[] = [
                    'symbol' => $sym,
                    'quantity' => $qty,
                    'avgPrice' => (float)$pos['avgPrice'],
                    'close' => (float)$currentPrice,
                    'marketValue' => (float)($currentPrice * $qty),
                    'unrealized' => (float)$unrealPnl,
                    'unrealizedPct' => $pos['avgPrice'] > 0 ? (float)(($unrealPnl / ($pos['avgPrice'] * $qty)) * 100) : 0,
                    'side' => $side,
                    'securityType' => $pos['securityType']
                ];
            }
        }
        
        if (empty($positions)) {
            $positions = [
                [
                    'symbol' => 'COPPERMED',
                    'quantity' => 100,
                    'avgPrice' => 1320.00,
                    'close' => 1326.15,
                    'marketValue' => 132615.00,
                    'unrealized' => 615.00,
                    'unrealizedPct' => 0.46,
                    'side' => 'Long',
                    'securityType' => 'Stock'
                ]
            ];
        }
        
        return ['positions' => $positions];
    }

    /**
     * Fetch today's orders.
     */
    public function fetchAccountOrders(string $accountId, string $apiKey, string $apiSecret): ?array
    {
        $useSandbox = $this->sandboxMode && (
            str_contains(strtolower($apiKey), 'demo') ||
            str_contains(strtolower($apiKey), 'mock') ||
            str_contains(strtolower($apiKey), 'test')
        );

        if ($useSandbox) {
            $user = auth()->user() ?: \App\Models\User::where('tradezero_key_id', $apiKey)->first();
            if ($user) {
                $orders = \App\Models\Order::where('user_id', $user->id)
                    ->latest()
                    ->get();
                $mappedOrders = $orders->map(function ($order) {
                    return [
                        'clientOrderId' => $order->client_order_id,
                        'symbol' => $order->symbol,
                        'side' => $order->side ?? 'Buy',
                        'orderQuantity' => $order->quantity,
                        'orderType' => $order->order_type,
                        'limitPrice' => $order->limit_price ? (float) $order->limit_price : null,
                        'orderStatus' => $order->status,
                        'priceAvg' => (float) $order->price_avg,
                        'created' => $order->created_at->toIso8601String(),
                        'securityType' => $order->security_type ?? 'Stock',
                        'legs' => $order->legs
                    ];
                })->toArray();
                return ['orders' => $mappedOrders];
            }
            return ['orders' => []];
        }

        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'TZ-API-KEY-ID' => $apiKey,
                'TZ-API-SECRET-KEY' => $apiSecret,
            ])->get("{$this->baseUrl}/v1/api/accounts/{$accountId}/orders");

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('TradeZero fetch orders failed', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
        } catch (\Exception $e) {
            Log::error('TradeZero fetch orders API Exception: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Fetch historical orders with pagination starting from a specific date.
     */
    public function fetchAccountOrdersWithPagination(string $accountId, string $startDate, ?int $page, string $apiKey, string $apiSecret): ?array
    {
        $useSandbox = $this->sandboxMode && (
            str_contains(strtolower($apiKey), 'demo') ||
            str_contains(strtolower($apiKey), 'mock') ||
            str_contains(strtolower($apiKey), 'test')
        );

        if ($useSandbox) {
            $user = auth()->user() ?: \App\Models\User::where('tradezero_key_id', $apiKey)->first();
            if ($user) {
                $query = \App\Models\Order::where('user_id', $user->id)
                    ->where('created_at', '>=', $startDate)
                    ->latest();

                $perPage = 10;
                $currentPage = $page ?? 1;
                $total = $query->count();
                $orders = $query->skip(($currentPage - 1) * $perPage)->take($perPage)->get();

                $mappedOrders = $orders->map(function ($order) {
                    return [
                        'clientOrderId' => $order->client_order_id,
                        'symbol' => $order->symbol,
                        'side' => $order->side ?? 'Buy',
                        'orderQuantity' => $order->quantity,
                        'orderType' => $order->order_type,
                        'limitPrice' => $order->limit_price ? (float) $order->limit_price : null,
                        'orderStatus' => $order->status,
                        'priceAvg' => (float) $order->price_avg,
                        'created' => $order->created_at->toIso8601String(),
                        'securityType' => $order->security_type ?? 'Stock',
                        'legs' => $order->legs
                    ];
                })->toArray();

                return [
                    'orders' => $mappedOrders,
                    'pagination' => [
                        'currentPage' => $currentPage,
                        'perPage' => $perPage,
                        'total' => $total,
                        'totalPages' => (int) ceil($total / $perPage)
                    ]
                ];
            }
            return ['orders' => [], 'pagination' => ['currentPage' => 1, 'perPage' => 10, 'total' => 0, 'totalPages' => 1]];
        }

        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'TZ-API-KEY-ID' => $apiKey,
                'TZ-API-SECRET-KEY' => $apiSecret,
            ])->get("{$this->baseUrl}/v1/api/accounts/{$accountId}/orders-with-pagination/start-date/{$startDate}", [
                'page' => $page ?? 1
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('TradeZero fetch paginated orders failed', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
        } catch (\Exception $e) {
            Log::error('TradeZero fetch paginated orders API Exception: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Fetch historical execution details (fills) starting from a specific date for a 1-week window.
     */
    public function fetchAccountFills(string $accountId, string $startDate, string $apiKey, string $apiSecret): ?array
    {
        $useSandbox = $this->sandboxMode && (
            str_contains(strtolower($apiKey), 'demo') ||
            str_contains(strtolower($apiKey), 'mock') ||
            str_contains(strtolower($apiKey), 'test')
        );

        if ($useSandbox) {
            $user = auth()->user() ?: \App\Models\User::where('tradezero_key_id', $apiKey)->first();
            if ($user) {
                $endLimit = \Carbon\Carbon::parse($startDate)->addWeek()->endOfDay();
                $orders = \App\Models\Order::where('user_id', $user->id)
                    ->where('created_at', '>=', $startDate)
                    ->where('created_at', '<=', $endLimit)
                    ->where('status', 'Filled')
                    ->latest()
                    ->get();

                $fills = $orders->map(function ($order) {
                    return [
                        'clientOrderId' => $order->client_order_id,
                        'symbol' => $order->symbol,
                        'side' => $order->side ?? 'Buy',
                        'qty' => $order->quantity,
                        'price' => (float) $order->price_avg,
                        'tradeDate' => $order->created_at->toIso8601String(),
                        'tradeId' => 'tr-' . substr($order->client_order_id, 4) . '-01',
                    ];
                })->toArray();

                return ['fills' => $fills];
            }
            return ['fills' => []];
        }

        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'TZ-API-KEY-ID' => $apiKey,
                'TZ-API-SECRET-KEY' => $apiSecret,
            ])->get("{$this->baseUrl}/v1/api/accounts/{$accountId}/orders/start-date/{$startDate}");

            if ($response->successful()) {
                $data = $response->json();
                if (is_array($data) && !isset($data['fills'])) {
                    if (array_is_list($data)) {
                        return ['fills' => $data];
                    }
                    return $data;
                }
                return $data;
            }

            Log::error('TradeZero fetch account fills failed', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
        } catch (\Exception $e) {
            Log::error('TradeZero fetch account fills API Exception: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Cancel an active order.
     */
    public function cancelOrder(string $accountId, string $clientOrderId, string $apiKey, string $apiSecret): ?array
    {
        $useSandbox = $this->sandboxMode && (
            str_contains(strtolower($apiKey), 'demo') ||
            str_contains(strtolower($apiKey), 'mock') ||
            str_contains(strtolower($apiKey), 'test')
        );

        if ($useSandbox) {
            $userId = auth()->id();
            $order = \App\Models\Order::where('user_id', $userId)
                ->where('client_order_id', $clientOrderId)
                ->first();

            if ($order) {
                $order->update(['status' => 'Cancelled']);
                return ['success' => true, 'clientOrderId' => $clientOrderId, 'orderStatus' => 'Cancelled'];
            }

            return null;
        }

        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'TZ-API-KEY-ID' => $apiKey,
                'TZ-API-SECRET-KEY' => $apiSecret,
            ])->delete("{$this->baseUrl}/v1/api/accounts/{$accountId}/orders/{$clientOrderId}");

            if ($response->successful()) {
                // Update local order status as well
                $userId = auth()->id();
                \App\Models\Order::where('user_id', $userId)
                    ->where('client_order_id', $clientOrderId)
                    ->update(['status' => 'Cancelled']);

                return $response->json();
            }

            Log::error('TradeZero cancel order request failed', [
                'accountId' => $accountId,
                'clientOrderId' => $clientOrderId,
                'status' => $response->status(),
                'body' => $response->body()
            ]);
        } catch (\Exception $e) {
            Log::error('TradeZero Cancel Order API Exception: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Fetch allowed routes for an account.
     */
    public function fetchAccountRoutes(string $accountId, string $apiKey, string $apiSecret): ?array
    {
        $useSandbox = $this->sandboxMode && (
            str_contains(strtolower($apiKey), 'demo') ||
            str_contains(strtolower($apiKey), 'mock') ||
            str_contains(strtolower($apiKey), 'test')
        );

        if ($useSandbox) {
            return [
                'routes' => [
                    [
                        'routeName' => 'PAPER',
                        'securityTypes' => ['Stock', 'Option'],
                        'orderTypes' => ['Market', 'Limit'],
                        'timesInForce' => ['Day', 'GTC']
                    ],
                    [
                        'routeName' => 'PAPERM',
                        'securityTypes' => ['Option'],
                        'orderTypes' => ['Limit'],
                        'timesInForce' => ['Day']
                    ]
                ]
            ];
        }

        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'TZ-API-KEY-ID' => $apiKey,
                'TZ-API-SECRET-KEY' => $apiSecret,
            ])->get("{$this->baseUrl}/v1/api/accounts/{$accountId}/routes");

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('TradeZero fetch account routes failed', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
        } catch (\Exception $e) {
            Log::error('TradeZero fetch account routes API Exception: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Fetch parallel account, pnl, positions, and orders for the dashboard snapshot concurrently.
     */
    public function fetchDashboardDataParallel(string $accountId, string $apiKey, string $apiSecret): ?array
    {
        $useSandbox = $this->sandboxMode && (
            str_contains(strtolower($apiKey), 'demo') ||
            str_contains(strtolower($apiKey), 'mock') ||
            str_contains(strtolower($apiKey), 'test')
        );

        if ($useSandbox) {
            $account = $this->fetchAccountDetails($accountId, $apiKey, $apiSecret);
            $pnl = $this->fetchAccountPnl($accountId, $apiKey, $apiSecret);
            $posRes = $this->fetchAccountPositions($accountId, $apiKey, $apiSecret);
            $ordersRes = $this->fetchAccountOrders($accountId, $apiKey, $apiSecret);

            return [
                'account' => $account,
                'pnl' => $pnl,
                'positions' => $posRes['positions'] ?? [],
                'orders' => $ordersRes['orders'] ?? []
            ];
        }

        try {
            $headers = [
                'Accept' => 'application/json',
                'TZ-API-KEY-ID' => $apiKey,
                'TZ-API-SECRET-KEY' => $apiSecret,
            ];

            // Concurrent parallel HTTP calls
            $responses = \Illuminate\Support\Facades\Http::pool(fn (\Illuminate\Http\Client\Pool $pool) => [
                $pool->as('account')->withHeaders($headers)->get("{$this->baseUrl}/v1/api/account/{$accountId}"),
                $pool->as('pnl')->withHeaders($headers)->get("{$this->baseUrl}/v1/api/accounts/{$accountId}/pnl"),
                $pool->as('positions')->withHeaders($headers)->get("{$this->baseUrl}/v1/api/accounts/{$accountId}/positions"),
                $pool->as('orders')->withHeaders($headers)->get("{$this->baseUrl}/v1/api/accounts/{$accountId}/orders"),
            ]);

            if (!$responses['account']->successful() || !$responses['pnl']->successful() || 
                !$responses['positions']->successful() || !$responses['orders']->successful()) {
                Log::error('TradeZero concurrent pool request failed', [
                    'account_status' => $responses['account']->status(),
                    'pnl_status' => $responses['pnl']->status(),
                    'positions_status' => $responses['positions']->status(),
                    'orders_status' => $responses['orders']->status(),
                ]);
                return null;
            }

            return [
                'account' => $responses['account']->json(),
                'pnl' => $responses['pnl']->json(),
                'positions' => $responses['positions']->json()['positions'] ?? [],
                'orders' => $responses['orders']->json()['orders'] ?? []
            ];
        } catch (\Exception $e) {
            Log::error('TradeZero concurrent pool Exception: ' . $e->getMessage());
            return null;
        }
    }
}


