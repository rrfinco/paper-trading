<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Account;
use App\Models\TradeZeroLocate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TradeZeroLocatesTest extends TestCase
{
    use RefreshDatabase;

    protected function setupUser(): User
    {
        $user = User::factory()->create([
            'tradezero_key_id' => 'key_live_demo',
            'tradezero_secret_key' => 'secret_demo',
            'tradezero_account_id' => 'TZ-DEMO-LIVE-1',
        ]);

        Account::create([
            'user_id' => $user->id,
            'name' => 'TZ-DEMO-LIVE-1',
            'balance' => 112000.00,
            'equity' => 118432.18,
            'account_type' => 'live',
            'provider' => 'tradezero',
            'status' => 'active',
        ]);

        return $user;
    }

    /**
     * Test ETB Check.
     */
    public function test_can_check_easy_to_borrow(): void
    {
        $user = $this->setupUser();

        // Checking an ETB symbol (e.g., AAPL)
        $response = $this->actingAs($user)
            ->getJson('/broker/locate/check-etb?symbol=AAPL');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('isEasyToBorrow', true);

        // Checking an HTB symbol (e.g., AMC)
        $response = $this->actingAs($user)
            ->getJson('/broker/locate/check-etb?symbol=AMC');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('isEasyToBorrow', false);
    }

    /**
     * Test requesting a locate quote.
     */
    public function test_can_request_locate_quote(): void
    {
        $user = $this->setupUser();

        $response = $this->actingAs($user)
            ->postJson('/broker/locate/quote', [
                'symbol' => 'AMC',
                'quantity' => 200,
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'Locate quote request queued successfully.')
            ->assertJsonStructure(['quoteReqID']);

        // Assert 2 offers were created: Pre-Borrow and Single Use (.SU)
        $this->assertDatabaseHas('tradezero_locates', [
            'user_id' => $user->id,
            'symbol' => 'AMC',
            'quantity' => 200,
            'locate_status' => 65, // Offered
            'locate_type' => 3, // Pre-Borrow
        ]);

        $this->assertDatabaseHas('tradezero_locates', [
            'user_id' => $user->id,
            'symbol' => 'AMC',
            'quantity' => 200,
            'locate_status' => 65, // Offered
            'locate_type' => 4, // Single Use
        ]);
    }

    /**
     * Test accepting a locate quote.
     */
    public function test_can_accept_locate_quote(): void
    {
        $user = $this->setupUser();

        // Create locate offers
        $quoteReqId = 'locate-test-uuid';
        TradeZeroLocate::create([
            'user_id' => $user->id,
            'account' => $user->tradezero_account_id,
            'symbol' => 'AMC',
            'quantity' => 100,
            'quote_req_id' => $quoteReqId,
            'locate_status' => 65,
            'locate_price' => 0.0350,
            'locate_type' => 3,
        ]);

        TradeZeroLocate::create([
            'user_id' => $user->id,
            'account' => $user->tradezero_account_id,
            'symbol' => 'AMC',
            'quantity' => 100,
            'quote_req_id' => $quoteReqId . '.SU',
            'locate_status' => 65,
            'locate_price' => 0.0090,
            'locate_type' => 4,
        ]);

        // Accept the single use offer
        $response = $this->actingAs($user)
            ->postJson('/broker/locate/accept', [
                'quote_req_id' => $quoteReqId . '.SU',
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        // Assert Single Use became Filled (50) and has active available_quantity
        $this->assertDatabaseHas('tradezero_locates', [
            'quote_req_id' => $quoteReqId . '.SU',
            'locate_status' => 50,
            'available_quantity' => 100,
        ]);

        // Assert the other Pre-Borrow quote expired (67)
        $this->assertDatabaseHas('tradezero_locates', [
            'quote_req_id' => $quoteReqId,
            'locate_status' => 67,
        ]);
    }

    /**
     * Test cancelling a locate quote.
     */
    public function test_can_cancel_locate_quote(): void
    {
        $user = $this->setupUser();

        $quoteReqId = 'locate-cancel-uuid';
        TradeZeroLocate::create([
            'user_id' => $user->id,
            'account' => $user->tradezero_account_id,
            'symbol' => 'AMC',
            'quantity' => 100,
            'quote_req_id' => $quoteReqId,
            'locate_status' => 65,
            'locate_price' => 0.0350,
            'locate_type' => 3,
        ]);

        TradeZeroLocate::create([
            'user_id' => $user->id,
            'account' => $user->tradezero_account_id,
            'symbol' => 'AMC',
            'quantity' => 100,
            'quote_req_id' => $quoteReqId . '.SU',
            'locate_status' => 65,
            'locate_price' => 0.0090,
            'locate_type' => 4,
        ]);

        // Cancel the offer
        $response = $this->actingAs($user)
            ->postJson('/broker/locate/cancel', [
                'quote_req_id' => $quoteReqId,
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        // Assert both Pre-Borrow and Single Use quotes expired/cancelled (67)
        $this->assertDatabaseHas('tradezero_locates', [
            'quote_req_id' => $quoteReqId,
            'locate_status' => 67,
        ]);

        $this->assertDatabaseHas('tradezero_locates', [
            'quote_req_id' => $quoteReqId . '.SU',
            'locate_status' => 67,
        ]);
    }


    /**
     * Test shorting a HTB symbol fails when no locate is available.
     */
    public function test_shorting_htb_fails_without_locate(): void
    {
        $user = $this->setupUser();

        // AMC is HTB. Try shorting without locate.
        $response = $this->actingAs($user)
            ->postJson('/broker/order', [
                'symbol' => 'AMC',
                'side' => 'Short',
                'quantity' => 100,
                'order_type' => 'Market',
            ]);

        $response->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'Locate required for AMC (Hard-to-Borrow). Please locate at least 100 shares first.');
    }

    /**
     * Test shorting a HTB symbol succeeds when locate is filled.
     */
    public function test_shorting_htb_succeeds_with_locate(): void
    {
        $user = $this->setupUser();

        // Place a locate fill in inventory
        TradeZeroLocate::create([
            'user_id' => $user->id,
            'account' => $user->tradezero_account_id,
            'symbol' => 'AMC',
            'quantity' => 150,
            'quote_req_id' => 'locate-filled-123',
            'locate_status' => 50, // Filled
            'locate_price' => 0.0090,
            'locate_type' => 4,
            'available_quantity' => 150,
        ]);

        // Short 100 shares of AMC
        $response = $this->actingAs($user)
            ->postJson('/broker/order', [
                'symbol' => 'AMC',
                'side' => 'Short',
                'quantity' => 100,
                'order_type' => 'Market',
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        // Assert remaining available locate shares decremented to 50
        $this->assertDatabaseHas('tradezero_locates', [
            'quote_req_id' => 'locate-filled-123',
            'available_quantity' => 50,
        ]);
    }

    /**
     * Test returning/selling back locate shares.
     */
    public function test_can_sell_back_locate(): void
    {
        $user = $this->setupUser();

        // Place a locate fill in inventory
        TradeZeroLocate::create([
            'user_id' => $user->id,
            'account' => $user->tradezero_account_id,
            'symbol' => 'AMC',
            'quantity' => 150,
            'quote_req_id' => 'locate-filled-456',
            'locate_status' => 50, // Filled
            'locate_price' => 0.0090,
            'locate_type' => 4,
            'available_quantity' => 150,
        ]);

        // Sell back 100 shares of AMC
        $response = $this->actingAs($user)
            ->postJson('/broker/locate/sell-back', [
                'symbol' => 'AMC',
                'quantity' => 100,
                'quote_req_id' => 'locate-filled-456',
                'locate_type' => 4
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        // Assert remaining available locate shares decremented to 50
        $this->assertDatabaseHas('tradezero_locates', [
            'quote_req_id' => 'locate-filled-456',
            'available_quantity' => 50,
        ]);
    }

    /**
     * Test fetching historical orders with pagination.
     */
    public function test_can_fetch_historical_orders_with_pagination(): void
    {
        $user = $this->setupUser();
        $account = \App\Models\Account::where('user_id', $user->id)->first();

        // Create a couple of mock orders in database
        \App\Models\Order::create([
            'account_id' => $account->id,
            'user_id' => $user->id,
            'client_order_id' => 'order-hist-1',
            'symbol' => 'TSLA',
            'side' => 'Buy',
            'quantity' => 10,
            'order_type' => 'Market',
            'status' => 'Filled',
            'price_avg' => 200.00,
            'provider' => 'tradezero',
        ]);

        \App\Models\Order::create([
            'account_id' => $account->id,
            'user_id' => $user->id,
            'client_order_id' => 'order-hist-2',
            'symbol' => 'AAPL',
            'side' => 'Sell',
            'quantity' => 20,
            'order_type' => 'Limit',
            'status' => 'Cancelled',
            'limit_price' => 150.00,
            'provider' => 'tradezero',
        ]);

        $response = $this->actingAs($user)
            ->getJson('/broker/orders-history?start_date=' . now()->subDays(5)->format('Y-m-d') . '&page=1');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'orders',
                'pagination' => [
                    'currentPage',
                    'perPage',
                    'total',
                    'totalPages',
                ]
            ]);

        $orders = $response->json('orders');
        $this->assertCount(2, $orders);
        $this->assertEquals('TSLA', $orders[0]['symbol']);
        $this->assertEquals('AAPL', $orders[1]['symbol']);
    }

    /**
     * Test locates page loads successfully.
     */
    public function test_can_access_locates_page(): void
    {
        $user = $this->setupUser();

        $response = $this->actingAs($user)
            ->get('/locates');

        $response->assertStatus(200);
    }

    /**
     * Test fetching order execution fills.
     */
    public function test_can_fetch_order_execution_fills(): void
    {
        $user = $this->setupUser();
        $account = \App\Models\Account::where('user_id', $user->id)->first();

        // Create a filled order in DB (mocking simulated fill)
        \App\Models\Order::create([
            'account_id' => $account->id,
            'user_id' => $user->id,
            'client_order_id' => 'order-fill-mock',
            'symbol' => 'MSFT',
            'side' => 'Buy',
            'quantity' => 50,
            'order_type' => 'Market',
            'status' => 'Filled',
            'price_avg' => 415.50,
            'provider' => 'tradezero',
        ]);

        $response = $this->actingAs($user)
            ->getJson('/broker/orders-fills?start_date=' . now()->subDays(3)->format('Y-m-d'));

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'fills' => [
                    '*' => [
                        'clientOrderId',
                        'symbol',
                        'side',
                        'qty',
                        'price',
                        'tradeDate',
                        'tradeId',
                    ]
                ]
            ]);

        $fills = $response->json('fills');
        $this->assertCount(1, $fills);
        $this->assertEquals('MSFT', $fills[0]['symbol']);
        $this->assertEquals(50, $fills[0]['qty']);
    }
}

