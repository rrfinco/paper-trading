<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Account;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TradeZeroMlegTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test placing a multi-leg order successfully under mock sandbox keys.
     */
    public function test_can_place_mleg_order_successfully(): void
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

        $legs = [
            [
                'symbol' => 'AAPL260717C00220000', // Strike 220
                'side' => 'Sell',
                'ratio' => 1,
                'openClose' => 'Open',
            ],
            [
                'symbol' => 'AAPL260717C00215000', // Strike 215
                'side' => 'Buy',
                'ratio' => 1,
                'openClose' => 'Open',
            ]
        ];

        $response = $this->actingAs($user)
            ->postJson('/broker/order', [
                'account_id' => $account->id,
                'symbol' => 'AAPL',
                'security_type' => 'Mleg',
                'quantity' => 1,
                'order_type' => 'Limit',
                'limit_price' => 3.50,
                'legs' => $legs,
            ]);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('order.security_type', 'Mleg');
        $response->assertJsonPath('order.symbol', 'AAPL');
        $response->assertJsonPath('order.limit_price', '$3.50');

        // The response should have the legs sorted by strike ascending.
        // Strike 215 is smaller than Strike 220, so 215 should come first.
        $orderedLegs = $response->json('order.legs');
        $this->assertCount(2, $orderedLegs);
        $this->assertEquals('AAPL260717C00215000', $orderedLegs[0]['symbol']);
        $this->assertEquals('AAPL260717C00220000', $orderedLegs[1]['symbol']);

        // Check DB entry in generic orders table
        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'account_id' => $account->id,
            'symbol' => 'AAPL',
            'side' => 'Buy', // Default fallback side or handled in TradeZeroProvider
            'quantity' => 1,
            'order_type' => 'Limit',
            'limit_price' => 3.50,
        ]);

        // Get the order from database and verify the legs array is cast properly
        $dbOrder = Order::where('symbol', 'AAPL')->where('account_id', $account->id)->first();
        $this->assertNotNull($dbOrder);
        $this->assertIsArray($dbOrder->legs);
        $this->assertEquals('AAPL260717C00215000', $dbOrder->legs[0]['symbol']);
    }

    /**
     * Test validation fails when legs are missing or invalid for Mleg.
     */
    public function test_mleg_order_validation_fails(): void
    {
        $user = User::factory()->create([
            'tradezero_key_id' => 'key_live_demo',
            'tradezero_secret_key' => 'secret_demo',
            'tradezero_account_id' => 'TZ-DEMO-LIVE-1',
        ]);

        // Missing legs
        $response = $this->actingAs($user)
            ->postJson('/broker/order', [
                'symbol' => 'AAPL',
                'security_type' => 'Mleg',
                'quantity' => 1,
                'order_type' => 'Limit',
                'limit_price' => 3.50,
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['legs']);

        // Invalid leg properties
        $response2 = $this->actingAs($user)
            ->postJson('/broker/order', [
                'symbol' => 'AAPL',
                'security_type' => 'Mleg',
                'quantity' => 1,
                'order_type' => 'Limit',
                'limit_price' => 3.50,
                'legs' => [
                    [
                        'symbol' => 'AAPL260717C00215000',
                        'side' => 'Short', // Must be Buy or Sell
                        'ratio' => 0,      // Must be >= 1
                        'openClose' => 'Invalid', // Must be Open or Close
                    ]
                ]
            ]);

        $response2->assertStatus(422);
        $response2->assertJsonValidationErrors(['legs.0.side', 'legs.0.ratio', 'legs.0.openClose']);
    }

    /**
     * Test fetching detail of a multi-leg order.
     */
    public function test_can_fetch_mleg_order_details(): void
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

        // First, create the order in DB
        $order = Order::create([
            'account_id' => $account->id,
            'user_id' => $user->id,
            'client_order_id' => 'mleg-user-1234567890-9999',
            'symbol' => 'AAPL',
            'security_type' => 'Mleg',
            'side' => 'Buy',
            'quantity' => 2,
            'order_type' => 'Limit',
            'legs' => [
                ['symbol' => 'AAPL260717C00215000', 'side' => 'Buy', 'ratio' => 1, 'openClose' => 'Open'],
                ['symbol' => 'AAPL260717C00220000', 'side' => 'Sell', 'ratio' => 1, 'openClose' => 'Open']
            ],
            'limit_price' => 3.00,
            'status' => 'Filled',
            'provider' => 'tradezero',
        ]);

        $response = $this->actingAs($user)
            ->getJson("/broker/order/{$order->client_order_id}");

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('order.legs.0.symbol', 'AAPL260717C00215000');
        $response->assertJsonPath('order.legs.1.symbol', 'AAPL260717C00220000');
    }

    /**
     * Test fetching the consolidated dashboard snapshot successfully.
     */
    public function test_can_fetch_dashboard_snapshot_successfully(): void
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
            ->getJson("/broker/snapshot?account={$account->id}");

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        
        $snapshot = $response->json('snapshot');
        $this->assertNotNull($snapshot);
        $this->assertEquals('TZ-DEMO-LIVE-1', $snapshot['account']);
        $this->assertEquals(112000.00, $snapshot['availableCash']);
        $this->assertEquals(450.50, $snapshot['dayRealized']);
        $this->assertEquals(615.00, $snapshot['dayUnrealized']);
        $this->assertEquals(1065.50, $snapshot['dayPnl']); // realized + unrealized
        $this->assertIsArray($snapshot['positions']);
        $this->assertIsArray($snapshot['orders']);
    }
}
