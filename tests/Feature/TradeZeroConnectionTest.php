<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TradeZeroConnectionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test validation error when connecting with invalid keys.
     */
    public function test_cannot_connect_with_invalid_credentials(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->post('/broker/connect', [
                'tradezero_key_id' => 'INVALID_KEY',
                'tradezero_secret_key' => 'some_secret',
            ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['connection']);
        
        $user->refresh();
        $this->assertNull($user->tradezero_key_id);
        $this->assertNull($user->tradezero_response);
    }

    /**
     * Test successful connection with mock live keys.
     */
    public function test_can_connect_successfully_with_valid_keys(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->post('/broker/connect', [
                'tradezero_key_id' => 'key_live_demo',
                'tradezero_secret_key' => 'secret_demo',
            ]);

        $response->assertRedirect('/dashboard');
        $response->assertSessionHasNoErrors();

        $user->refresh();
        $this->assertEquals('key_live_demo', $user->tradezero_key_id);
        $this->assertEquals('TZ-DEMO-LIVE-1', $user->tradezero_account_id);
        
        // Assert the raw response was populated
        $this->assertNotNull($user->tradezero_response);
        $this->assertArrayHasKey('accounts', $user->tradezero_response);
        
        $account = $user->tradezero_response['accounts'][0];
        $this->assertEquals('TZ-DEMO-LIVE-1', $account['account']);
        $this->assertEquals('Live', $account['accountType']);
        $this->assertEquals(450000.00, $account['buyingPower']);

        // Assert the detailed response was populated
        $this->assertNotNull($user->tradezero_account_details);
        $this->assertEquals('TZ-DEMO-LIVE-1', $user->tradezero_account_details[0]['account']);
        $this->assertEquals('Active', $user->tradezero_account_details[0]['accountStatus']);
        $this->assertEquals(450000.00, $user->tradezero_account_details[0]['buyingPower']);
        $this->assertEquals(112000.00, $user->tradezero_account_details[0]['availableCash']);
        $this->assertArrayHasKey('routes', $user->tradezero_account_details[0]);
        $this->assertCount(2, $user->tradezero_account_details[0]['routes']);
        $this->assertEquals('PAPER', $user->tradezero_account_details[0]['routes'][0]['routeName']);

        // Assert database records in tradezero_accounts table
        $this->assertDatabaseHas('tradezero_accounts', [
            'user_id' => $user->id,
            'account' => 'TZ-DEMO-LIVE-1',
            'account_status' => 'Active',
            'account_type' => 'Live',
            'available_cash' => 112000.00,
            'available_cash_ems' => 0.00,
            'buying_power' => 450000.00,
            'equity' => 118432.18,
            'is_future_account' => false,
            'leverage' => 4.00,
            'maintenance_deficit' => 0.00,
            'margin_deficit' => 0.00,
            'margin_ratio' => 100.00,
            'margin_requirement' => 0.00,
            'opt_contracts_traded' => 12,
            'opt_level' => 2,
            'option_cash_total_balance' => 30000.00,
            'option_trading_level' => 2,
            'overnight_bp' => 220000.00,
            'realized' => 450.50,
            'shares_traded' => 5000,
            'sod_equity' => 117981.68,
            'total_commissions' => 75.25,
            'total_locate_costs' => 15.00,
            'unrealized' => 120.50,
            'used_leverage' => 2.10,
        ]);

        $this->assertDatabaseHas('tradezero_accounts', [
            'user_id' => $user->id,
            'account' => 'TZ-DEMO-LIVE-2',
            'account_status' => 'Active',
            'account_type' => 'Live',
            'available_cash' => 60000.00,
            'available_cash_ems' => 0.00,
            'buying_power' => 250000.00,
            'equity' => 64000.00,
            'is_future_account' => false,
            'leverage' => 4.00,
            'maintenance_deficit' => 0.00,
            'margin_deficit' => 0.00,
            'margin_ratio' => 100.00,
            'margin_requirement' => 0.00,
            'opt_contracts_traded' => 5,
            'opt_level' => 2,
            'option_cash_total_balance' => 15000.00,
            'option_trading_level' => 2,
            'overnight_bp' => 120000.00,
            'realized' => 150.00,
            'shares_traded' => 2500,
            'sod_equity' => 63850.00,
            'total_commissions' => 25.00,
            'total_locate_costs' => 0.00,
            'unrealized' => -50.00,
            'used_leverage' => 1.50,
        ]);
    }

    /**
     * Test unlinking TradeZero connection.
     */
    public function test_can_disconnect_broker_connection(): void
    {
        $user = User::factory()->create([
            'tradezero_key_id' => 'key_demo',
            'tradezero_secret_key' => 'secret_demo',
            'tradezero_account_id' => 'TZ-DEMO-PAPER',
            'tradezero_response' => ['accounts' => []],
        ]);

        // Create a mock account record to check deletion
        $account = \App\Models\TradeZeroAccount::create([
            'user_id' => $user->id,
            'account' => 'TZ-DEMO-PAPER',
            'available_cash' => 10000.00,
        ]);

        $response = $this->actingAs($user)
            ->post('/broker/disconnect');

        $response->assertRedirect('/dashboard');
        
        $user->refresh();
        $this->assertNull($user->tradezero_key_id);
        $this->assertNull($user->tradezero_secret_key);
        $this->assertNull($user->tradezero_account_id);
        $this->assertNull($user->tradezero_response);

        // Assert that the tradezero_accounts table is empty
        $this->assertDatabaseMissing('tradezero_accounts', [
            'id' => $account->id,
        ]);
    }
}
