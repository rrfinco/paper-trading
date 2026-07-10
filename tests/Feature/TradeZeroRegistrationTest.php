<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TradeZeroRegistrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test validation error when registering with invalid TradeZero credentials.
     */
    public function test_cannot_register_with_invalid_tradezero_credentials(): void
    {
        $response = $this->post('/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'tradezero_key_id' => 'INVALID_KEY',
            'tradezero_secret_key' => 'invalid_secret',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['tradezero_key_id']);
        
        $this->assertDatabaseCount('users', 0);
    }

    /**
     * Test successful registration with mock TradeZero live credentials.
     */
    public function test_can_register_successfully_with_valid_mock_credentials(): void
    {
        $response = $this->post('/register', [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'tradezero_key_id' => 'key_live_demo',
            'tradezero_secret_key' => 'secret_demo',
        ]);

        $response->assertRedirect('/dashboard');
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('users', [
            'email' => 'jane@example.com',
            'tradezero_key_id' => 'key_live_demo',
            'tradezero_account_id' => 'TZ-DEMO-LIVE-1',
        ]);

        $user = User::where('email', 'jane@example.com')->first();
        $this->assertNotNull($user);
        $this->assertNotNull($user->tradezero_account_details);
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
            'buying_power' => 450000.00,
            'equity' => 118432.18,
        ]);

        $this->assertDatabaseHas('tradezero_accounts', [
            'user_id' => $user->id,
            'account' => 'TZ-DEMO-LIVE-2',
            'account_status' => 'Active',
            'account_type' => 'Live',
            'available_cash' => 60000.00,
            'buying_power' => 250000.00,
            'equity' => 64000.00,
        ]);

        // Assert generic accounts are also created
        $this->assertDatabaseHas('accounts', [
            'user_id' => $user->id,
            'name' => 'TZ-DEMO-LIVE-1',
            'balance' => 112000.00,
            'equity' => 118432.18,
            'account_type' => 'live',
            'provider' => 'tradezero',
            'status' => 'active',
        ]);
    }
}
