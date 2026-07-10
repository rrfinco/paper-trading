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

        $headerAccounts = auth()->user()->accounts;
        $activeAccountId = request('account');
        
        // Find active account
        $primaryAccount = null;
        if ($activeAccountId) {
            $primaryAccount = $headerAccounts->firstWhere('id', $activeAccountId) ?? $headerAccounts->firstWhere('name', $activeAccountId);
        }
        if (!$primaryAccount) {
            if (auth()->user()->tradezero_account_id) {
                $primaryAccount = $headerAccounts->firstWhere('name', auth()->user()->tradezero_account_id);
            }
            if (!$primaryAccount) {
                $primaryAccount = $headerAccounts->first();
            }
        }

        $tzAccounts = $user->tradeZeroAccounts;
        $isConnected = !is_null($user->tradezero_key_id) && $tzAccounts->isNotEmpty();
    @endphp



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

    <div x-data="{ sidebarCollapsed: false }" class="min-h-screen bg-[#f4f5f8] text-zinc-800 flex antialiased">

        <!-- ── LEFT SIDEBAR PANEL (Stovest Style) ── -->
        <aside :class="sidebarCollapsed ? 'w-20 px-3 py-6' : 'w-64 p-6'" class="bg-white border-r border-zinc-200 flex flex-col justify-between shrink-0 z-20 shadow-sm transition-all duration-300 ease-in-out">
            <div class="space-y-8">
                <!-- Branding -->
                <div class="flex items-center" :class="sidebarCollapsed ? 'justify-center' : 'gap-2'">
                    <div class="h-9 w-9 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-700 flex items-center justify-center shadow-lg shadow-blue-600/20 shrink-0">
                        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                    </div>
                    <span x-show="!sidebarCollapsed" x-transition class="text-xl font-black tracking-tight text-zinc-900 select-none">Paper<span class="text-blue-600">Trading</span></span>
                </div>

                <!-- Navigation Tabs -->
                <nav class="space-y-1.5">
                    <p x-show="!sidebarCollapsed" x-transition class="text-zinc-400 text-[10px] font-bold uppercase tracking-wider mb-2 select-none">Main Menu</p>
                    <a href="{{ route('dashboard') }}" class="w-full flex items-center rounded-xl text-xs font-bold tracking-wide transition-all text-zinc-650 hover:bg-zinc-100 hover:text-zinc-900" :class="sidebarCollapsed ? 'justify-center py-3 px-0' : 'gap-3.5 px-4 py-3'">
                        <span class="text-lg">📊</span>
                        <span x-show="!sidebarCollapsed" x-transition>Dashboard</span>
                    </a>
                    <a href="{{ route('trade') }}" class="w-full flex items-center rounded-xl text-xs font-bold tracking-wide transition-all text-zinc-650 hover:bg-zinc-100 hover:text-zinc-900" :class="sidebarCollapsed ? 'justify-center py-3 px-0' : 'gap-3.5 px-4 py-3'">
                        <span class="text-lg">📈</span>
                        <span x-show="!sidebarCollapsed" x-transition>Trade Entry</span>
                    </a>
                    <a href="{{ route('locates') }}" class="w-full flex items-center rounded-xl text-xs font-bold tracking-wide transition-all text-zinc-650 hover:bg-zinc-100 hover:text-zinc-900" :class="sidebarCollapsed ? 'justify-center py-3 px-0' : 'gap-3.5 px-4 py-3'">
                        <span class="text-lg">🔑</span>
                        <span x-show="!sidebarCollapsed" x-transition>Locates Manager</span>
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
                    <button @click="sidebarCollapsed = !sidebarCollapsed" class="p-2 hover:bg-zinc-100 text-zinc-650 hover:text-zinc-900 border border-zinc-200/80 rounded-xl transition-colors cursor-pointer shadow-sm select-none" title="Toggle Sidebar">
                        <svg class="w-4 h-4 transition-transform duration-300" :class="sidebarCollapsed ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
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
                            @foreach($headerAccounts as $acc)
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
                                <span class="text-[10px] text-zinc-550 block leading-none mt-0.5">{{ auth()->user()->email }}</span>
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

            <main class="flex-1 p-6 md:p-8 space-y-8 max-w-7xl w-full mx-auto">
                <!-- Page Header -->
                <div class="space-y-1 select-none mb-6">
                    <p class="text-zinc-400 text-[10px] font-bold uppercase tracking-wider">Settings</p>
                    <h1 class="text-2xl font-black text-zinc-900">Account & Profile Settings</h1>
                    <p class="text-xs text-zinc-550 leading-normal">Manage user preferences and review linked TradeZero brokerage metrics.</p>
                </div>
            
            <!-- SECTION 1: TradeZero API connection snapshot -->
            <div class="p-6 sm:p-8 bg-white border border-zinc-200/80 shadow-sm sm:rounded-2xl">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between border-b border-zinc-200/80 pb-5 mb-6 gap-4">
                    <div>
                        <h3 class="text-lg font-bold text-zinc-900 flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                            </svg>
                            {{ __('TradeZero Brokerage Integration') }}
                        </h3>
                        <p class="text-xs text-zinc-500 mt-1">Review the API keys linked to your account profile and associated balances.</p>
                    </div>
                    
                    @if($isConnected)
                        <div class="flex items-center gap-2 bg-emerald-50 border border-emerald-250 text-emerald-700 px-3.5 py-1.5 rounded-xl text-xs font-semibold">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            Verified & Connected
                        </div>
                    @else
                        <div class="flex items-center gap-2 bg-zinc-100 border border-zinc-250 text-zinc-550 px-3.5 py-1.5 rounded-xl text-xs font-semibold">
                            <span class="w-2 h-2 rounded-full bg-zinc-400"></span>
                            Disconnected
                        </div>
                    @endif
                </div>

                @if($isConnected)
                    <div class="space-y-6">
                        <!-- Key Information Panel -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-5 rounded-xl border border-zinc-200 bg-zinc-50/50 text-xs">
                            <div>
                                <span class="text-zinc-500 block mb-1 font-bold">TZ-API-KEY-ID</span>
                                <code class="font-mono text-zinc-700 font-bold bg-white px-2 py-1.5 rounded border border-zinc-200 block truncate">
                                    {{ $user->tradezero_key_id }}
                                </code>
                            </div>
                            <div>
                                <span class="text-zinc-500 block mb-1 font-bold">TZ-API-SECRET-KEY</span>
                                <code class="font-mono text-zinc-400 bg-white px-2 py-1.5 rounded border border-zinc-200 block truncate">
                                    ••••••••••••••••••••••••••••••••••••••••
                                </code>
                            </div>
                        </div>

                        <!-- Discovered Account snap views -->
                        <h4 class="text-xs font-extrabold text-zinc-500 uppercase tracking-wider mb-3">Discovered Accounts ({{ $tzAccounts->count() }})</h4>
                        
                        <div class="space-y-6">
                            @foreach($tzAccounts as $acc)
                                @php
                                    $isLive = str_contains(strtolower($acc->account), 'live') || strtolower($acc->account_type) === 'live';
                                @endphp
                                <div class="border border-zinc-200 hover:border-blue-300 transition-all duration-350 rounded-xl bg-white p-6 shadow-sm">
                                    <div class="flex items-center justify-between border-b border-zinc-200 pb-4 mb-4">
                                        <div class="flex items-center gap-3">
                                            <span class="text-base font-extrabold text-zinc-900 tracking-tight">{{ $acc->account }}</span>
                                            <span class="px-2 py-0.5 text-[9px] font-bold uppercase rounded border border-blue-200 bg-blue-50 text-blue-600">
                                                {{ $isLive ? 'Live Account' : 'Paper Trading' }}
                                            </span>
                                            <span class="px-2 py-0.5 text-[9px] font-bold uppercase rounded border border-emerald-250 bg-emerald-50 text-emerald-600">
                                                {{ $acc->account_status }}
                                            </span>
                                        </div>
                                        <div class="text-[10px] text-zinc-400">Sourced via Developer API</div>
                                    </div>
                                    
                                    <!-- Main Metrics Grid -->
                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-6">
                                        <div class="p-4 rounded-xl bg-zinc-50/50 border border-zinc-200">
                                            <span class="text-[10px] text-zinc-500 uppercase font-bold tracking-wider">Net Equity</span>
                                            <div class="text-xl font-extrabold text-zinc-900 mt-1">${{ number_format($acc->equity ?? 0.00, 2) }}</div>
                                        </div>
                                        <div class="p-4 rounded-xl bg-zinc-50/50 border border-zinc-200">
                                            <span class="text-[10px] text-zinc-500 uppercase font-bold tracking-wider">Available Cash</span>
                                            <div class="text-xl font-extrabold text-zinc-900 mt-1">${{ number_format($acc->available_cash ?? 0.00, 2) }}</div>
                                        </div>
                                        <div class="p-4 rounded-xl bg-zinc-50/50 border border-zinc-200">
                                            <span class="text-[10px] text-zinc-500 uppercase font-bold tracking-wider">Buying Power</span>
                                            <div class="text-xl font-extrabold text-blue-600 mt-1">${{ number_format($acc->buying_power ?? 0.00, 2) }}</div>
                                        </div>
                                    </div>

                                    <!-- Account parameters parameters subgrid -->
                                    <div class="border-t border-zinc-200 pt-4">
                                        <span class="text-[10px] text-zinc-500 uppercase font-bold tracking-wider block mb-3">Detailed Metrics & Limits</span>
                                        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 text-xs">
                                            <div class="bg-zinc-50/50 p-3 rounded-lg border border-zinc-200">
                                                <span class="text-zinc-500 block mb-0.5 text-[9px] font-bold uppercase">Realized P&L</span>
                                                @php $realized = $acc->realized ?? 0.00; @endphp
                                                <span class="font-bold {{ $realized >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                                    {{ $realized >= 0 ? '+' : '' }}${{ number_format($realized, 2) }}
                                                </span>
                                            </div>
                                            <div class="bg-zinc-50/50 p-3 rounded-lg border border-zinc-200">
                                                <span class="text-zinc-500 block mb-0.5 text-[9px] font-bold uppercase">Unrealized P&L</span>
                                                @php $unrealized = $acc->unrealized ?? 0.00; @endphp
                                                <span class="font-bold {{ $unrealized >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                                    {{ $unrealized >= 0 ? '+' : '' }}${{ number_format($unrealized, 2) }}
                                                </span>
                                            </div>
                                            <div class="bg-zinc-50/50 p-3 rounded-lg border border-zinc-200">
                                                <span class="text-zinc-500 block mb-0.5 text-[9px] font-bold uppercase">Margin Ratio</span>
                                                <span class="font-bold text-zinc-800">{{ number_format($acc->margin_ratio ?? 0.00, 2) }}%</span>
                                            </div>
                                            <div class="bg-zinc-50/50 p-3 rounded-lg border border-zinc-200">
                                                <span class="text-zinc-500 block mb-0.5 text-[9px] font-bold uppercase">Margin Req</span>
                                                <span class="font-bold text-zinc-800">${{ number_format($acc->margin_requirement ?? 0.00, 2) }}</span>
                                            </div>
                                            <div class="bg-zinc-50/50 p-3 rounded-lg border border-zinc-200">
                                                <span class="text-zinc-500 block mb-0.5 text-[9px] font-bold uppercase">Leverage Multiplier</span>
                                                <span class="font-bold text-zinc-800">{{ $acc->leverage ?? 0.00 }}x</span>
                                            </div>
                                            <div class="bg-zinc-50/50 p-3 rounded-lg border border-zinc-200">
                                                <span class="text-zinc-500 block mb-0.5 text-[9px] font-bold uppercase">Used Leverage</span>
                                                <span class="font-bold text-zinc-800">{{ $acc->used_leverage ?? 0.00 }}x</span>
                                            </div>
                                            <div class="bg-zinc-50/50 p-3 rounded-lg border border-zinc-200">
                                                <span class="text-zinc-500 block mb-0.5 text-[9px] font-bold uppercase">Overnight BP</span>
                                                <span class="font-bold text-zinc-800">${{ number_format($acc->overnight_bp ?? 0.00, 2) }}</span>
                                            </div>
                                            <div class="bg-zinc-50/50 p-3 rounded-lg border border-zinc-200">
                                                <span class="text-zinc-500 block mb-0.5 text-[9px] font-bold uppercase">SOD Net Equity</span>
                                                <span class="font-bold text-zinc-800">${{ number_format($acc->sod_equity ?? 0.00, 2) }}</span>
                                            </div>
                                            <div class="bg-zinc-50/50 p-3 rounded-lg border border-zinc-200">
                                                <span class="text-zinc-500 block mb-0.5 text-[9px] font-bold uppercase">Maint Deficit</span>
                                                <span class="font-bold text-rose-600">${{ number_format($acc->maintenance_deficit ?? 0.00, 2) }}</span>
                                            </div>
                                            <div class="bg-zinc-50/50 p-3 rounded-lg border border-zinc-200">
                                                <span class="text-zinc-500 block mb-0.5 text-[9px] font-bold uppercase">Option Level</span>
                                                <span class="font-bold text-blue-600">Level {{ $acc->option_trading_level ?? 0 }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="p-8 border border-dashed border-zinc-300 rounded-xl bg-zinc-50/50 text-center flex flex-col items-center justify-center">
                        <svg class="w-12 h-12 text-zinc-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h4 class="text-sm font-bold text-zinc-800 mb-1">No TradeZero Accounts Connected</h4>
                        <p class="text-xs text-zinc-550 max-w-sm mb-5">Link your API keys to authorize real-time balances, portfolio snapshots, and options trading verification.</p>
                        
                        <a href="{{ route('dashboard') }}" class="px-5 py-2.5 text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 rounded-xl transition-all shadow-md flex items-center justify-center gap-2">
                            Go to Dashboard to Connect
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </a>
                    </div>
                @endif
            </div>

            <!-- SECTION 2: Profile information -->
            <div class="p-6 sm:p-8 bg-white border border-zinc-200/80 shadow-sm sm:rounded-2xl">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <!-- SECTION 3: Update Password -->
            <div class="p-6 sm:p-8 bg-white border border-zinc-200/80 shadow-sm sm:rounded-2xl">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <!-- SECTION 4: Danger Zone -->
            <div class="p-6 sm:p-8 bg-white border border-zinc-200/80 shadow-sm sm:rounded-2xl">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
            </main>
        </div>
    </div>
</x-app-layout>