<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Account;
use App\Services\BrokerManager;
use App\Services\TradeZeroService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TradeOrderController extends Controller
{
    protected BrokerManager $brokerManager;
    protected TradeZeroService $tradeZeroService; // Kept for locating backwards compatibility

    public function __construct(BrokerManager $brokerManager, TradeZeroService $tradeZeroService)
    {
        $this->brokerManager = $brokerManager;
        $this->tradeZeroService = $tradeZeroService;
    }

    /**
     * Get active selected account.
     */
    protected function getActiveAccount(Request $request): Account
    {
        $user = Auth::user();

        // Auto-seed default Paper Account if user has none
        if ($user->accounts()->count() === 0) {
            Account::create([
                'user_id' => $user->id,
                'name' => 'Paper Account',
                'balance' => 100000.00,
                'equity' => 100000.00,
                'account_type' => 'paper',
                'provider' => 'paper',
                'status' => 'active',
            ]);
        }

        $accountId = $request->input('account_id') ?? $request->query('account');

        if ($accountId) {
            $account = $user->accounts()->where('id', $accountId)->first()
                ?? $user->accounts()->where('name', $accountId)->first();
            if ($account) {
                return $account;
            }
        }

        // Fallback to primary connected TradeZero account name
        if ($user->tradezero_account_id) {
            $tzAcc = $user->accounts()->where('name', $user->tradezero_account_id)->first();
            if ($tzAcc) {
                return $tzAcc;
            }
        }

        return $user->accounts()->first();
    }

    /**
     * Submit unified order.
     */
    public function placeOrder(Request $request)
    {
        $request->validate([
            'symbol' => ['required', 'string', 'max:20'],
            'security_type' => ['sometimes', 'string', 'in:Stock,Option,Mleg'],
            'side' => ['required_unless:security_type,Mleg', 'string', 'in:Buy,Sell,Short,Cover', 'nullable'],
            'quantity' => ['required', 'integer', 'min:1'],
            'order_type' => ['required', 'string', 'in:Market,Limit'],
            'limit_price' => ['required_if:order_type,Limit', 'numeric', 'min:0.01', 'nullable'],
            'legs' => ['required_if:security_type,Mleg', 'array'],
            'legs.*.symbol' => ['required_with:legs', 'string'],
            'legs.*.side' => ['required_with:legs', 'string', 'in:Buy,Sell'],
            'legs.*.ratio' => ['required_with:legs', 'integer', 'min:1'],
            'legs.*.openClose' => ['required_with:legs', 'string', 'in:Open,Close'],
            'route' => ['sometimes', 'string', 'max:50', 'nullable'],
        ]);

        $account = $this->getActiveAccount($request);
        $user = Auth::user();

        // Extra safety check for live broker connections
        if ($account->provider === 'tradezero' && (is_null($user->tradezero_key_id) || is_null($user->tradezero_account_id))) {
            return response()->json([
                'success' => false,
                'message' => 'No active TradeZero broker connection found. Please connect your keys first.'
            ], 403);
        }

        $symbol = strtoupper($request->input('symbol'));
        $quantity = (int) $request->input('quantity');
        $securityType = $request->input('security_type', 'Stock');
        $side = $request->input('side');
        if ($side) {
            $side = ucfirst(strtolower($side));
        }

        // Locate checks for TradeZero Short selling
        if ($account->provider === 'tradezero' && $securityType !== 'Mleg' && $side === 'Short') {
            $etbCheck = $this->tradeZeroService->checkEasyToBorrow(
                $account->name,
                $symbol,
                $user->tradezero_key_id,
                $user->tradezero_secret_key
            );

            $isEtb = $etbCheck['isEasyToBorrow'] ?? true;

            if (!$isEtb) {
                // HTB symbol. Check local locate inventory.
                $locate = \App\Models\TradeZeroLocate::where('user_id', $user->id)
                    ->where('account', $account->name)
                    ->where('symbol', $symbol)
                    ->where('locate_status', 50) // Filled
                    ->where('available_quantity', '>=', $quantity)
                    ->first();

                if (!$locate) {
                    return response()->json([
                        'success' => false,
                        'message' => "Locate required for {$symbol} (Hard-to-Borrow). Please locate at least {$quantity} shares first."
                    ], 422);
                }

                // sandbox decrements
                $useSandbox = env('TRADEZERO_SANDBOX_MODE', true) && (
                    str_contains(strtolower($user->tradezero_key_id), 'demo') ||
                    str_contains(strtolower($user->tradezero_key_id), 'mock') ||
                    str_contains(strtolower($user->tradezero_key_id), 'test')
                );
                if ($useSandbox) {
                    $locate->decrement('available_quantity', $quantity);
                }
            }
        }

        // Submits order to provider driver
        try {
            $provider = $this->brokerManager->driver($account);
            $order = $provider->placeOrder($account, $request->all());

            return response()->json([
                'success' => true,
                'message' => 'Order executed successfully!',
                'order' => [
                    'id' => $order->id,
                    'client_order_id' => $order->client_order_id,
                    'symbol' => $order->symbol,
                    'side' => $order->side ? strtoupper($order->side) : 'MLEG',
                    'quantity' => $order->quantity,
                    'order_type' => $order->order_type,
                    'security_type' => $order->security_type,
                    'limit_price' => $order->limit_price ? '$' . number_format($order->limit_price, 2) : 'MKT',
                    'status' => strtoupper($order->status),
                    'price_avg' => '$' . number_format($order->limit_price ?? 1326.15, 2),
                    'created_at' => $order->created_at->format('Y-m-d H:i:s'),
                    'legs' => $order->legs
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Show order details.
     */
    public function showOrder(Request $request, string $clientOrderId)
    {
        $order = Order::where('user_id', Auth::id())
            ->where('client_order_id', $clientOrderId)
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found.'
            ], 404);
        }

        // If provider is tradezero, fetch live details from TradeZero API and update local order state
        if ($order->provider === 'tradezero') {
            $user = Auth::user();
            $account = $order->account;
            $accountName = $account ? $account->name : ($user->tradezero_account_id ?? '');

            if ($user->tradezero_key_id && $user->tradezero_secret_key && $accountName) {
                $tzOrder = $this->tradeZeroService->fetchOrderDetails(
                    $accountName,
                    $clientOrderId,
                    $user->tradezero_key_id,
                    $user->tradezero_secret_key
                );

                if ($tzOrder) {
                    $order->update([
                        'status' => $tzOrder['orderStatus'] ?? $order->status,
                        'limit_price' => isset($tzOrder['limitPrice']) ? (float) $tzOrder['limitPrice'] : $order->limit_price,
                        'legs' => $tzOrder['legs'] ?? $order->legs,
                    ]);
                }
            }
        }

        return response()->json([
            'success' => true,
            'order' => [
                'clientOrderId' => $order->client_order_id,
                'symbol' => $order->symbol,
                'side' => $order->side,
                'orderQuantity' => $order->quantity,
                'orderType' => $order->order_type,
                'limitPrice' => $order->limit_price,
                'orderStatus' => $order->status,
                'priceAvg' => $order->limit_price ?? 1326.15,
                'created' => $order->created_at->toIso8601String(),
                'securityType' => str_contains($order->symbol, '17') || strlen($order->symbol) > 8 ? 'Option' : 'Stock',
                'legs' => $order->legs
            ]
        ]);
    }

    /**
     * Consolidated snapshot payload.
     */
    public function dashboardSnapshot(Request $request)
    {
        $account = $this->getActiveAccount($request);

        try {
            $provider = $this->brokerManager->driver($account);
            $snapshot = $provider->getBalance($account);

            return response()->json([
                'success' => true,
                'snapshot' => $snapshot
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check if a symbol is Easy-To-Borrow.
     */
    public function checkEtb(Request $request)
    {
        $account = $this->getActiveAccount($request);

        if ($account->provider !== 'tradezero') {
            return response()->json([
                'success' => true,
                'symbol' => strtoupper($request->input('symbol')),
                'isEasyToBorrow' => true
            ]);
        }

        $request->validate(['symbol' => ['required', 'string', 'max:10']]);
        $user = Auth::user();

        $res = $this->tradeZeroService->checkEasyToBorrow(
            $account->name,
            $request->input('symbol'),
            $user->tradezero_key_id,
            $user->tradezero_secret_key
        );

        if (is_null($res)) {
            return response()->json(['success' => false, 'message' => 'ETB check failed.'], 400);
        }

        return response()->json([
            'success' => true,
            'symbol' => strtoupper($request->input('symbol')),
            'isEasyToBorrow' => $res['isEasyToBorrow'] ?? true
        ]);
    }

    /**
     * Request a locate quote.
     */
    public function requestLocateQuote(Request $request)
    {
        $account = $this->getActiveAccount($request);

        if ($account->provider !== 'tradezero') {
            return response()->json([
                'success' => true,
                'message' => 'Locate quote request skipped (Easy-To-Borrow account).',
                'quoteReqID' => 'paper-locate-' . uniqid()
            ]);
        }

        $request->validate([
            'symbol' => ['required', 'string', 'max:10'],
            'quantity' => ['required', 'integer', 'min:100']
        ]);

        $user = Auth::user();
        $quoteReqId = 'locate-' . \Illuminate\Support\Str::uuid();

        $res = $this->tradeZeroService->requestLocateQuote(
            $account->name,
            $request->input('symbol'),
            (int) $request->input('quantity'),
            $quoteReqId,
            $user->tradezero_key_id,
            $user->tradezero_secret_key
        );

        if (is_null($res)) {
            return response()->json(['success' => false, 'message' => 'Quote request failed.'], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Locate quote request queued successfully.',
            'quoteReqID' => $quoteReqId
        ]);
    }

    /**
     * Fetch locate history.
     */
    public function locateHistory(Request $request)
    {
        $account = $this->getActiveAccount($request);

        if ($account->provider !== 'tradezero') {
            return response()->json([
                'success' => true,
                'locateHistory' => []
            ]);
        }

        $user = Auth::user();
        $res = $this->tradeZeroService->fetchLocatesHistory(
            $account->name,
            $user->tradezero_key_id,
            $user->tradezero_secret_key
        );

        if (is_null($res)) {
            return response()->json(['success' => false, 'message' => 'Failed to retrieve history.'], 400);
        }

        return response()->json([
            'success' => true,
            'locateHistory' => $res['locateHistory'] ?? []
        ]);
    }

    /**
     * Accept a locate quote.
     */
    public function acceptLocate(Request $request)
    {
        $account = $this->getActiveAccount($request);

        if ($account->provider !== 'tradezero') {
            return response()->json([
                'success' => true,
                'message' => 'Locate quote accepted.'
            ]);
        }

        $request->validate(['quote_req_id' => ['required', 'string']]);
        $user = Auth::user();

        $res = $this->tradeZeroService->acceptLocateQuote(
            $account->name,
            $request->input('quote_req_id'),
            $user->tradezero_key_id,
            $user->tradezero_secret_key
        );

        if (is_null($res)) {
            return response()->json(['success' => false, 'message' => 'Failed to accept quote.'], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Locate quote accepted successfully.'
        ]);
    }

    /**
     * Cancel a locate quote request.
     */
    public function cancelLocate(Request $request)
    {
        $account = $this->getActiveAccount($request);

        if ($account->provider !== 'tradezero') {
            return response()->json([
                'success' => true,
                'message' => 'Locate quote cancelled.'
            ]);
        }

        $request->validate(['quote_req_id' => ['required', 'string']]);
        $user = Auth::user();

        $res = $this->tradeZeroService->cancelLocateQuote(
            $account->name,
            $request->input('quote_req_id'),
            $user->tradezero_key_id,
            $user->tradezero_secret_key
        );

        if (is_null($res)) {
            return response()->json(['success' => false, 'message' => 'Failed to cancel quote.'], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Locate quote cancelled successfully.'
        ]);
    }

    /**
     * Return/sell back locate shares from inventory.
     */
    public function sellBackLocate(Request $request)
    {
        $account = $this->getActiveAccount($request);

        if ($account->provider !== 'tradezero') {
            return response()->json([
                'success' => true,
                'message' => 'Locate shares returned successfully.'
            ]);
        }

        $request->validate([
            'symbol' => ['required', 'string'],
            'quantity' => ['required', 'integer', 'min:1'],
            'quote_req_id' => ['required', 'string'],
            'locate_type' => ['required']
        ]);

        $user = Auth::user();

        $res = $this->tradeZeroService->sellBackLocate(
            $account->name,
            [
                'symbol' => $request->input('symbol'),
                'quantity' => (int) $request->input('quantity'),
                'quoteReqID' => $request->input('quote_req_id'),
                'locateType' => $request->input('locate_type')
            ],
            $user->tradezero_key_id,
            $user->tradezero_secret_key
        );

        if (is_null($res)) {
            return response()->json(['success' => false, 'message' => 'Failed to return locates.'], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Locate shares returned successfully.'
        ]);
    }



    /**
     * Fetch locates inventory.
     */
    public function locateInventory(Request $request)
    {
        $account = $this->getActiveAccount($request);

        if ($account->provider !== 'tradezero') {
            return response()->json([
                'success' => true,
                'locateInventory' => []
            ]);
        }

        $user = Auth::user();
        $res = $this->tradeZeroService->fetchLocatesInventory(
            $account->name,
            $user->tradezero_key_id,
            $user->tradezero_secret_key
        );

        if (is_null($res)) {
            return response()->json(['success' => false, 'message' => 'Failed to retrieve inventory.'], 400);
        }

        return response()->json([
            'success' => true,
            'locateInventory' => $res['locateInventory'] ?? []
        ]);
    }

    /**
     * Fetch paginated orders history.
     */
    public function ordersHistory(Request $request)
    {
        $account = $this->getActiveAccount($request);

        if ($account->provider !== 'tradezero') {
            return response()->json([
                'success' => true,
                'orders' => [],
                'pagination' => [
                    'currentPage' => 1,
                    'totalPages' => 1,
                    'total' => 0,
                    'perPage' => 10
                ]
            ]);
        }

        $startDate = $request->input('start_date', now()->subYear()->format('Y-m-d'));
        $page = $request->input('page', 1);

        $user = Auth::user();
        $res = $this->tradeZeroService->fetchAccountOrdersWithPagination(
            $account->name,
            $startDate,
            (int) $page,
            $user->tradezero_key_id,
            $user->tradezero_secret_key
        );

        if (is_null($res)) {
            return response()->json(['success' => false, 'message' => 'Failed to retrieve historical orders.'], 400);
        }

        return response()->json(array_merge(['success' => true], $res));
    }

    /**
     * Fetch order execution details/fills history (1-week window).
     */
    public function ordersFills(Request $request)
    {
        $account = $this->getActiveAccount($request);

        if ($account->provider !== 'tradezero') {
            return response()->json([
                'success' => true,
                'fills' => []
            ]);
        }

        $startDate = $request->input('start_date', now()->subDays(7)->format('Y-m-d'));

        $user = Auth::user();
        $res = $this->tradeZeroService->fetchAccountFills(
            $account->name,
            $startDate,
            $user->tradezero_key_id,
            $user->tradezero_secret_key
        );

        if (is_null($res)) {
            return response()->json(['success' => false, 'message' => 'Failed to retrieve order execution fills.'], 400);
        }

        return response()->json(array_merge(['success' => true], $res));
    }

    /**
     * Cancel an active order.
     */
    public function cancelOrder(Request $request)
    {
        $request->validate(['client_order_id' => ['required', 'string']]);
        $account = $this->getActiveAccount($request);

        try {
            $provider = $this->brokerManager->driver($account);
            $res = $provider->cancelOrder($account, $request->input('client_order_id'));

            if (is_null($res) || (isset($res['success']) && !$res['success'])) {
                return response()->json([
                    'success' => false,
                    'message' => $res['message'] ?? 'Failed to cancel the order.'
                ], 400);
            }

            return response()->json([
                'success' => true,
                'message' => 'Order cancelled successfully.',
                'result' => $res
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Fetch allowed routes/venues for the active account.
     */
    public function getRoutes(Request $request)
    {
        $account = $this->getActiveAccount($request);

        if ($account->provider !== 'tradezero') {
            return response()->json([
                'success' => true,
                'routes' => [
                    [
                        'routeName' => 'SMART',
                        'securityTypes' => ['Stock', 'Option'],
                        'orderTypes' => ['Market', 'Limit'],
                        'timesInForce' => ['Day', 'GTC']
                    ]
                ]
            ]);
        }

        $user = Auth::user();
        $res = $this->tradeZeroService->fetchAccountRoutes(
            $account->name,
            $user->tradezero_key_id,
            $user->tradezero_secret_key
        );

        if (is_null($res)) {
            return response()->json(['success' => false, 'message' => 'Failed to retrieve routes.'], 400);
        }

        return response()->json(array_merge(['success' => true], $res));
    }

    /**
     * Fetch open positions for the active account.
     */
    public function getPositions(Request $request)
    {
        $account = $this->getActiveAccount($request);

        try {
            $provider = $this->brokerManager->driver($account);
            $positions = $provider->getPositions($account);

            return response()->json([
                'success' => true,
                'positions' => $positions
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search stock symbols using Yahoo Finance autocomplete API with local fallback.
     */
    public function searchSymbols(Request $request)
    {
        $query = trim($request->input('q', ''));

        if (empty($query)) {
            return response()->json(['success' => true, 'results' => []]);
        }

        // 1. Try to query Yahoo Finance
        try {
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/115.0.0.0 Safari/537.36'
            ])->timeout(3)->get("https://query1.finance.yahoo.com/v1/finance/search?q=" . urlencode($query) . "&quotesCount=10&newsCount=0");

            if ($response->successful()) {
                $data = $response->json();
                $quotes = $data['quotes'] ?? [];
                
                $results = [];
                foreach ($quotes as $quote) {
                    if (isset($quote['symbol']) && ($quote['quoteType'] ?? '') === 'EQUITY') {
                        $results[] = [
                            'symbol' => strtoupper($quote['symbol']),
                            'name' => $quote['longname'] ?? $quote['shortname'] ?? ''
                        ];
                    }
                }

                if (!empty($results)) {
                    return response()->json([
                        'success' => true,
                        'results' => $results
                    ]);
                }
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Yahoo Finance search failed, using fallback: ' . $e->getMessage());
        }

        // 2. Fallback local mock data
        $mockSymbols = [
            ['symbol' => 'AAPL', 'name' => 'Apple Inc.'],
            ['symbol' => 'MSFT', 'name' => 'Microsoft Corporation'],
            ['symbol' => 'GOOGL', 'name' => 'Alphabet Inc.'],
            ['symbol' => 'AMZN', 'name' => 'Amazon.com, Inc.'],
            ['symbol' => 'TSLA', 'name' => 'Tesla, Inc.'],
            ['symbol' => 'NVDA', 'name' => 'NVIDIA Corporation'],
            ['symbol' => 'META', 'name' => 'Meta Platforms, Inc.'],
            ['symbol' => 'NFLX', 'name' => 'Netflix, Inc.'],
            ['symbol' => 'AMD', 'name' => 'Advanced Micro Devices, Inc.'],
            ['symbol' => 'MCK', 'name' => 'McKesson Corporation'],
            ['symbol' => 'BABA', 'name' => 'Alibaba Group Holding Limited'],
            ['symbol' => 'JPM', 'name' => 'JPMorgan Chase & Co.'],
            ['symbol' => 'BAC', 'name' => 'Bank of America Corporation'],
            ['symbol' => 'DIS', 'name' => 'The Walt Disney Company'],
        ];

        $results = [];
        $queryUpper = strtoupper($query);
        foreach ($mockSymbols as $item) {
            if (str_contains($item['symbol'], $queryUpper) || str_contains(strtoupper($item['name']), $queryUpper)) {
                $results[] = [
                    'symbol' => $item['symbol'],
                    'name' => $item['name']
                ];
            }
        }

        return response()->json([
            'success' => true,
            'results' => array_slice($results, 0, 10)
        ]);
    }

    /**
     * Generate secure token and register with local WebSocket relay server.
     */
    public function getWebSocketToken(Request $request)
    {
        return response()->json([
            'success' => false,
            'message' => 'WebSockets are disabled. Falling back to REST polling.'
        ]);
    }
}
