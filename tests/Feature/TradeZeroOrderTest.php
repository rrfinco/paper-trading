<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Account;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TradeZeroOrderTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test placing a market order successfully under mock sandbox keys.
     */
    public function test_can_place_market_order_successfully(): void
    {
        $user = User::factory()->create([
            'tradezero_key_id' => 'key_live_demo',
            'tradezero_secret_key' => 'secret_demo',
            'tradezero_account_id' => 'TZ-DEMO-LIVE-1',
        ]);

        $account = Account::create([
            'user_id' => $user->id,
            'name' => 'TZ-DEMO-LIVE-1',
            'balance' => 112000.00,
            'equity' => 118432.18,
            'account_type' => 'live',
            'provider' => 'tradezero',
            'status' => 'active',
        ]);

        $response = $this->actingAs($user)
            ->postJson('/broker/order', [
                'account_id' => $account->id,
                'symbol' => 'AAPL',
                'side' => 'Buy',
                'quantity' => 10,
                'order_type' => 'Market',
            ]);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('order.symbol', 'AAPL');
        $response->assertJsonPath('order.side', 'BUY');
        $response->assertJsonPath('order.quantity', 10);
        $response->assertJsonPath('order.order_type', 'Market');

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'account_id' => $account->id,
            'symbol' => 'AAPL',
            'side' => 'Buy',
            'quantity' => 10,
            'order_type' => 'Market',
            'status' => 'Filled',
        ]);
    }

    /**
     * Test placing a limit order successfully.
     */
    public function test_can_place_limit_order_successfully(): void
    {
        $user = User::factory()->create([
            'tradezero_key_id' => 'key_live_demo',
            'tradezero_secret_key' => 'secret_demo',
            'tradezero_account_id' => 'TZ-DEMO-LIVE-1',
        ]);

        $account = Account::create([
            'user_id' => $user->id,
            'name' => 'TZ-DEMO-LIVE-1',
            'balance' => 112000.00,
            'equity' => 118432.18,
            'account_type' => 'live',
            'provider' => 'tradezero',
            'status' => 'active',
        ]);

        $response = $this->actingAs($user)
            ->postJson('/broker/order', [
                'account_id' => $account->id,
                'symbol' => 'NVDA',
                'side' => 'Sell',
                'quantity' => 5,
                'order_type' => 'Limit',
                'limit_price' => 127.80,
            ]);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('order.symbol', 'NVDA');
        $response->assertJsonPath('order.side', 'SELL');
        $response->assertJsonPath('order.limit_price', '$127.80');

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'account_id' => $account->id,
            'symbol' => 'NVDA',
            'side' => 'Sell',
            'quantity' => 5,
            'order_type' => 'Limit',
            'limit_price' => 127.80,
        ]);
    }

    /**
     * Test validation fails when required parameters are missing.
     */
    public function test_order_placement_validation_fails(): void
    {
        $user = User::factory()->create([
            'tradezero_key_id' => 'key_live_demo',
            'tradezero_secret_key' => 'secret_demo',
            'tradezero_account_id' => 'TZ-DEMO-LIVE-1',
        ]);

        $response = $this->actingAs($user)
            ->postJson('/broker/order', [
                'order_type' => 'Market',
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['symbol', 'quantity', 'side']);
    }

    /**
     * Test limit price is required when order type is Limit.
     */
    public function test_limit_price_is_required_for_limit_orders(): void
    {
        $user = User::factory()->create([
            'tradezero_key_id' => 'key_live_demo',
            'tradezero_secret_key' => 'secret_demo',
            'tradezero_account_id' => 'TZ-DEMO-LIVE-1',
        ]);

        $response = $this->actingAs($user)
            ->postJson('/broker/order', [
                'symbol' => 'AAPL',
                'side' => 'Buy',
                'quantity' => 10,
                'order_type' => 'Limit',
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['limit_price']);
    }

    /**
     * Test order placement fails when broker connection is missing.
     */
    public function test_cannot_place_order_without_broker_connection(): void
    {
        $user = User::factory()->create(); // No keys connected

        // Seeding a tradezero provider account without credentials
        $account = Account::create([
            'user_id' => $user->id,
            'name' => 'TZ-MOCK-1',
            'balance' => 5000.00,
            'equity' => 5000.00,
            'account_type' => 'paper',
            'provider' => 'tradezero',
            'status' => 'active',
        ]);

        $response = $this->actingAs($user)
            ->postJson('/broker/order', [
                'account_id' => $account->id,
                'symbol' => 'AAPL',
                'side' => 'Buy',
                'quantity' => 10,
                'order_type' => 'Market',
            ]);

        $response->assertStatus(403);
        $response->assertJsonPath('success', false);
        $response->assertJsonPath('message', 'No active TradeZero broker connection found. Please connect your keys first.');
    }

    /**
     * Test cancelling an order successfully.
     */
    public function test_can_cancel_order_successfully(): void
    {
        $user = User::factory()->create([
            'tradezero_key_id' => 'key_live_demo',
            'tradezero_secret_key' => 'secret_demo',
            'tradezero_account_id' => 'TZ-DEMO-LIVE-1',
        ]);

        $account = Account::create([
            'user_id' => $user->id,
            'name' => 'TZ-DEMO-LIVE-1',
            'balance' => 112000.00,
            'equity' => 118432.18,
            'account_type' => 'live',
            'provider' => 'tradezero',
            'status' => 'active',
        ]);

        $order = Order::create([
            'account_id' => $account->id,
            'user_id' => $user->id,
            'client_order_id' => 'ord-12345',
            'symbol' => 'AAPL',
            'side' => 'Buy',
            'quantity' => 10,
            'order_type' => 'Limit',
            'limit_price' => 150.00,
            'status' => 'New',
            'provider' => 'tradezero',
        ]);

        $response = $this->actingAs($user)
            ->postJson('/broker/order/cancel', [
                'account_id' => $account->id,
                'client_order_id' => 'ord-12345',
            ]);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'Cancelled',
        ]);
    }

    /**
     * Test fetching order details via the singular endpoint and syncing status.
     */
    public function test_can_fetch_order_details_successfully(): void
    {
        $user = User::factory()->create([
            'tradezero_key_id' => 'key_live_demo',
            'tradezero_secret_key' => 'secret_demo',
            'tradezero_account_id' => 'TZ-DEMO-LIVE-1',
        ]);

        $account = Account::create([
            'user_id' => $user->id,
            'name' => 'TZ-DEMO-LIVE-1',
            'balance' => 112000.00,
            'equity' => 118432.18,
            'account_type' => 'live',
            'provider' => 'tradezero',
            'status' => 'active',
        ]);

        $order = Order::create([
            'account_id' => $account->id,
            'user_id' => $user->id,
            'client_order_id' => 'ord-55555',
            'symbol' => 'AAPL',
            'side' => 'Buy',
            'quantity' => 10,
            'order_type' => 'Limit',
            'limit_price' => 150.00,
            'status' => 'New',
            'provider' => 'tradezero',
        ]);

        $this->partialMock(\App\Services\TradeZeroService::class, function ($mock) {
            $mock->shouldReceive('fetchOrderDetails')
                ->once()
                ->with('TZ-DEMO-LIVE-1', 'ord-55555', 'key_live_demo', 'secret_demo')
                ->andReturn([
                    'clientOrderId' => 'ord-55555',
                    'orderStatus' => 'Filled',
                    'limitPrice' => 150.00,
                    'legs' => []
                ]);
        });

        $response = $this->actingAs($user)
            ->getJson('/broker/order/ord-55555');

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('order.orderStatus', 'Filled');

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'Filled',
        ]);
    }

    /**
     * Test fetching order details via the plural endpoint and syncing status.
     */
    public function test_can_fetch_order_details_plural_successfully(): void
    {
        $user = User::factory()->create([
            'tradezero_key_id' => 'key_live_demo',
            'tradezero_secret_key' => 'secret_demo',
            'tradezero_account_id' => 'TZ-DEMO-LIVE-1',
        ]);

        $account = Account::create([
            'user_id' => $user->id,
            'name' => 'TZ-DEMO-LIVE-1',
            'balance' => 112000.00,
            'equity' => 118432.18,
            'account_type' => 'live',
            'provider' => 'tradezero',
            'status' => 'active',
        ]);

        $order = Order::create([
            'account_id' => $account->id,
            'user_id' => $user->id,
            'client_order_id' => 'ord-66666',
            'symbol' => 'AAPL',
            'side' => 'Buy',
            'quantity' => 10,
            'order_type' => 'Limit',
            'limit_price' => 150.00,
            'status' => 'New',
            'provider' => 'tradezero',
        ]);

        $this->partialMock(\App\Services\TradeZeroService::class, function ($mock) {
            $mock->shouldReceive('fetchOrderDetails')
                ->once()
                ->with('TZ-DEMO-LIVE-1', 'ord-66666', 'key_live_demo', 'secret_demo')
                ->andReturn([
                    'clientOrderId' => 'ord-66666',
                    'orderStatus' => 'Filled',
                    'limitPrice' => 150.00,
                    'legs' => []
                ]);
        });

        $response = $this->actingAs($user)
            ->getJson('/broker/orders/ord-66666');

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('order.orderStatus', 'Filled');

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'Filled',
        ]);
    }

    /**
     * Test fetching allowed routes for a TradeZero account.
     */
    public function test_can_fetch_account_routes(): void
    {
        $user = User::factory()->create([
            'tradezero_key_id' => 'key_live_demo',
            'tradezero_secret_key' => 'secret_demo',
            'tradezero_account_id' => 'TZ-DEMO-LIVE-1',
        ]);

        $account = Account::create([
            'user_id' => $user->id,
            'name' => 'TZ-DEMO-LIVE-1',
            'balance' => 112000.00,
            'equity' => 118432.18,
            'account_type' => 'live',
            'provider' => 'tradezero',
            'status' => 'active',
        ]);

        $response = $this->actingAs($user)
            ->getJson('/broker/routes?account_id=' . $account->id);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonStructure([
            'success',
            'routes' => [
                '*' => [
                    'routeName',
                    'securityTypes',
                    'orderTypes',
                    'timesInForce'
                ]
            ]
        ]);
    }

    /**
     * Test placing an order with a specified routing destination.
     */
    public function test_can_place_order_with_route(): void
    {
        $user = User::factory()->create([
            'tradezero_key_id' => 'key_live_demo',
            'tradezero_secret_key' => 'secret_demo',
            'tradezero_account_id' => 'TZ-DEMO-LIVE-1',
        ]);

        $account = Account::create([
            'user_id' => $user->id,
            'name' => 'TZ-DEMO-LIVE-1',
            'balance' => 112000.00,
            'equity' => 118432.18,
            'account_type' => 'live',
            'provider' => 'tradezero',
            'status' => 'active',
        ]);

        $response = $this->actingAs($user)
            ->postJson('/broker/order', [
                'account_id' => $account->id,
                'symbol' => 'AAPL',
                'side' => 'Buy',
                'quantity' => 10,
                'order_type' => 'Market',
                'route' => 'SMART',
            ]);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('order.symbol', 'AAPL');
        $response->assertJsonPath('order.side', 'BUY');
        $response->assertJsonPath('order.quantity', 10);

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'account_id' => $account->id,
            'symbol' => 'AAPL',
            'side' => 'Buy',
            'quantity' => 10,
            'order_type' => 'Market',
            'status' => 'Filled',
        ]);
    }

    /**
     * Test fetching positions for a TradeZero account.
     */
    public function test_can_fetch_account_positions(): void
    {
        $user = User::factory()->create([
            'tradezero_key_id' => 'key_live_demo',
            'tradezero_secret_key' => 'secret_demo',
            'tradezero_account_id' => 'TZ-DEMO-LIVE-1',
        ]);

        $account = Account::create([
            'user_id' => $user->id,
            'name' => 'TZ-DEMO-LIVE-1',
            'balance' => 112000.00,
            'equity' => 118432.18,
            'account_type' => 'live',
            'provider' => 'tradezero',
            'status' => 'active',
        ]);

        $response = $this->actingAs($user)
            ->getJson('/broker/positions?account=' . $account->name);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonStructure([
            'success',
            'positions' => [
                '*' => [
                    'symbol',
                    'quantity',
                    'avgPrice',
                    'close',
                    'marketValue',
                    'unrealized',
                    'unrealizedPct',
                    'side',
                    'securityType'
                ]
            ]
        ]);
    }

    /**
     * Test autocomplete symbol search.
     */
    public function test_can_autocomplete_search_symbols_via_api(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->getJson('/broker/symbols/search?q=MCK');

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonStructure([
            'success',
            'results' => [
                '*' => [
                    'symbol',
                    'name'
                ]
            ]
        ]);
        
        $results = $response->json('results');
        $this->assertNotEmpty($results);
        $this->assertEquals('MCK', $results[0]['symbol']);
    }
}
