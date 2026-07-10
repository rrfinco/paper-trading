<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Account;
use App\Models\Position;
use App\Models\Order;
use App\Models\TradeZeroLocate;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    /**
     * Display the Admin Dashboard user listing and KPIs.
     */
    public function index()
    {
        $users = User::withCount(['accounts', 'orders', 'positions'])->get();

        $metrics = [
            'total_users' => User::count(),
            'total_tradezero_connected' => User::whereNotNull('tradezero_account_id')->count(),
            'total_accounts_count' => Account::count(),
            'total_balance_sum' => Account::sum('balance'),
            'total_equity_sum' => Account::sum('equity'),
            'active_orders_count' => Order::whereIn('status', ['new', 'pending', 'PartiallyFilled', 'New', 'PendingNew', 'Accepted'])->count(),
            'active_positions_count' => Position::count(),
        ];

        return view('admin.dashboard', compact('users', 'metrics'));
    }

    /**
     * Retrieve detailed status and records of a specific user.
     */
    public function show(User $user)
    {
        $accounts = Account::where('user_id', $user->id)->get();
        $positions = Position::where('user_id', $user->id)->get();
        $orders = Order::where('user_id', $user->id)->latest()->take(20)->get();
        $locates = TradeZeroLocate::where('user_id', $user->id)->latest()->take(20)->get();

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'created_at' => $user->created_at->toIso8601String(),
                'tradezero_account_id' => $user->tradezero_account_id,
                'is_connected' => !is_null($user->tradezero_account_id),
            ],
            'accounts' => $accounts->map(function ($acc) {
                return [
                    'name' => $acc->name,
                    'provider' => strtoupper($acc->provider),
                    'balance' => (float) $acc->balance,
                    'equity' => (float) $acc->equity,
                    'account_type' => ucfirst($acc->account_type),
                    'status' => ucfirst($acc->status),
                ];
            }),
            'positions' => $positions->map(function ($pos) {
                return [
                    'symbol' => $pos->symbol,
                    'quantity' => (int) $pos->quantity,
                    'avg_price' => (float) $pos->avg_price,
                    'side' => $pos->quantity > 0 ? 'LONG' : 'SHORT',
                ];
            }),
            'orders' => $orders->map(function ($ord) {
                return [
                    'client_order_id' => $ord->client_order_id,
                    'symbol' => $ord->symbol,
                    'side' => strtoupper($ord->side),
                    'quantity' => (int) $ord->quantity,
                    'order_type' => $ord->order_type,
                    'limit_price' => $ord->limit_price ? (float) $ord->limit_price : null,
                    'status' => strtoupper($ord->status),
                    'created_at' => $ord->created_at->toIso8601String(),
                ];
            }),
            'locates' => $locates->map(function ($loc) {
                return [
                    'symbol' => $loc->symbol,
                    'quantity' => (int) $loc->requested_quantity,
                    'available' => (int) $loc->available_quantity,
                    'rate' => (float) $loc->locate_price,
                    'status' => $loc->locate_status === 50 ? 'FILLED' : ($loc->locate_status === 65 ? 'OFFERED' : 'PENDING'),
                    'created_at' => $loc->created_at->toIso8601String(),
                ];
            })
        ]);
    }
}
