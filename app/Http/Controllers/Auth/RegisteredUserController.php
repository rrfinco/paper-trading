<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request, \App\Services\TradeZeroService $tradeZeroService): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'tradezero_key_id' => ['required', 'string', 'max:255'],
            'tradezero_secret_key' => ['required', 'string'],
        ]);

        $keyId = $request->input('tradezero_key_id');
        $secretKey = $request->input('tradezero_secret_key');

        // Fetch accounts using TradeZero Service
        $accountsResponse = $tradeZeroService->fetchAccountsList($keyId, $secretKey);

        if (is_null($accountsResponse)) {
            throw ValidationException::withMessages([
                'tradezero_key_id' => __('Could not authenticate keys with TradeZero. Please check your credentials and try again.'),
            ]);
        }

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
                    $details = $tradeZeroService->fetchAccountDetails($accountId, $keyId, $secretKey);
                    
                    if (is_null($details)) {
                        throw ValidationException::withMessages([
                            'tradezero_key_id' => __("Could not retrieve account details for {$accountId} from TradeZero API."),
                        ]);
                    }

                    // Fetch allowed routes for {$accountId}
                    $routesData = $tradeZeroService->fetchAccountRoutes($accountId, $keyId, $secretKey);
                    
                    if (is_null($routesData)) {
                        throw ValidationException::withMessages([
                            'tradezero_key_id' => __("Could not retrieve allowed routes for {$accountId} from TradeZero API."),
                        ]);
                    }

                    $details['routes'] = $routesData['routes'] ?? [];
                    
                    $detailsList[] = $details;
                }
            }
        }

        if (empty($primaryAccountId)) {
            throw ValidationException::withMessages([
                'tradezero_key_id' => __('No accounts were found associated with these TradeZero credentials.'),
            ]);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'tradezero_key_id' => $keyId,
            'tradezero_secret_key' => $secretKey,
            'tradezero_account_id' => $primaryAccountId,
            'tradezero_response' => $accountsResponse,
            'tradezero_account_details' => $detailsList,
        ]);

        // Save account records in tradezero_accounts and generic accounts table
        if (is_array($accounts)) {
            foreach ($accounts as $index => $acc) {
                $accountId = $acc['account'] ?? $acc['accountId'] ?? null;
                if ($accountId) {
                    $details = collect($detailsList)->firstWhere('account', $accountId);
                    if ($details) {
                        \App\Models\TradeZeroAccount::create([
                            'user_id' => $user->id,
                            'account' => $accountId,
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
                        ]);

                        \App\Models\Account::create([
                            'user_id' => $user->id,
                            'name' => $accountId,
                            'balance' => $details['availableCash'] ?? 0.00,
                            'equity' => $details['equity'] ?? 0.00,
                            'account_type' => strtolower($acc['accountType'] ?? $details['accountType'] ?? 'Paper') === 'live' ? 'live' : 'paper',
                            'provider' => 'tradezero',
                            'status' => 'active',
                        ]);
                    }
                }
            }
        }

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
