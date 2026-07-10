<x-app-layout>
    @php
        // Auto-seed default Paper Account if user has none
        if (auth()->user()->accounts()->count() === 0) {
            \App\Models\Account::create([
                'user_id' => auth()->user()->id,
                'name' => 'Paper Account',
                'balance' => 100000.00,
                'equity' => 100000.00,
                'account_type' => 'paper',
                'provider' => 'paper',
                'status' => 'active',
            ]);
        }

        $accounts = auth()->user()->accounts;
        $activeAccountId = request('account');
        
        // Find active account
        $primaryAccount = null;
        if ($activeAccountId) {
            $primaryAccount = $accounts->firstWhere('id', $activeAccountId) ?? $accounts->firstWhere('name', $activeAccountId);
        }
        if (!$primaryAccount) {
            if (auth()->user()->tradezero_account_id) {
                $primaryAccount = $accounts->firstWhere('name', auth()->user()->tradezero_account_id);
            }
            if (!$primaryAccount) {
                $primaryAccount = $accounts->first();
            }
        }
        $isConnected = true;
        $orders = $primaryAccount ? $primaryAccount->orders()->latest()->take(15)->get() : collect();
    @endphp

    <!-- TradingView Lightweight Charts Script Import -->
    <script src="https://unpkg.com/lightweight-charts/dist/lightweight-charts.standalone.production.js"></script>

    <!-- Custom Style Overrides for Premium Light SaaS Dashboard -->
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap');

        :root {
            --neo-blue: #2563eb;
            --neo-blue-glow: rgba(37, 99, 235, 0.08);
            --neo-green: #10b981;
            --neo-green-glow: rgba(16, 185, 129, 0.08);
            --neo-red: #ef4444;
            --neo-red-glow: rgba(239, 68, 68, 0.08);
            --bg-dark-900: #f4f5f8;
            --bg-dark-800: #ffffff;
            --bg-dark-700: #e4e4e7;
            --text-muted: #71717a;
            --font-mono: 'JetBrains Mono', monospace;
        }

        body {
            background-color: var(--bg-dark-900);
            color: #18181b;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Outfit', sans-serif !important;
            letter-spacing: -0.02em;
        }

        .premium-card {
            background-color: var(--bg-dark-800);
            border: 1px solid rgba(0, 0, 0, 0.06);
            border-radius: 1.25rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05), 0 1px 2px 0 rgba(0, 0, 0, 0.03);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .premium-card:hover {
            border-color: rgba(37, 99, 235, 0.25);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        }

        .glowing-sidebar-item.active {
            background-color: var(--neo-blue-glow);
            border-left: 3px solid var(--neo-blue);
            color: var(--neo-blue);
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
            height: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.02);
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(0, 0, 0, 0.08);
            border-radius: 2px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: var(--neo-blue);
        }
    </style>

    <div x-data="dashboardPage()" id="main_dashboard_container" class="min-h-screen bg-[#f4f5f8] text-zinc-800 flex antialiased">

        <!-- ── LEFT SIDEBAR PANEL (Stovest Style) ── -->
        <aside :class="sidebarCollapsed ? 'w-20 px-3 py-6' : 'w-64 p-6'" class="bg-white border-r border-zinc-200 flex flex-col justify-between shrink-0 z-20 shadow-sm">
            <div class="space-y-8">
                <!-- Branding -->
                <div class="flex items-center" :class="sidebarCollapsed ? 'justify-center' : 'gap-2'">
                    <div class="h-9 w-9 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-700 flex items-center justify-center shadow-lg shadow-blue-600/20 shrink-0">
                        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                    </div>
                    <span x-show="!sidebarCollapsed" class="text-xl font-black tracking-tight text-zinc-900 select-none">Paper<span class="text-blue-600">Trading</span></span>
                </div>

                <!-- Navigation Tabs -->
                <nav class="space-y-1.5">
                    <p x-show="!sidebarCollapsed" class="text-zinc-400 text-[10px] font-bold uppercase tracking-wider mb-2 select-none">Main Menu</p>
                    <button class="w-full flex items-center rounded-xl text-xs font-bold tracking-wide glowing-sidebar-item active" :class="sidebarCollapsed ? 'justify-center py-3 px-0' : 'gap-3.5 px-4 py-3'">
                        <span class="text-lg">📊</span>
                        <span x-show="!sidebarCollapsed">Dashboard</span>
                    </button>
                    <a href="{{ route('trade') }}" class="w-full flex items-center rounded-xl text-xs font-bold tracking-wide text-zinc-500 hover:text-zinc-900 hover:bg-zinc-50" :class="sidebarCollapsed ? 'justify-center py-3 px-0' : 'gap-3.5 px-4 py-3'">
                        <span class="text-lg">📈</span>
                        <span x-show="!sidebarCollapsed">Trade Entry</span>
                    </a>
                    <a href="{{ route('locates') }}" class="w-full flex items-center rounded-xl text-xs font-bold tracking-wide text-zinc-500 hover:text-zinc-900 hover:bg-zinc-50" :class="sidebarCollapsed ? 'justify-center py-3 px-0' : 'gap-3.5 px-4 py-3'">
                        <span class="text-lg">🔑</span>
                        <span x-show="!sidebarCollapsed">Locates Manager</span>
                    </a>
                </nav>
            </div>
        </aside>

        <!-- ── MAIN BODY CONTENT CONTAINER ── -->
        <div class="flex-1 flex flex-col min-w-0 overflow-y-auto">

            <!-- ── TOP BAR (Search & Account Selector) ── -->
            <header class="h-20 border-b border-zinc-200 px-6 md:px-8 flex items-center justify-between gap-6 shrink-0 bg-white/90 backdrop-blur-md sticky top-0 z-30">
                <div class="flex items-center gap-4">
                    <!-- Collapse Toggle Button -->
                    <button @click="sidebarCollapsed = !sidebarCollapsed" class="p-2 hover:bg-zinc-100 text-zinc-650 hover:text-zinc-900 border border-zinc-200/80 rounded-xl cursor-pointer shadow-sm select-none" title="Toggle Sidebar">
                        <svg class="w-4 h-4" :class="sidebarCollapsed ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                    <!-- Search bar placeholder -->
                    <div class="relative w-72">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-zinc-400 text-xs">🔍</span>
                        <input type="text" placeholder="Ask stocks.ai anything" class="w-full bg-zinc-100 border border-zinc-200/80 rounded-xl pl-9 pr-4 py-2 text-xs text-zinc-800 placeholder-zinc-400 focus:outline-none focus:border-blue-500/50 transition-colors" />
                    </div>
                </div>

                <!-- Account selections and User details -->
                <div class="flex items-center gap-4">
                    <!-- Dropdown Account Switcher -->
                    <div class="flex items-center gap-2">
                        <span class="text-zinc-400 text-[10px] uppercase font-bold tracking-wider font-mono">Account:</span>
                        <select onchange="window.location.search = 'account=' + this.value" class="bg-white border border-zinc-200 text-zinc-800 rounded-xl text-xs font-bold px-3 py-2 focus:outline-none cursor-pointer shadow-sm hover:border-zinc-300">
                            @foreach($accounts as $acc)
                                <option value="{{ $acc->name }}" {{ $acc->id === $primaryAccount->id ? 'selected' : '' }}>
                                    {{ $acc->name }} ({{ strtoupper($acc->provider) }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="w-px h-6 bg-zinc-200"></div>

                    <!-- Profile Dropdown Container -->
                    <div x-data="{ open: false }" class="relative">
                        <!-- Profile Trigger Button -->
                        <button @click="open = !open" @click.outside="open = false" class="flex items-center gap-2.5 p-1.5 rounded-xl hover:bg-zinc-100 transition-colors cursor-pointer select-none text-left focus:outline-none text-zinc-800">
                            <div class="h-9 w-9 rounded-full bg-blue-50 border border-blue-200 flex items-center justify-center text-blue-600 font-extrabold text-xs shrink-0 shadow-sm">
                                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                            </div>
                            <div class="hidden sm:block text-left text-xs pr-1">
                                <h5 class="font-bold text-zinc-800 leading-normal flex items-center gap-1.5">
                                    {{ auth()->user()->name }}
                                    <svg class="w-3.5 h-3.5 text-zinc-400 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </h5>
                                <span class="text-[10px] text-zinc-500 block leading-none mt-0.5">{{ auth()->user()->email }}</span>
                            </div>
                        </button>

                        <!-- Dropdown Menu -->
                        <div x-show="open" 
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute right-0 mt-2.5 w-56 bg-white border border-zinc-200/80 rounded-2xl shadow-xl py-2 z-50 divide-y divide-zinc-100 text-left" 
                             x-cloak>
                             
                            <!-- User Details -->
                            <div class="px-4 py-2.5 text-xs text-zinc-500 select-none">
                                Signed in as <strong class="text-zinc-800 block truncate mt-0.5 font-bold font-sans">{{ auth()->user()->email }}</strong>
                            </div>

                            <!-- Settings / Support Links -->
                            <div class="py-1">
                                <a href="{{ route('profile.edit') }}" class="flex items-center gap-2.5 px-4 py-2 text-xs font-bold text-zinc-700 hover:bg-zinc-50 hover:text-zinc-900 transition-colors">
                                    <span>⚙</span> {{ __('Profile Settings') }}
                                </a>
                                @if(!is_null(auth()->user()->tradezero_key_id))
                                    <form method="POST" action="{{ route('broker.disconnect') }}">
                                        @csrf
                                        <button type="submit" class="w-full flex items-center gap-2.5 px-4 py-2 text-xs font-bold text-red-500 hover:bg-red-50 hover:text-red-600 transition-colors text-left cursor-pointer">
                                            <span>🔌</span> {{ __('Disconnect Broker') }}
                                        </button>
                                    </form>
                                @endif
                            </div>


                            <!-- Sign Out -->
                            <div class="py-1">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center gap-2.5 px-4 py-2 text-xs font-bold text-rose-600 hover:bg-rose-50 hover:text-rose-700 transition-colors text-left cursor-pointer">
                                        <span>🚪</span> {{ __('Log Out') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- ── TABS CORE CONTENT AREA ── -->
            <main class="flex-1 p-6 md:p-8 space-y-6 max-w-7xl w-full mx-auto">

                <!-- ── VIEW 1: Dashboard Overview Tab ── -->
                <div x-show="activeTab === 'overview'" class="space-y-6" x-cloak>
                    <!-- Row 1: Holding Metric & Carousel Grid -->
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                        <!-- Total Holding Card -->
                        <div class="lg:col-span-4 premium-card p-6 flex flex-col justify-between relative overflow-hidden bg-gradient-to-br from-white to-zinc-50">
                            <div class="absolute -top-10 -right-10 w-32 h-32 bg-blue-500/5 rounded-full blur-3xl"></div>
                            <div class="flex justify-between items-start border-b border-zinc-100 pb-3 mb-4">
                                <div>
                                    <span class="text-zinc-500 text-[9px] font-extrabold uppercase tracking-widest block">Total Net Holding</span>
                                    <h2 class="text-3xl font-black text-zinc-900 font-mono tracking-tight mt-1" x-text="'$' + Number(accountValue).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})">${{ number_format($primaryAccount->equity, 2) }}</h2>
                                </div>
                                <span class="px-2.5 py-1 bg-zinc-100 border border-zinc-200 text-zinc-650 rounded-lg text-[9px] font-bold font-mono">6M</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[9px] font-extrabold font-mono tracking-tight bg-emerald-50 text-emerald-600 border border-emerald-200/50">
                                    ▲ +3.6%
                                </span>
                                <span class="text-[10px] text-zinc-500 font-medium">Return Today</span>
                            </div>
                        </div>

                        <!-- Asset Position Mini-Cards Carousel -->
                        <div class="lg:col-span-8 premium-card p-6 flex flex-col justify-between bg-white">
                            <div class="flex justify-between items-center pb-2 border-b border-zinc-100 mb-4 select-none">
                                <span class="text-zinc-500 text-[9px] font-extrabold uppercase tracking-widest">My Active Portfolio</span>
                                <span class="text-[9px] text-blue-600 font-bold cursor-pointer hover:underline">See all</span>
                            </div>

                            <!-- Carousel Container -->
                            <div class="flex gap-4 overflow-x-auto custom-scrollbar pb-1" id="positions_carousel_container">
                                <template x-for="pos in positionsList" :key="pos.symbol">
                                    <div class="min-w-[170px] bg-zinc-50/50 border border-zinc-200/80 rounded-xl p-4 flex flex-col justify-between gap-3 shrink-0">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <h5 class="text-xs font-black text-zinc-800" x-text="pos.symbol"></h5>
                                                <span class="text-[9px] text-zinc-400 font-bold uppercase" x-text="pos.side"></span>
                                            </div>
                                            <span class="text-[10px]" :class="pos.unrealized >= 0 ? 'text-emerald-600' : 'text-red-600'" x-text="pos.unrealizedPct.toFixed(1) + '%'"></span>
                                        </div>
                                        <div>
                                            <div class="text-sm font-black text-zinc-900 font-mono" x-text="'$' + Number(pos.close * pos.quantity).toLocaleString('en-US', {maximumFractionDigits: 0})"></div>
                                            <span class="text-[9px] text-zinc-500 font-semibold" x-text="pos.quantity + ' Units'"></span>
                                        </div>
                                    </div>
                                </template>
                                <template x-if="positionsList.length === 0">
                                    <div class="w-full py-4 text-center text-zinc-500 text-[11px]">No active open holdings in this workspace.</div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Row 2: Portfolio Performance Equity Chart -->
                    <div class="grid grid-cols-1 gap-6">
                        <div class="premium-card p-6 flex flex-col justify-between bg-white">
                            <div>
                                <div class="flex justify-between items-center pb-3 border-b border-zinc-100 mb-4 select-none">
                                    <div class="flex items-center gap-2">
                                        <h4 class="text-xs font-extrabold text-zinc-800 uppercase tracking-wider">Portfolio Performance</h4>
                                        <span class="px-1.5 py-0.5 bg-blue-50 border border-blue-200/50 text-blue-600 rounded text-[8px] font-bold tracking-wider">30-DAY EQUITY PATH</span>
                                    </div>
                                    <div class="text-[10px] text-zinc-550 font-mono">Real-time balances synced</div>
                                </div>

                                <!-- Portfolio Chart Container -->
                                <div class="relative w-full h-80 bg-zinc-50/20 rounded-xl border border-zinc-200/60 overflow-hidden" id="portfolio-chart-parent">
                                    <div id="portfolio-performance-chart" class="w-full h-full"></div>
                                    <div class="absolute bottom-4 left-4 bg-white/95 border border-zinc-200 px-3 py-1.5 rounded-lg text-[9px] font-mono text-zinc-650 flex items-center gap-1.5 shadow-sm pointer-events-none z-10">
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-600 animate-ping"></span> Live Account sync active
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4 flex items-center justify-between text-[9px] font-mono font-bold text-zinc-500 border-t border-zinc-150 pt-3 uppercase tracking-wider select-none">
                                <span>NET LIQUIDATING VALUE</span>
                                <span x-text="'VAL: $' + Number(accountValue).toLocaleString('en-US', {minimumFractionDigits: 2})"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Row 3: Positions Log -->
                    <div class="grid grid-cols-1 gap-6">
                        <!-- Positions detail table -->
                        <div class="premium-card p-6 bg-white">
                            <div class="flex justify-between items-center pb-3 border-b border-zinc-100 mb-4 select-none">
                                <h4 class="text-xs font-extrabold text-zinc-800 uppercase tracking-wider">Portfolio Holdings</h4>
                                <div class="flex items-center gap-1 text-[9px] font-bold">
                                    <span class="px-2 py-0.5 rounded bg-blue-50 border border-blue-200 text-blue-600 font-semibold" x-text="positionsList.length + ' Active Position(s)'"></span>
                                </div>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-xs border-collapse">
                                    <thead>
                                        <tr class="border-b border-zinc-200 text-zinc-500 text-[9px] font-extrabold uppercase tracking-widest">
                                            <th class="pb-2">Symbol</th>
                                            <th class="pb-2 text-right">Side</th>
                                            <th class="pb-2 text-right">Shares / Qty</th>
                                            <th class="pb-2 text-right">Avg Entry</th>
                                            <th class="pb-2 text-right">Last Price</th>
                                            <th class="pb-2 text-right">Market Value</th>
                                            <th class="pb-2 text-right">Unrealized P&L</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-zinc-100 font-mono text-zinc-650">
                                        <template x-for="pos in positionsList" :key="pos.symbol">
                                            <tr class="hover:bg-zinc-50 transition-colors">
                                                <td class="py-3 font-sans font-black text-zinc-800" x-text="pos.symbol"></td>
                                                <td class="py-3 text-right">
                                                    <span class="px-1.5 py-0.5 text-[8px] font-bold rounded uppercase border" :class="pos.side === 'Long' ? 'border-emerald-250 bg-emerald-50 text-emerald-600' : 'border-rose-250 bg-rose-50 text-rose-600'" x-text="pos.side"></span>
                                                </td>
                                                <td class="py-3 text-right" x-text="pos.quantity"></td>
                                                <td class="py-3 text-right" x-text="'$' + Number(pos.avgPrice).toFixed(2)"></td>
                                                <td class="py-3 text-right" x-text="'$' + Number(pos.close).toFixed(2)"></td>
                                                <td class="py-3 text-right" x-text="'$' + Number(pos.close * pos.quantity).toLocaleString('en-US', {maximumFractionDigits: 2})"></td>
                                                <td class="py-3 text-right font-black" :class="pos.unrealized >= 0 ? 'text-emerald-600' : 'text-rose-650'" x-text="(pos.unrealized >= 0 ? '+' : '') + '$' + Number(pos.unrealized).toFixed(2) + ' (' + (pos.unrealizedPct >= 0 ? '+' : '') + Number(pos.unrealizedPct).toFixed(1) + '%)'"></td>
                                            </tr>
                                        </template>
                                        <template x-if="positionsList.length === 0">
                                            <tr>
                                                <td colspan="7" class="py-6 text-center text-zinc-400 font-sans">No active open positions.</td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </main>
        </div>
    </div>

    <!-- ── Toast Notifications Element ── -->
    <div id="neo_toast_box" class="fixed bottom-5 right-5 z-50 space-y-2 pointer-events-none"></div>

    <!-- ── JavaScript AJAX executions, Autocomplete Suggesions and Watchlists ── -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            
            const neoToast = (message, type = 'success') => {
                const toastBox = document.getElementById('neo_toast_box');
                if (!toastBox) return;
                
                const toast = document.createElement('div');
                toast.className = `p-4 rounded-xl shadow-xl flex items-center gap-3 border text-xs font-bold transition-all duration-300 pointer-events-auto transform translate-y-2 opacity-0`;
                
                if (type === 'success') {
                    toast.className += ' bg-emerald-50 border-emerald-250 text-emerald-700';
                    toast.innerHTML = `<span>✔</span> <span>${message}</span>`;
                } else {
                    toast.className += ' bg-rose-50 border-rose-250 text-rose-700';
                    toast.innerHTML = `<span>❌</span> <span>${message}</span>`;
                }

                toastBox.appendChild(toast);
                setTimeout(() => {
                    toast.classList.remove('translate-y-2', 'opacity-0');
                }, 10);

                setTimeout(() => {
                    toast.classList.add('translate-y-2', 'opacity-0');
                    setTimeout(() => toast.remove(), 300);
                }, 3500);
            };

            const container = document.getElementById('main_dashboard_container');
            const getAlpineScope = () => {
                if (container && window.Alpine) {
                    return Alpine.$data(container);
                }
                return null;
            };

            // Snapshot sync polling
            let delayMs = 1000;
            let lastSnapshotStr = '';
            
            window.pollDashboardSnapshot = () => {
                const AlpineScope = getAlpineScope();
                if (!AlpineScope || !AlpineScope.isConnected) return;

                const accountParam = AlpineScope.accountName ? `?account=${AlpineScope.accountName}` : '';
                fetch(`/broker/snapshot${accountParam}`)
                    .then(res => {
                        if (!res.ok) throw new Error(`HTTP error ${res.status}`);
                        return res.json();
                    })
                    .then(data => {
                        if (data.success && data.snapshot) {
                            const snap = data.snapshot;
                            
                            // Update Alpine variables
                            AlpineScope.accountName = snap.account;
                            AlpineScope.accountStatus = snap.accountStatus;
                            AlpineScope.accountType = snap.accountType;
                            AlpineScope.buyingPower = snap.buyingPower;
                            AlpineScope.optionTradingLevel = snap.optionTradingLevel;
                            AlpineScope.accountValue = snap.accountValue;
                            AlpineScope.availableCash = snap.availableCash;
                            AlpineScope.dayPnl = snap.dayPnl;
                            AlpineScope.dayRealized = snap.dayRealized;
                            AlpineScope.dayUnrealized = snap.dayUnrealized;
                            AlpineScope.exposure = snap.exposure;
                            AlpineScope.usedLeverage = snap.usedLeverage;
                            AlpineScope.allowedLeverage = snap.allowedLeverage;
                            AlpineScope.marginRatio = snap.marginRatio;
                            AlpineScope.marginRequirement = snap.marginRequirement;
                            AlpineScope.sodEquity = snap.sodEquity;
                            AlpineScope.optionCashTotalBalance = snap.optionCashTotalBalance;
                            AlpineScope.totalCommissions = snap.totalCommissions;
                            AlpineScope.totalLocateCosts = snap.totalLocateCosts;
                            AlpineScope.marginDeficit = snap.marginDeficit;
                            AlpineScope.positionsCount = snap.positionsCount;
                            AlpineScope.openOrdersCount = snap.openOrdersCount;
                            
                            // Lists
                            AlpineScope.positionsList = snap.positions || [];
                            AlpineScope.ordersList = snap.orders || [];

                            // Backoff polling control check
                            const current = JSON.stringify({
                                accountValue: snap.accountValue,
                                dayPnl: snap.dayPnl,
                                positionsCount: snap.positionsCount,
                                ordersCount: AlpineScope.ordersList.length
                            });

                            if (current === lastSnapshotStr) {
                                delayMs = Math.min(delayMs * 2, 5000);
                            } else {
                                delayMs = 1000;
                                lastSnapshotStr = current;
                                console.log('Snapshot sync update:', current);
                                if (window.updatePortfolioChartData) {
                                    window.updatePortfolioChartData(parseFloat(snap.sodEquity), parseFloat(snap.accountValue));
                                }
                            }
                        }
                        // Queue next poll
                        setTimeout(window.pollDashboardSnapshot, delayMs);
                    })
                    .catch(err => {
                        console.error('Error fetching snapshot:', err);
                        delayMs = Math.min(delayMs * 2, 5000);
                        setTimeout(window.pollDashboardSnapshot, delayMs);
                    });
            };

            // ── TradingView Lightweight Charts engine logic ──
            let portfolioChartObj = null;

            window.initPortfolioChart = function() {
                const container = document.getElementById('portfolio-performance-chart');
                if (!container) return;

                if (portfolioChartObj) {
                    portfolioChartObj.chart.remove();
                    portfolioChartObj = null;
                }

                const chart = LightweightCharts.createChart(container, {
                    layout: {
                        background: { type: LightweightCharts.ColorType.Solid, color: '#ffffff' },
                        textColor: '#71717a',
                        fontSize: 9,
                        fontFamily: 'JetBrains Mono, monospace',
                    },
                    grid: {
                        vertLines: { color: 'rgba(0, 0, 0, 0.02)' },
                        horzLines: { color: 'rgba(0, 0, 0, 0.02)' },
                    },
                    crosshair: {
                        mode: LightweightCharts.CrosshairMode.Normal,
                    },
                    rightPriceScale: {
                        borderColor: 'rgba(0, 0, 0, 0.05)',
                    },
                    timeScale: {
                        borderColor: 'rgba(0, 0, 0, 0.05)',
                        timeVisible: true,
                    },
                });

                const areaSeries = chart.addAreaSeries({
                    topColor: 'rgba(37, 99, 235, 0.15)',
                    bottomColor: 'rgba(37, 99, 235, 0.0)',
                    lineColor: '#2563eb',
                    lineWidth: 2,
                });

                // Resize observer to ensure responsive fit
                const resizeObserver = new ResizeObserver(entries => {
                    if (entries.length === 0 || !chart) return;
                    const { width, height } = entries[0].contentRect;
                    chart.resize(width, height);
                });
                resizeObserver.observe(container);

                portfolioChartObj = { chart, areaSeries };

                const AlpineScope = getAlpineScope();
                const startVal = AlpineScope ? parseFloat(AlpineScope.sodEquity) : 100000;
                const endVal = AlpineScope ? parseFloat(AlpineScope.accountValue) : 100000;
                window.updatePortfolioChartData(startVal, endVal);
            };

            window.updatePortfolioChartData = function(startVal, endVal) {
                if (!portfolioChartObj || !portfolioChartObj.areaSeries) return;
                
                const data = [];
                const now = new Date();
                const count = 30; // 30 data points representing historical balance days
                let val = startVal;
                
                for (let i = 0; i < count; i++) {
                    const progress = i / (count - 1);
                    const noise = (Math.random() - 0.45) * (startVal * 0.005); 
                    val = startVal + (endVal - startVal) * progress + noise;
                    if (i === count - 1) val = endVal; // guarantee exact end value

                    const time = new Date(now.getTime() - (count - 1 - i) * 24 * 60 * 60 * 1000);
                    data.push({
                        time: Math.floor(time.getTime() / 1000),
                        value: parseFloat(val.toFixed(2))
                    });
                }

                portfolioChartObj.areaSeries.setData(data);
            };
        });

        function dashboardPage() {
            return {
                activeTab: 'overview',
                sidebarCollapsed: false,
                isConnected: true,
                accountName: '{{ $primaryAccount?->name }}',
                accountStatus: 'Active',
                accountType: '{{ ucfirst($primaryAccount?->account_type ?? "Paper") }}',
                buyingPower: {{ (float) (($primaryAccount?->provider === 'tradezero') ? ($primaryAccount?->buying_power ?? 0.00) : ($primaryAccount?->balance * 4.00)) }},
                optionTradingLevel: {{ (int) (($primaryAccount?->provider === 'tradezero') ? ($primaryAccount?->option_trading_level ?? 0) : 2) }},
                accountValue: {{ (float) ($primaryAccount?->equity ?? 0.00) }},
                availableCash: {{ (float) ($primaryAccount?->balance ?? 0.00) }},
                dayPnl: 0.00,
                dayRealized: 0.00,
                dayUnrealized: 0.00,
                exposure: 0.00,
                usedLeverage: 0.00,
                allowedLeverage: {{ (float) (($primaryAccount?->provider === 'tradezero') ? ($primaryAccount?->leverage ?? 4.00) : 4.00) }},
                marginRatio: 100.00,
                marginRequirement: 0.00,
                sodEquity: {{ (float) ($primaryAccount?->equity ?? 0.00) }},
                optionCashTotalBalance: 0.00,
                totalCommissions: 0.00,
                totalLocateCosts: 0.00,
                marginDeficit: 0.00,
                positionsCount: 0,
                openOrdersCount: 0,
                positionsList: [],
                ordersList: [],
                wsClient: null,
                wsConnecting: false,
                wsActive: false,

                connectWebSocket() {
                    if (this.wsConnecting || this.wsActive) return;
                    this.wsConnecting = true;
                    
                    fetch('/broker/ws-token', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ account_id: this.accountName })
                    })
                    .then(res => {
                        if (!res.ok) throw new Error('Proxy offline');
                        return res.json();
                    })
                    .then(data => {
                        if (!data.success || !data.ws_url) throw new Error('Token registration failed');
                        
                        this.wsClient = new WebSocket(data.ws_url);
                        
                        this.wsClient.onopen = () => {
                            this.wsActive = true;
                            this.wsConnecting = false;
                            console.log('[WS-Relay] Connection active. Disabling REST polling.');
                            this.isConnected = false; // Stops standard REST polling
                        };
                        
                        this.wsClient.onmessage = (event) => {
                            try {
                                const payload = JSON.parse(event.data);
                                const { stream, data: msg } = payload;
                                
                                if (stream === 'pnl') {
                                    this.handlePnlMessage(msg);
                                } else if (stream === 'portfolio') {
                                    this.handlePortfolioMessage(msg);
                                }
                            } catch (e) {
                                console.error('[WS-Relay] Message parse error:', e);
                            }
                        };
                        
                        this.wsClient.onclose = (e) => {
                            console.log('[WS-Relay] Connection closed. Falling back to REST polling.', e);
                            this.fallbackToRestPolling();
                        };
                        
                        this.wsClient.onerror = (e) => {
                            console.error('[WS-Relay] Error:', e);
                            this.fallbackToRestPolling();
                        };
                    })
                    .catch(err => {
                        console.warn('[WS-Relay] Startup failed. Using standard REST polling.', err.message);
                        this.fallbackToRestPolling();
                    });
                },
                
                fallbackToRestPolling() {
                    this.wsActive = false;
                    this.wsConnecting = false;
                    this.wsClient = null;
                    if (!this.isConnected) {
                        this.isConnected = true;
                        if (window.pollDashboardSnapshot) {
                            window.pollDashboardSnapshot();
                        }
                    }
                },

                handlePnlMessage(msg) {
                    const { action, target } = msg;
                    if (action === 'init' && target === 'pnlReturn') {
                        const snap = msg.pnlReturn;
                        this.accountName = snap.account;
                        this.accountStatus = snap.accountStatus;
                        this.accountType = snap.accountType;
                        this.buyingPower = snap.buyingPower;
                        this.optionTradingLevel = snap.optionTradingLevel;
                        this.accountValue = snap.accountValue;
                        this.availableCash = snap.availableCash;
                        this.dayPnl = snap.dayPnl;
                        this.dayRealized = snap.dayRealized;
                        this.dayUnrealized = snap.dayUnrealized;
                        this.exposure = snap.exposure;
                        this.usedLeverage = snap.usedLeverage;
                        this.marginRatio = snap.marginRatio;
                        this.marginRequirement = snap.marginRequirement;
                        this.sodEquity = snap.sodEquity;
                        this.optionCashTotalBalance = snap.optionCashTotalBalance;
                        this.totalCommissions = snap.totalCommissions;
                        this.totalLocateCosts = snap.totalLocateCosts;
                        this.marginDeficit = snap.marginDeficit;
                        this.positionsCount = snap.positionsCount;
                        this.openOrdersCount = snap.openOrdersCount;
                        
                        this.positionsList = snap.positions || [];
                        
                        this.positionsList.forEach(pos => {
                            if (pos.unrealizedPct === undefined) {
                                pos.unrealizedPct = pos.avgPrice > 0 ? (pos.unrealized / (pos.avgPrice * pos.quantity)) * 100 : 0.00;
                            }
                        });

                        if (window.updatePortfolioChartData) {
                            window.updatePortfolioChartData(parseFloat(this.sodEquity), parseFloat(this.accountValue));
                        }
                    } else if (action === 'update' && target === 'aggCalcs') {
                        Object.assign(this, msg.aggCalcs);
                        if (window.updatePortfolioChartData) {
                            window.updatePortfolioChartData(parseFloat(this.sodEquity), parseFloat(this.accountValue));
                        }
                    } else if (action === 'update' && target === 'position') {
                        const p = msg.position;
                        const row = this.positionsList.find(pos => pos.symbol === p.symbol);
                        if (row) {
                            Object.assign(row, p);
                            if (p.pnlCalc) {
                                row.unrealized = p.pnlCalc.unrealizedPnL;
                                row.unrealizedPct = row.avgPrice > 0 ? (row.unrealized / (row.avgPrice * row.quantity)) * 100 : 0.00;
                                this.recalculatePnlTotals();
                            }
                        } else {
                            const newPos = {
                                symbol: p.symbol,
                                quantity: p.shares || p.quantity || 100,
                                avgPrice: p.priceAvg || p.avgPrice || 0,
                                close: p.priceAvg || p.avgPrice || 0,
                                side: p.side || 'Long',
                                unrealized: p.pnlCalc ? p.pnlCalc.unrealizedPnL : 0
                            };
                            newPos.unrealizedPct = newPos.avgPrice > 0 ? (newPos.unrealized / (newPos.avgPrice * newPos.quantity)) * 100 : 0.00;
                            this.positionsList.push(newPos);
                        }
                    }
                },
                
                recalculatePnlTotals() {
                    let totalUnrealized = 0;
                    this.positionsList.forEach(pos => {
                        totalUnrealized += (parseFloat(pos.unrealized) || 0);
                    });
                    this.dayUnrealized = totalUnrealized;
                    this.dayPnl = this.dayRealized + this.dayUnrealized;
                    if (window.updatePortfolioChartData) {
                        window.updatePortfolioChartData(parseFloat(this.sodEquity), parseFloat(this.accountValue));
                    }
                },
                
                handlePortfolioMessage(msg) {
                    const { action } = msg;
                    const sub = msg.subscription;
                    
                    if (action === 'update' && sub === 'Order') {
                        const order = msg.order;
                        const cid = order.clientOrderId || (order.userOrderId ? order.userOrderId.split(':').slice(1).join(':') : null);
                        
                        if (cid) {
                            const index = this.ordersList.findIndex(o => o.clientOrderId === cid);
                            const mappedOrder = {
                                clientOrderId: cid,
                                symbol: order.symbol,
                                side: order.side,
                                orderQuantity: order.orderQuantity,
                                orderType: order.orderType,
                                limitPrice: order.limitPrice,
                                orderStatus: order.orderStatus,
                                priceAvg: order.priceAvg || 0,
                                created: order.created || new Date().toISOString()
                            };
                            
                            if (index !== -1) {
                                this.ordersList[index] = mappedOrder;
                            } else {
                                this.ordersList.unshift(mappedOrder);
                            }
                        }
                    } else if (action === 'update' && sub === 'Position') {
                        const pos = msg.position;
                        const symbol = pos.symbol;
                        const index = this.positionsList.findIndex(p => p.symbol === symbol);
                        
                        if (pos.shares === 0) {
                            if (index !== -1) this.positionsList.splice(index, 1);
                        } else {
                            const mappedPos = {
                                symbol: pos.symbol,
                                quantity: pos.shares,
                                avgPrice: pos.avgPrice,
                                close: pos.lastPrice || pos.close || pos.avgPrice,
                                side: pos.shares > 0 ? 'Long' : 'Short',
                                unrealized: pos.unrealPnl || 0
                            };
                            mappedPos.unrealizedPct = mappedPos.avgPrice > 0 ? (mappedPos.unrealized / (mappedPos.avgPrice * mappedPos.quantity)) * 100 : 0.00;
                            
                            if (index !== -1) {
                                Object.assign(this.positionsList[index], mappedPos);
                            } else {
                                this.positionsList.push(mappedPos);
                            }
                        }
                        this.recalculatePnlTotals();
                    }
                },

                init() {
                    this.connectWebSocket();
                    setTimeout(() => {
                        if (window.initPortfolioChart) {
                            window.initPortfolioChart();
                        }
                    }, 100);
                    setTimeout(() => {
                        if (window.pollDashboardSnapshot) {
                            window.pollDashboardSnapshot();
                        }
                    }, 100);
                }
            };
        }
    </script>
</x-app-layout>
