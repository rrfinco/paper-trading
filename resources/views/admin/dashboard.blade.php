<x-app-layout>
    <!-- Premium Admin Control Panel Styles -->
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap');

        :root {
            --admin-blue: #4f46e5;
            --admin-blue-glow: rgba(79, 70, 229, 0.08);
            --admin-green: #10b981;
            --admin-red: #ef4444;
            --admin-bg-base: #f8fafc;
            --admin-bg-card: #ffffff;
            --admin-border: #e2e8f0;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --font-mono: 'JetBrains Mono', monospace;
        }

        body {
            background-color: var(--admin-bg-base);
            color: var(--text-main);
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Outfit', sans-serif !important;
            letter-spacing: -0.02em;
        }

        .admin-card {
            background-color: var(--admin-bg-card);
            border: 1px solid var(--admin-border);
            border-radius: 1rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
            transition: all 0.2s ease-in-out;
        }

        .admin-card:hover {
            border-color: rgba(79, 70, 229, 0.2);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
            height: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.01);
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(0, 0, 0, 0.08);
            border-radius: 2px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: var(--admin-blue);
        }
    </style>

    <div x-data="adminDashboard()" id="admin_dashboard_container" class="min-h-screen bg-[#f8fafc] text-slate-800 flex antialiased">
        
        <!-- ── LEFT NAVIGATION SIDEBAR ── -->
        <aside class="w-64 p-6 bg-white border-r border-slate-200 flex flex-col justify-between shrink-0 z-20 shadow-sm">
            <div class="space-y-8">
                <!-- Branding -->
                <div class="flex items-center gap-2">
                    <div class="h-9 w-9 rounded-xl bg-gradient-to-tr from-indigo-600 to-violet-700 flex items-center justify-center shadow-lg shadow-indigo-600/20 shrink-0">
                        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <span class="text-xl font-black tracking-tight text-slate-900 select-none">Admin<span class="text-indigo-600">Portal</span></span>
                </div>

                <!-- Navigation Tabs -->
                <nav class="space-y-1.5">
                    <p class="text-slate-400 text-[10px] font-bold uppercase tracking-wider mb-2 select-none">Control Panels</p>
                    <button class="w-full flex items-center rounded-xl text-xs font-bold tracking-wide bg-indigo-50 text-indigo-600 border-l-3 border-indigo-600 gap-3.5 px-4 py-3">
                        <span class="text-lg">👥</span>
                        <span>User Accounts</span>
                    </button>
                </nav>
            </div>

            <!-- Profile & Disconnect -->
            <div class="border-t border-slate-100 pt-4">
                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs font-bold text-slate-500 hover:text-slate-950 transition-colors cursor-pointer">
                        <span class="text-sm">🚪</span>
                        <span>Logout Dashboard</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- ── MAIN BODY CONTENT CONTAINER ── -->
        <div class="flex-1 flex flex-col min-w-0 overflow-y-auto">

            <!-- ── TOP BAR (Session Details) ── -->
            <header class="h-20 border-b border-slate-200 px-8 flex items-center justify-between gap-6 shrink-0 bg-white/90 backdrop-blur-md sticky top-0 z-10 shadow-sm">
                <div>
                    <h2 class="text-lg font-black text-slate-800">User Management Center</h2>
                    <p class="text-xs text-slate-400 font-medium">Overview of registered traders and broker credentials</p>
                </div>

                <!-- User Profile Session Info -->
                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-2.5 text-right">
                        <div class="text-xs pr-1">
                            <h5 class="font-bold text-slate-800">{{ auth()->user()->name }}</h5>
                            <span class="text-[10px] bg-indigo-50 border border-indigo-200 text-indigo-700 px-1.5 py-0.5 rounded-md font-bold uppercase mt-1 inline-block">Administrator</span>
                        </div>
                        <div class="h-9 w-9 rounded-full bg-indigo-50 border border-indigo-200 flex items-center justify-center text-indigo-650 font-extrabold text-xs shrink-0 shadow-sm">
                            AD
                        </div>
                    </div>
                </div>
            </header>

            <!-- ── CORE KPI PANEL DESK ── -->
            <main class="flex-1 p-8 space-y-6 max-w-7xl w-full mx-auto">
                
                <!-- KPI Indicators Row -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="admin-card p-6 flex flex-col justify-between">
                        <div class="flex justify-between items-center text-slate-400">
                            <span class="text-[9px] font-extrabold uppercase tracking-wider">Total User Registrations</span>
                            <span class="text-lg">👥</span>
                        </div>
                        <div class="mt-4">
                            <h3 class="text-2xl font-black text-slate-900 font-mono">{{ $metrics['total_users'] }}</h3>
                            <span class="text-[10px] text-slate-400 font-medium">Registered accounts in database</span>
                        </div>
                    </div>

                    <div class="admin-card p-6 flex flex-col justify-between">
                        <div class="flex justify-between items-center text-slate-400">
                            <span class="text-[9px] font-extrabold uppercase tracking-wider">TradeZero Integrations</span>
                            <span class="text-lg">🔌</span>
                        </div>
                        <div class="mt-4">
                            <h3 class="text-2xl font-black text-indigo-600 font-mono">{{ $metrics['total_tradezero_connected'] }}</h3>
                            <span class="text-[10px] text-slate-400 font-medium">Connected active TradeZero clients</span>
                        </div>
                    </div>

                    <div class="admin-card p-6 flex flex-col justify-between">
                        <div class="flex justify-between items-center text-slate-400">
                            <span class="text-[9px] font-extrabold uppercase tracking-wider">Total Cash Liquidity</span>
                            <span class="text-lg">💵</span>
                        </div>
                        <div class="mt-4">
                            <h3 class="text-2xl font-black text-emerald-600 font-mono">${{ number_format($metrics['total_balance_sum'], 2) }}</h3>
                            <span class="text-[10px] text-slate-400 font-medium">Combined user cash accounts</span>
                        </div>
                    </div>

                    <div class="admin-card p-6 flex flex-col justify-between">
                        <div class="flex justify-between items-center text-slate-400">
                            <span class="text-[9px] font-extrabold uppercase tracking-wider">Cumulative Valuation</span>
                            <span class="text-lg">📈</span>
                        </div>
                        <div class="mt-4">
                            <h3 class="text-2xl font-black text-indigo-600 font-mono">${{ number_format($metrics['total_equity_sum'], 2) }}</h3>
                            <span class="text-[10px] text-slate-400 font-medium">Sum of all equity valuation models</span>
                        </div>
                    </div>
                </div>

                <!-- Users Data Grid Table -->
                <div class="admin-card p-6 bg-white shadow-sm">
                    <div class="flex justify-between items-center pb-4 border-b border-slate-100 mb-4 select-none">
                        <h4 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider">Trader Directory</h4>
                        <span class="px-2 py-0.5 rounded bg-slate-50 border border-slate-200 text-slate-500 text-[10px] font-bold">{{ count($users) }} accounts registered</span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="border-b border-slate-200 text-slate-400 text-[9px] font-extrabold uppercase tracking-widest">
                                    <th class="pb-2">ID</th>
                                    <th class="pb-2">User Details</th>
                                    <th class="pb-2 text-center">Accounts</th>
                                    <th class="pb-2 text-center">Positions</th>
                                    <th class="pb-2 text-center">Orders</th>
                                    <th class="pb-2">TradeZero status</th>
                                    <th class="pb-2">Joined Date</th>
                                    <th class="pb-2 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-sans text-slate-650">
                                @foreach($users as $user)
                                    <tr class="hover:bg-slate-50/50 transition-colors">
                                        <td class="py-3.5 font-mono text-[10px] text-slate-400">#{{ $user->id }}</td>
                                        <td class="py-3.5">
                                            <div class="flex items-center gap-3">
                                                <div class="h-8 w-8 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center font-bold text-slate-500 uppercase text-[10px]">
                                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                                </div>
                                                <div>
                                                    <span class="font-bold text-slate-900 block">{{ $user->name }}</span>
                                                    <span class="text-[10px] text-slate-400 font-mono block">{{ $user->email }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-3.5 text-center font-mono font-bold">{{ $user->accounts_count }}</td>
                                        <td class="py-3.5 text-center font-mono">{{ $user->positions_count }}</td>
                                        <td class="py-3.5 text-center font-mono">{{ $user->orders_count }}</td>
                                        <td class="py-3.5">
                                            @if($user->tradezero_account_id)
                                                <span class="px-2 py-0.5 text-[9px] font-bold rounded-lg border border-indigo-250 bg-indigo-50 text-indigo-600 font-mono">
                                                    {{ $user->tradezero_account_id }}
                                                </span>
                                            @else
                                                <span class="px-2 py-0.5 text-[9px] font-bold rounded-lg border border-slate-200 bg-slate-50 text-slate-400">
                                                    Not connected
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-3.5 text-slate-500 font-mono text-[11px]">{{ $user->created_at->format('Y-m-d H:i') }}</td>
                                        <td class="py-3.5 text-right">
                                            <button @click="openUserDetails({{ $user->id }})" class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-[10px] font-bold cursor-pointer transition-colors shadow-sm select-none">
                                                View details
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>

        <!-- ── PREMIUM DETAIL DRAWER/MODAL PANEL ── -->
        <div x-show="drawerOpen" class="fixed inset-0 z-50 overflow-hidden" style="display: none;">
            <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" @click="drawerOpen = false" x-transition.opacity></div>

            <div class="fixed inset-y-0 right-0 max-w-full flex pl-10">
                <div class="w-screen max-w-2xl bg-white flex flex-col shadow-2xl border-l border-slate-200" 
                     x-transition:enter="transform transition ease-in-out duration-300 sm:duration-300"
                     x-transition:enter-start="translate-x-full"
                     x-transition:enter-end="translate-x-0"
                     x-transition:leave="transform transition ease-in-out duration-300 sm:duration-300"
                     x-transition:leave-start="translate-x-0"
                     x-transition:leave-end="translate-x-full">
                    
                    <!-- Drawer Header -->
                    <div class="h-20 border-b border-slate-150 px-6 flex items-center justify-between bg-slate-50">
                        <div class="flex items-center gap-3">
                            <div class="h-10 w-10 rounded-xl bg-indigo-50 border border-indigo-200 flex items-center justify-center font-bold text-indigo-650 uppercase">
                                <span x-text="selectedUser ? selectedUser.name.substring(0,2) : 'US'"></span>
                            </div>
                            <div>
                                <h3 class="text-sm font-black text-slate-800" x-text="selectedUser ? selectedUser.name : 'Trader Details'"></h3>
                                <p class="text-[10px] text-slate-400 font-mono" x-text="selectedUser ? selectedUser.email : ''"></p>
                            </div>
                        </div>
                        <button @click="drawerOpen = false" class="p-2 hover:bg-slate-200 rounded-lg text-slate-400 hover:text-slate-700 cursor-pointer transition-colors">
                            <span class="text-lg">✕</span>
                        </button>
                    </div>

                    <!-- Loading view -->
                    <div x-show="loading" class="flex-1 flex flex-col items-center justify-center text-slate-400 gap-2">
                        <div class="w-6 h-6 border-2 border-indigo-600 border-t-transparent rounded-full animate-spin"></div>
                        <span class="text-xs font-bold font-sans">Retrieving account data records...</span>
                    </div>

                    <!-- Drawer content -->
                    <div x-show="!loading && selectedUser" class="flex-1 overflow-y-auto p-6 space-y-6 custom-scrollbar font-sans text-xs">
                        
                        <!-- Account profile summary metadata -->
                        <div class="grid grid-cols-2 gap-4 bg-slate-50 p-4 rounded-xl border border-slate-150">
                            <div>
                                <span class="text-[9px] uppercase tracking-wider text-slate-400 font-bold block">Security Authorization</span>
                                <span class="font-bold text-slate-750 font-mono capitalize" x-text="selectedUser ? selectedUser.role : '-'"></span>
                            </div>
                            <div>
                                <span class="text-[9px] uppercase tracking-wider text-slate-400 font-bold block">TradeZero Connection Code</span>
                                <span class="font-bold text-slate-750 font-mono" x-text="selectedUser && selectedUser.tradezero_account_id ? selectedUser.tradezero_account_id : 'Not Connectioned'"></span>
                            </div>
                        </div>

                        <!-- 1. Connected Accounts -->
                        <div>
                            <h5 class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider pb-1.5 border-b border-slate-100 mb-2">Connected Accounts</h5>
                            <div class="space-y-2">
                                <template x-for="acc in details.accounts" :key="acc.name">
                                    <div class="border border-slate-150 rounded-xl p-3 flex justify-between items-center hover:border-slate-300 transition-colors">
                                        <div>
                                            <span class="font-bold text-slate-800 block" x-text="acc.name"></span>
                                            <span class="text-[9px] text-slate-400 font-bold uppercase" x-text="acc.provider + ' (' + acc.account_type + ')'"></span>
                                        </div>
                                        <div class="text-right">
                                            <span class="font-bold text-indigo-600 font-mono block" x-text="'$' + Number(acc.equity).toLocaleString('en-US', {minimumFractionDigits:2})"></span>
                                            <span class="text-[9px] text-slate-400 font-mono" x-text="'Cash: $' + Number(acc.balance).toLocaleString('en-US', {minimumFractionDigits:2})"></span>
                                        </div>
                                    </div>
                                </template>
                                <template x-if="details.accounts.length === 0">
                                    <p class="text-slate-400 italic py-2">No connected accounts found for this trader.</p>
                                </template>
                            </div>
                        </div>

                        <!-- 2. Active Positions -->
                        <div>
                            <h5 class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider pb-1.5 border-b border-slate-100 mb-2">Active Positions</h5>
                            <div class="overflow-x-auto">
                                <table class="w-full text-left font-mono">
                                    <thead>
                                        <tr class="border-b border-slate-150 text-slate-400 text-[8px] font-extrabold uppercase tracking-widest pb-1">
                                            <th class="pb-1.5">Symbol</th>
                                            <th class="pb-1.5 text-right">Shares</th>
                                            <th class="pb-1.5 text-right">Avg Entry</th>
                                            <th class="pb-1.5 text-right">Side</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 text-slate-700">
                                        <template x-for="pos in details.positions" :key="pos.symbol">
                                            <tr class="hover:bg-slate-50/50">
                                                <td class="py-2 font-sans font-black text-slate-900" x-text="pos.symbol"></td>
                                                <td class="py-2 text-right" x-text="pos.quantity"></td>
                                                <td class="py-2 text-right" x-text="'$' + Number(pos.avg_price).toFixed(2)"></td>
                                                <td class="py-2 text-right">
                                                    <span class="px-1.5 py-0.5 text-[8px] font-bold rounded uppercase border" :class="pos.side === 'LONG' ? 'border-emerald-250 bg-emerald-50 text-emerald-600' : 'border-rose-250 bg-rose-50 text-rose-600'" x-text="pos.side"></span>
                                                </td>
                                            </tr>
                                        </template>
                                        <template x-if="details.positions.length === 0">
                                            <tr>
                                                <td colspan="4" class="py-4 text-center text-slate-400 font-sans italic">No open positions.</td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- 3. Recent Orders -->
                        <div>
                            <h5 class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider pb-1.5 border-b border-slate-100 mb-2">Recent Orders Log</h5>
                            <div class="overflow-x-auto">
                                <table class="w-full text-left font-mono">
                                    <thead>
                                        <tr class="border-b border-slate-150 text-slate-400 text-[8px] font-extrabold uppercase tracking-widest pb-1">
                                            <th class="pb-1.5">Symbol</th>
                                            <th class="pb-1.5 text-right">Qty</th>
                                            <th class="pb-1.5 text-right">Limit Price</th>
                                            <th class="pb-1.5 text-right">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 text-slate-700">
                                        <template x-for="ord in details.orders" :key="ord.client_order_id">
                                            <tr class="hover:bg-slate-50/50">
                                                <td class="py-2">
                                                    <span class="font-sans font-black text-slate-900" x-text="ord.symbol"></span>
                                                    <span class="text-[8px] text-slate-400 block" x-text="ord.side"></span>
                                                </td>
                                                <td class="py-2 text-right" x-text="ord.quantity"></td>
                                                <td class="py-2 text-right" x-text="ord.limit_price ? '$' + Number(ord.limit_price).toFixed(2) : 'MKT'"></td>
                                                <td class="py-2 text-right">
                                                    <span class="px-1 py-0.5 rounded text-[8px] font-bold border uppercase" :class="ord.status === 'FILLED' ? 'border-emerald-250 bg-emerald-50 text-emerald-600' : 'border-slate-250 bg-slate-50 text-slate-500'" x-text="ord.status"></span>
                                                </td>
                                            </tr>
                                        </template>
                                        <template x-if="details.orders.length === 0">
                                            <tr>
                                                <td colspan="4" class="py-4 text-center text-slate-400 font-sans italic">No orders logged.</td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>

    </div>

    <!-- Admin dashboard page controller script -->
    <script>
        function adminDashboard() {
            return {
                drawerOpen: false,
                loading: false,
                selectedUser: null,
                details: {
                    accounts: [],
                    positions: [],
                    orders: [],
                    locates: []
                },

                openUserDetails(userId) {
                    this.drawerOpen = true;
                    this.loading = true;
                    this.selectedUser = null;
                    
                    fetch(`/admin/users/${userId}`)
                        .then(res => {
                            if (!res.ok) throw new Error('Details retrieve failed');
                            return res.json();
                        })
                        .then(data => {
                            if (data.success) {
                                this.selectedUser = data.user;
                                this.details.accounts = data.accounts || [];
                                this.details.positions = data.positions || [];
                                this.details.orders = data.orders || [];
                                this.details.locates = data.locates || [];
                            }
                        })
                        .catch(err => {
                            console.error('Fetch admin details failed:', err);
                            this.drawerOpen = false;
                        })
                        .finally(() => {
                            this.loading = false;
                        });
                }
            };
        }
    </script>
</x-app-layout>
