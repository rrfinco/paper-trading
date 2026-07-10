<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\TradeZeroAccount;
use App\Services\TradeZeroService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BrokerConnectionController extends Controller
{
    protected TradeZeroService $tradeZeroService;

    public function __construct(TradeZeroService $tradeZeroService)
    {
        $this->tradeZeroService = $tradeZeroService;
    }

    /**
     * Connect TradeZero Broker Account.
     */
    public function connect(Request $request)
    {
        $request->validate([
            'tradezero_key_id' => ['required', 'string', 'max:255'],
            'tradezero_secret_key' => ['required', 'string'],
        ]);

        $user = Auth::user();
        $keyId = $request->input('tradezero_key_id');
        $secretKey = $request->input('tradezero_secret_key');

        // Fetch accounts using TradeZero Service
        $accountsResponse = $this->tradeZeroService->fetchAccountsList($keyId, $secretKey);

        if (is_null($accountsResponse)) {
            return redirect()->back()
                ->withInput($request->except(['tradezero_secret_key']))
                ->withErrors(['connection' => 'Could not authenticate keys with TradeZero. Please check your credentials and try again.']);
        }

        // Save keys and raw response in users table
        $accounts = $accountsResponse['accounts'] ?? $accountsResponse;
        
        $detailsList = [];
        $primaryAccountId = null;
        
        if (is_array($accounts)) {
            foreach ($accounts as $index => $acc) {
                $accountId = $acc['account'] ?? $acc['accountId'] ?? null;
                if ($accountId) {
                    if ($index === 0) {
                        $primaryAccountId = $accountId;
                    }
                    
                    // Fetch details from /v1/api/account/{accountId}
                    $details = $this->tradeZeroService->fetchAccountDetails($accountId, $keyId, $secretKey);
                    
                    if (is_null($details)) {
                        return redirect()->back()
                            ->withInput($request->except(['tradezero_secret_key']))
                            ->withErrors(['connection' => "Could not retrieve account details for {$accountId} from TradeZero API."]);
                    }

                    // Fetch allowed routes for {$accountId}
                    $routesData = $this->tradeZeroService->fetchAccountRoutes($accountId, $keyId, $secretKey);
                    
                    if (is_null($routesData)) {
                        return redirect()->back()
                            ->withInput($request->except(['tradezero_secret_key']))
                            ->withErrors(['connection' => "Could not retrieve allowed routes for {$accountId} from TradeZero API."]);
                    }

                    $details['routes'] = $routesData['routes'] ?? [];
                    
                    // Save or update in tradezero_accounts database table
                    TradeZeroAccount::updateOrCreate(
                        [
                            'user_id' => $user->id,
                            'account' => $accountId,
                        ],
                        [
                            'account_status' => $details['accountStatus'] ?? 'Active',
                            'account_type' => $acc['accountType'] ?? $details['accountType'] ?? 'Paper',
                            'available_cash' => $details['availableCash'] ?? 0.00,
                            'available_cash_ems' => $details['availableCashEMS'] ?? 0.00,
                            'buying_power' => $details['buyingPower'] ?? 0.00,
                            'equity' => $details['equity'] ?? 0.00,
                            'is_future_account' => $details['isFutureAccount'] ?? false,
                            'leverage' => $details['leverage'] ?? 0.00,
                            'maintenance_deficit' => $details['maintenanceDeficit'] ?? 0.00,
                            'margin_deficit' => $details['marginDeficit'] ?? 0.00,
                            'margin_ratio' => $details['marginRatio'] ?? 0.00,
                            'margin_requirement' => $details['marginRequirement'] ?? 0.00,
                            'opt_contracts_traded' => $details['optContractsTraded'] ?? 0,
                            'opt_level' => $details['optLevel'] ?? 0,
                            'option_cash_total_balance' => $details['optionCashTotalBalance'] ?? 0.00,
                            'option_trading_level' => $details['optionTradingLevel'] ?? 0,
                            'overnight_bp' => $details['overnightBp'] ?? 0.00,
                            'realized' => $details['realized'] ?? 0.00,
                            'shares_traded' => $details['sharesTraded'] ?? 0,
                            'sod_equity' => $details['sodEquity'] ?? 0.00,
                            'total_commissions' => $details['totalCommissions'] ?? 0.00,
                            'total_locate_costs' => $details['totalLocateCosts'] ?? 0.00,
                            'unrealized' => $details['unrealized'] ?? 0.00,
                            'used_leverage' => $details['usedLeverage'] ?? 0.00,
                        ]
                    );

                    // Create or update in generic accounts database table
                    \App\Models\Account::updateOrCreate(
                        [
                            'user_id' => $user->id,
                            'name' => $accountId,
                        ],
                        [
                            'balance' => $details['availableCash'] ?? 0.00,
                            'equity' => $details['equity'] ?? 0.00,
                            'account_type' => strtolower($acc['accountType'] ?? $details['accountType'] ?? 'Paper') === 'live' ? 'live' : 'paper',
                            'provider' => 'tradezero',
                            'status' => 'active',
                        ]
                    );

                    $detailsList[] = $details;
                }
            }
        }

        $user->update([
            'tradezero_key_id' => $keyId,
            'tradezero_secret_key' => $secretKey,
            'tradezero_account_id' => $primaryAccountId,
            'tradezero_response' => $accountsResponse,
            'tradezero_account_details' => $detailsList,
        ]);

        return redirect()->route('dashboard')->with('success', 'TradeZero Broker API verified and connected successfully!');
    }

    /**
     * Disconnect TradeZero Broker Account.
     */
    public function disconnect()
    {
        $user = Auth::user();

        $user->update([
            'tradezero_key_id' => null,
            'tradezero_secret_key' => null,
            'tradezero_account_id' => null,
            'tradezero_response' => null,
            'tradezero_account_details' => null,
        ]);

        // Delete all associated TradeZero accounts
        $user->tradeZeroAccounts()->delete();
        $user->accounts()->where('provider', 'tradezero')->delete();

        return redirect()->route('dashboard')->with('success', 'Broker connection disconnected successfully.');
    }

}
