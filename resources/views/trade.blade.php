<x-app-layout>
    @php
        $accounts = auth()->user()->accounts;
        $activeAccountId = request('account');
        
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
    @endphp

    <!-- TradeZero Dark Terminal Layout Style System -->
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap');

        :root {
            --tz-bg-main: #0c0c0e;
            --tz-bg-panel: #16161a;
            --tz-bg-input: #202026;
            --tz-border: #282830;
            --tz-green: #10b981;
            --tz-red: #ef4444;
            --tz-blue: #2563eb;
            --font-mono: 'JetBrains Mono', monospace;
        }

        body {
            background-color: var(--tz-bg-main);
            color: #e4e4e7;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .tz-panel {
            background-color: var(--tz-bg-panel);
            border: 1px solid var(--tz-border);
            border-radius: 0.75rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.2), 0 2px 4px -1px rgba(0, 0, 0, 0.1);
        }

        .tz-border-b {
            border-bottom: 1px solid var(--tz-border);
        }

        .tz-font-mono {
            font-family: var(--font-mono);
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
            height: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.02);
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 2px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: var(--tz-blue);
        }
    </style>

    <div x-data="tradePage()" id="main_trade_container" class="min-h-screen bg-[#0c0c0e] text-zinc-100 flex antialiased">
        
        <!-- ── LEFT NAVIGATION SIDEBAR ── -->
        <aside :class="sidebarCollapsed ? 'w-20 px-3 py-6' : 'w-64 p-6'" class="bg-[#121216] border-r border-zinc-800 flex flex-col justify-between shrink-0 z-20 transition-all duration-300">
            <div class="space-y-8">
                <!-- Brand logo -->
                <div class="flex items-center" :class="sidebarCollapsed ? 'justify-center' : 'gap-2'">
                    <div class="h-9 w-9 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-700 flex items-center justify-center shadow-lg shadow-blue-600/20 shrink-0">
                        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                    </div>
                    <span x-show="!sidebarCollapsed" class="text-lg font-black tracking-tight text-white select-none">Paper<span class="text-blue-500">Trading</span></span>
                </div>

                <!-- Navigation Tabs -->
                <nav class="space-y-1.5">
                    <p x-show="!sidebarCollapsed" class="text-zinc-500 text-[10px] font-extrabold uppercase tracking-wider mb-2 select-none">Navigation</p>
                    
                    <a href="{{ route('dashboard') }}" class="w-full flex items-center rounded-xl text-xs font-bold tracking-wide text-zinc-400 hover:text-white hover:bg-zinc-800/50" :class="sidebarCollapsed ? 'justify-center py-3 px-0' : 'gap-3.5 px-4 py-3'">
                        <span class="text-lg">📊</span>
                        <span x-show="!sidebarCollapsed">Dashboard</span>
                    </a>

                    <button class="w-full flex items-center rounded-xl text-xs font-bold tracking-wide bg-blue-600/10 text-blue-400 border-l-3 border-blue-600" :class="sidebarCollapsed ? 'justify-center py-3 px-0' : 'gap-3.5 px-4 py-3'">
                        <span class="text-lg">📈</span>
                        <span x-show="!sidebarCollapsed">Trade Entry</span>
                    </button>

                    <a href="{{ route('locates') }}" class="w-full flex items-center rounded-xl text-xs font-bold tracking-wide text-zinc-400 hover:text-white hover:bg-zinc-800/50" :class="sidebarCollapsed ? 'justify-center py-3 px-0' : 'gap-3.5 px-4 py-3'">
                        <span class="text-lg">🔑</span>
                        <span x-show="!sidebarCollapsed">Locates Manager</span>
                    </a>
                </nav>
            </div>

            <!-- Profile & Disconnect -->
            <div class="border-t border-zinc-800/80 pt-4 space-y-2">
                <a href="{{ route('profile.edit') }}" class="w-full flex items-center rounded-xl text-xs font-bold text-zinc-400 hover:text-white" :class="sidebarCollapsed ? 'justify-center p-2' : 'gap-3 px-4 py-2.5'">
                    <span class="text-sm">👤</span>
                    <span x-show="!sidebarCollapsed">Profile Settings</span>
                </a>
                
                @if(auth()->user()->tradezero_account_id)
                    <form method="POST" action="{{ route('broker.disconnect') }}" class="w-full">
                        @csrf
                        <button type="submit" class="w-full flex items-center rounded-xl text-xs font-bold text-rose-400 hover:text-rose-300 hover:bg-rose-950/20" :class="sidebarCollapsed ? 'justify-center p-2' : 'gap-3 px-4 py-2.5'">
                            <span class="text-sm">🔌</span>
                            <span x-show="!sidebarCollapsed">Disconnect Broker</span>
                        </button>
                    </form>
                @endif

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <button type="submit" class="w-full flex items-center rounded-xl text-xs font-bold text-zinc-500 hover:text-zinc-300" :class="sidebarCollapsed ? 'justify-center p-2' : 'gap-3 px-4 py-2.5'">
                        <span class="text-sm">🚪</span>
                        <span x-show="!sidebarCollapsed">Logout</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- ── MAIN BODY CONTENT CONTAINER ── -->
        <div class="flex-1 flex flex-col min-w-0 overflow-y-auto">

            <!-- ── TRADEZERO TOP ACCOUNT METRICS HEADER ── -->
            <header class="bg-[#0f0f12] border-b border-zinc-800 px-6 py-4 flex flex-wrap items-center justify-between gap-6 shrink-0 sticky top-0 z-30">
                <div class="flex items-center gap-4">
                    <button @click="sidebarCollapsed = !sidebarCollapsed" class="p-2 hover:bg-zinc-800/80 text-zinc-400 hover:text-white border border-zinc-800 rounded-xl cursor-pointer shadow-sm select-none" title="Toggle Sidebar">
                        <svg class="w-4 h-4" :class="sidebarCollapsed ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                    <div class="flex items-center gap-2">
                        <span class="text-[10px] font-extrabold text-zinc-500 uppercase tracking-wider">Account Preferences</span>
                        <select onchange="window.location.search = 'account=' + this.value" class="bg-[#1c1c22] border border-zinc-800 text-zinc-100 rounded-xl text-xs font-bold px-3 py-1.5 focus:outline-none cursor-pointer hover:border-zinc-700 shadow-inner">
                            @foreach($accounts as $acc)
                                <option value="{{ $acc->name }}" {{ $acc->id === $primaryAccount->id ? 'selected' : '' }}>
                                    {{ $acc->name }} ({{ strtoupper($acc->provider) }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Live TradeZero Metrics Panel -->
                <div class="flex flex-wrap items-center gap-x-6 gap-y-2 text-[11px] font-semibold text-zinc-400">
                    <div>
                        <div class="text-[9px] uppercase tracking-wider text-zinc-500 select-none">Total Unrealized</div>
                        <div class="font-extrabold text-emerald-500 tz-font-mono" :class="dayUnrealized < 0 ? 'text-rose-500' : 'text-emerald-500'" x-text="'$' + Number(dayUnrealized).toFixed(2)">$0.00</div>
                    </div>
                    <div>
                        <div class="text-[9px] uppercase tracking-wider text-zinc-500 select-none">Day Realized</div>
                        <div class="font-extrabold text-emerald-500 tz-font-mono" :class="dayRealized < 0 ? 'text-rose-500' : 'text-emerald-500'" x-text="'$' + Number(dayRealized).toFixed(2)">$0.00</div>
                    </div>
                    <div>
                        <div class="text-[9px] uppercase tracking-wider text-zinc-500 select-none">Day Total</div>
                        <div class="font-extrabold tz-font-mono" :class="(dayRealized + dayUnrealized) < 0 ? 'text-rose-500' : 'text-emerald-500'" x-text="'$' + Number(dayRealized + dayUnrealized).toFixed(2)">$0.00</div>
                    </div>
                    <div>
                        <div class="text-[9px] uppercase tracking-wider text-zinc-500 select-none">Buying Power</div>
                        <div class="font-extrabold text-white tz-font-mono" x-text="'$' + Number(buyingPower).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})">$0.00</div>
                    </div>
                    <div>
                        <div class="text-[9px] uppercase tracking-wider text-zinc-500 select-none">Cash Balance</div>
                        <div class="font-extrabold text-white tz-font-mono" x-text="'$' + Number(availableCash).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})">$0.00</div>
                    </div>
                    <div>
                        <div class="text-[9px] uppercase tracking-wider text-zinc-500 select-none">Account Value</div>
                        <div class="font-extrabold text-white tz-font-mono" x-text="'$' + Number(accountValue).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})">$0.00</div>
                    </div>
                    <div>
                        <div class="text-[9px] uppercase tracking-wider text-zinc-500 select-none">Locate Costs</div>
                        <div class="font-extrabold text-amber-500 tz-font-mono" x-text="'$' + Number(totalLocateCosts).toFixed(2)">$0.00</div>
                    </div>
                </div>
            </header>

            <!-- Warning Banner for Paper Trading -->
            <template x-if="accountType === 'Paper'">
                <div class="bg-amber-600/90 text-white font-extrabold text-xs py-1.5 px-6 tracking-wide uppercase text-center shadow select-none">
                    ⚠️ This is a Paper Trading version of the application. All executions are simulated.
                </div>
            </template>

            <!-- ── HORIZONTAL ORDER TICKET MODULE ── -->
            <section class="bg-[#151519] border-b border-zinc-800 p-4 shrink-0 shadow-inner flex flex-col gap-3.5 select-none">
                <!-- Sub-row 1: Symbol & Dynamic Live Market Data Info -->
                <div class="flex flex-wrap items-center gap-6 text-xs text-zinc-400">
                    <div class="flex items-center gap-3">
                        <span class="font-bold text-white uppercase tracking-wider text-sm">Order Ticket</span>
                        <div class="relative w-36" @click.away="searchSymbolsOpen = false">
                            <input type="text" x-model="orderSymbol" @input="orderSymbolName = ''; searchSymbols()" @focus="searchSymbols()" placeholder="SYMBOL" 
                                   class="w-full bg-[#202026] border border-zinc-800 text-white rounded-lg px-2.5 py-1 text-xs font-black uppercase placeholder-zinc-500 focus:outline-none focus:border-blue-500" />
                            
                            <!-- Search Dropdown -->
                            <div x-show="searchSymbolsOpen && searchSymbolsList.length > 0" 
                                 class="absolute left-0 right-0 z-50 mt-1 bg-[#1a1a22] border border-zinc-800 rounded-xl shadow-2xl max-h-60 overflow-y-auto"
                                 x-transition>
                                <template x-for="item in searchSymbolsList" :key="item.symbol">
                                    <button type="button" @click="orderSymbol = item.symbol; orderSymbolName = item.name; checkEtbStatus(); searchSymbolsOpen = false; updateTickerMetrics(item.symbol);"
                                            class="w-full text-left px-3 py-2 text-xs hover:bg-zinc-800 transition-colors flex items-center justify-between border-b border-zinc-800 last:border-b-0 cursor-pointer">
                                        <div>
                                            <span class="font-bold text-white" x-text="item.symbol"></span>
                                            <span class="text-zinc-500 ml-2" x-text="item.name"></span>
                                        </div>
                                        <span class="text-[8px] bg-zinc-800 text-zinc-400 px-1 py-0.5 rounded font-extrabold uppercase">Stock</span>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Dynamic Stock Ticker Info -->
                    <div class="flex flex-wrap items-center gap-x-5 gap-y-1 text-[11px] border-l border-zinc-800 pl-5">
                        <div class="font-bold text-white uppercase" x-text="orderSymbol ? orderSymbol + ' - ' + (orderSymbolName || 'Stock') : 'SELECT SYMBOL'"></div>
                        <template x-if="orderSymbol">
                            <div class="flex gap-1.5 items-center">
                                <span class="text-zinc-500">Last</span>
                                <span class="font-bold tz-font-mono" :class="tickerChange.startsWith('-') ? 'text-rose-500' : 'text-emerald-500'" x-text="tickerLast">0.00</span>
                                <span class="text-[9px] tz-font-mono font-extrabold" :class="tickerChange.startsWith('-') ? 'text-rose-500' : 'text-emerald-500'" x-text="tickerChange">0.00</span>
                                <span class="text-[9px] tz-font-mono font-extrabold" :class="tickerChange.startsWith('-') ? 'text-rose-500' : 'text-emerald-500'" x-text="'(' + tickerPctChange + ')'">(0.00%)</span>
                                
                                <template x-if="orderSecurityType !== 'Mleg'">
                                    <span class="px-1 bg-emerald-950/40 border border-emerald-900 text-emerald-500 rounded text-[8px] font-extrabold ml-1 uppercase"
                                          :class="etbStatus === 'HTB' ? 'bg-rose-950/40 border-rose-900 text-rose-500' : ''"
                                          x-text="etbStatus || 'Checking...'"></span>
                                </template>
                            </div>
                        </template>
                        <template x-if="orderSymbol">
                            <div class="flex gap-4 text-zinc-500 font-mono">
                                <div>High: <span class="text-zinc-300 font-bold" x-text="tickerHigh">0.00</span></div>
                                <div>Low: <span class="text-zinc-300 font-bold" x-text="tickerLow">0.00</span></div>
                                <div>Close: <span class="text-zinc-300 font-bold" x-text="tickerClose">0.00</span></div>
                                <div>Bid: <span class="text-zinc-300 font-bold" x-text="tickerBid">0.00</span></div>
                                <div>Ask: <span class="text-zinc-300 font-bold" x-text="tickerAsk">0.00</span></div>
                                <div>Vol: <span class="text-zinc-300 font-bold" x-text="tickerVolume">0</span></div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Sub-row 2: Parameter Inputs and Order Submission -->
                <div class="flex flex-wrap items-end justify-between gap-5 border-t border-zinc-800/60 pt-3">
                    <div class="flex flex-wrap items-center gap-4">
                        <!-- Account static field -->
                        <div>
                            <label class="text-[9px] font-extrabold text-zinc-500 block uppercase tracking-wider mb-1">Account</label>
                            <div class="bg-[#202026] text-zinc-300 text-xs font-bold px-3 py-1.5 rounded-lg border border-zinc-800 tz-font-mono select-all" x-text="accountName"></div>
                        </div>

                        <!-- Quantity input -->
                        <div>
                            <label class="text-[9px] font-extrabold text-zinc-500 block uppercase tracking-wider mb-1">Quantity</label>
                            <input type="number" x-model.number="orderQuantity" min="1" step="10"
                                   class="w-20 bg-[#202026] border border-zinc-800 text-white rounded-lg px-2.5 py-1 text-xs font-black tz-font-mono focus:outline-none focus:border-blue-500" />
                        </div>

                        <!-- Display Qty optional check -->
                        <div class="flex items-center gap-1.5 h-8">
                            <input type="checkbox" id="display_qty_chk" class="rounded border-zinc-800 text-blue-600 focus:ring-0 focus:ring-offset-0 bg-[#202026] cursor-pointer" />
                            <label for="display_qty_chk" class="text-[9px] font-extrabold text-zinc-500 uppercase tracking-wider cursor-pointer">Display Qty</label>
                        </div>

                        <!-- Route selector -->
                        <div>
                            <label class="text-[9px] font-extrabold text-zinc-500 block uppercase tracking-wider mb-1">Route</label>
                            <select x-model="selectedRoute" class="bg-[#202026] border border-zinc-800 text-white rounded-lg text-xs font-bold px-2.5 py-1 focus:outline-none cursor-pointer hover:border-zinc-700">
                                <template x-for="r in routesList" :key="r.routeName">
                                    <option :value="r.routeName" x-text="r.routeName"></option>
                                </template>
                            </select>
                        </div>

                        <!-- Order Type -->
                        <div>
                            <label class="text-[9px] font-extrabold text-zinc-500 block uppercase tracking-wider mb-1">Order Type</label>
                            <select x-model="orderType" class="bg-[#202026] border border-zinc-800 text-white rounded-lg text-xs font-bold px-2.5 py-1 focus:outline-none cursor-pointer hover:border-zinc-700">
                                <option value="Market">MKT</option>
                                <option value="Limit">LMT</option>
                            </select>
                        </div>

                        <!-- Price input -->
                        <div x-show="orderType === 'Limit'">
                            <label class="text-[9px] font-extrabold text-zinc-500 block uppercase tracking-wider mb-1">Price</label>
                            <input type="number" x-model="orderLimitPrice" step="0.01" min="0.01"
                                   class="w-24 bg-[#202026] border border-zinc-800 text-white rounded-lg px-2.5 py-1 text-xs font-black tz-font-mono focus:outline-none focus:border-blue-500" />
                        </div>

                        <!-- Stop Price -->
                        <div>
                            <label class="text-[9px] font-extrabold text-zinc-500 block uppercase tracking-wider mb-1">Stop Price</label>
                            <input type="number" value="0.00" placeholder="0.00" disabled
                                   class="w-20 bg-[#202026]/40 border border-zinc-800/60 text-zinc-600 rounded-lg px-2.5 py-1 text-xs font-black tz-font-mono cursor-not-allowed select-none" />
                        </div>

                        <!-- Time in Force -->
                        <div>
                            <label class="text-[9px] font-extrabold text-zinc-500 block uppercase tracking-wider mb-1">TIF</label>
                            <select x-model="orderTimeInForce" class="bg-[#202026] border border-zinc-800 text-white rounded-lg text-xs font-bold px-2.5 py-1 focus:outline-none cursor-pointer hover:border-zinc-700">
                                <option value="Day">DAY</option>
                                <option value="GTC">GTC</option>
                            </select>
                        </div>
                    </div>

                    <!-- Buy/Sell Grid of Buttons matching the layout -->
                    <div class="grid grid-cols-2 gap-2 w-full sm:w-auto shrink-0 select-none">
                        <button @click="orderSide = 'Buy'; submitOrder()" :disabled="orderIsSubmitting"
                                class="h-9 px-6 bg-blue-600 hover:bg-blue-700 disabled:opacity-50 text-white text-[11px] font-black uppercase tracking-widest rounded-lg shadow-lg flex items-center justify-center cursor-pointer transition-colors">
                            BUY
                        </button>
                        <button @click="orderSide = 'Sell'; submitOrder()" :disabled="orderIsSubmitting"
                                class="h-9 px-6 bg-rose-600 hover:bg-rose-700 disabled:opacity-50 text-white text-[11px] font-black uppercase tracking-widest rounded-lg shadow-lg flex items-center justify-center cursor-pointer transition-colors">
                            SELL
                        </button>
                        <button @click="orderSide = 'Short'; submitOrder()" :disabled="orderIsSubmitting"
                                class="h-9 px-6 bg-rose-500 hover:bg-rose-600 disabled:opacity-50 text-white text-[11px] font-black uppercase tracking-widest rounded-lg shadow-lg flex items-center justify-center cursor-pointer transition-colors">
                            SHORT
                        </button>
                        <button @click="orderSide = 'Cover'; submitOrder()" :disabled="orderIsSubmitting"
                                class="h-9 px-6 bg-blue-500 hover:bg-blue-600 disabled:opacity-50 text-white text-[11px] font-black uppercase tracking-widest rounded-lg shadow-lg flex items-center justify-center cursor-pointer transition-colors">
                            COVER
                        </button>
                    </div>
                </div>
            </section>

            <!-- ── DOCK PANEL GRID DESK ── -->
            <main class="flex-1 p-4 grid grid-cols-1 lg:grid-cols-12 gap-4 max-w-[1600px] w-full mx-auto">
                <!-- Left Column (width 4/12): Notifications & Level 2 -->
                <div class="lg:col-span-4 space-y-4">
                    <!-- Notifications Logs Panel -->
                    <div class="tz-panel p-4 flex flex-col h-60 min-h-60 overflow-hidden">
                        <h4 class="text-xs font-extrabold text-zinc-400 uppercase tracking-wider pb-2 border-b border-zinc-800/60 select-none">Notifications</h4>
                        <div class="flex-1 overflow-y-auto mt-2 space-y-2.5 custom-scrollbar text-[11px] font-sans">
                            <template x-for="log in notificationsList" :key="log.time">
                                <div class="flex items-start gap-2 border-b border-zinc-800/40 pb-1.5 last:border-b-0">
                                    <span class="text-zinc-500 tz-font-mono font-bold shrink-0 select-all" x-text="log.time"></span>
                                    <span class="text-zinc-300" x-text="log.message"></span>
                                </div>
                            </template>
                            <template x-if="notificationsList.length === 0">
                                <div class="text-zinc-500 text-center py-8 select-none">No active order notifications.</div>
                            </template>
                        </div>
                    </div>

                    <!-- Level 2 Book Simulator -->
                    <div class="tz-panel p-4 flex flex-col h-72 min-h-72 overflow-hidden">
                        <h4 class="text-xs font-extrabold text-zinc-400 uppercase tracking-wider pb-2 border-b border-zinc-800/60 select-none">Level 2 Quotes</h4>
                        
                        <div class="flex-1 overflow-y-auto mt-2 custom-scrollbar text-[11px]">
                            <table class="w-full text-left font-mono">
                                <thead>
                                    <tr class="text-[9px] font-extrabold text-zinc-500 uppercase tracking-wider tz-border-b">
                                        <th class="pb-1.5">MMID</th>
                                        <th class="pb-1.5 text-right">Bid</th>
                                        <th class="pb-1.5 text-right">Ask</th>
                                        <th class="pb-1.5 text-right">Size</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-zinc-900/65 text-zinc-400">
                                    <template x-for="mm in level2Quotes" :key="mm.mmid">
                                        <tr class="hover:bg-zinc-800/20">
                                            <td class="py-2 text-emerald-500 font-sans font-bold uppercase" x-text="mm.mmid"></td>
                                            <td class="py-2 text-right font-bold text-emerald-500" x-text="Number(mm.bid).toFixed(2)"></td>
                                            <td class="py-2 text-right font-bold text-rose-500" x-text="Number(mm.ask).toFixed(2)"></td>
                                            <td class="py-2 text-right text-zinc-500" x-text="mm.size * 100"></td>
                                        </tr>
                                    </template>
                                    <template x-if="!orderSymbol">
                                        <tr>
                                            <td colspan="4" class="py-8 text-center text-zinc-500 font-sans">Enter a symbol to view Level 2 quotes.</td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Right Column (width 8/12): Candlestick Chart & Tabbed Portfolio -->
                <div class="lg:col-span-8 flex flex-col gap-4">
                    
                    <!-- Candlestick TradingView Embed Chart -->
                    <div class="tz-panel flex-1 flex flex-col overflow-hidden min-h-[380px]">
                        <div class="px-4 py-2 bg-[#101014] border-b border-zinc-800 flex items-center justify-between shrink-0 select-none">
                            <span class="text-xs font-extrabold text-zinc-400 uppercase tracking-wider">TradingView Candlestick Chart</span>
                            <span class="px-2 py-0.5 bg-blue-950/40 border border-blue-900/60 rounded text-[9px] font-extrabold text-blue-400 tz-font-mono" x-text="orderSymbol ? orderSymbol.toUpperCase() : 'AAPL'"></span>
                        </div>
                        <div class="flex-1 bg-zinc-950 relative min-h-[340px]">
                            <!-- Iframe embedding TradingView free stock charting widget -->
                            <iframe :src="'https://s.tradingview.com/widgetembed/?frameElementId=tradingview_chart&symbol=' + (orderSymbol ? orderSymbol.toUpperCase() : 'AAPL') + '&interval=D&symboledit=1&saveimage=1&toolbarbg=f1f3f6&studies=%5B%5D&theme=dark&style=1&timezone=exchange&studies_overrides=%7B%7D&overrides=%7B%7D&enabled_features=%5B%5D&disabled_features=%5B%5D&locale=en&utm_source=localhost&utm_medium=widget&utm_campaign=chart-embed'"
                                    class="w-full h-full border-0 absolute inset-0" style="min-height: 340px;"></iframe>
                        </div>
                    </div>

                    <!-- Portfolio Tabs Grid (Open Positions, Active Orders, Inactive History) -->
                    <div class="tz-panel p-4 flex flex-col h-[300px] min-h-[300px] overflow-hidden">
                        <div class="flex justify-between items-center border-b border-zinc-800/80 pb-2 mb-3 select-none">
                            <div class="flex gap-4 text-xs font-extrabold uppercase">
                                <button @click="ordersTab = 'positions'" 
                                        :class="ordersTab === 'positions' ? 'text-blue-500 border-b-2 border-blue-500 pb-1.5' : 'text-zinc-500 hover:text-zinc-300'" class="cursor-pointer">
                                    Open Positions (<span x-text="positionsList.length">0</span>)
                                </button>
                                <button @click="ordersTab = 'live'" 
                                        :class="ordersTab === 'live' ? 'text-blue-500 border-b-2 border-blue-500 pb-1.5' : 'text-zinc-500 hover:text-zinc-300'" class="cursor-pointer">
                                    Active Orders (<span x-text="ordersList.filter(o => ['New','PendingNew','Accepted','PartiallyFilled'].includes(o.orderStatus)).length">0</span>)
                                </button>
                                <button @click="ordersTab = 'inactive'; fetchHistoricalOrders()" 
                                        :class="ordersTab === 'inactive' ? 'text-blue-500 border-b-2 border-blue-500 pb-1.5' : 'text-zinc-500 hover:text-zinc-300'" class="cursor-pointer">
                                    Inactive Orders
                                </button>
                            </div>
                        </div>

                        <div class="flex-1 overflow-y-auto custom-scrollbar text-[11px] font-mono">
                            <!-- 1. OPEN POSITIONS TAB -->
                            <div x-show="ordersTab === 'positions'">
                                <table class="w-full text-left">
                                    <thead>
                                        <tr class="text-[9px] font-extrabold text-zinc-500 uppercase tracking-wider tz-border-b">
                                            <th class="pb-2">Symbol</th>
                                            <th class="pb-2 text-right">Shares</th>
                                            <th class="pb-2 text-right">Side</th>
                                            <th class="pb-2 text-right">Avg Price</th>
                                            <th class="pb-2 text-right">Current Price</th>
                                            <th class="pb-2 text-right">Unrealized P&L</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-zinc-900/60 text-zinc-300">
                                        <template x-for="pos in positionsList" :key="pos.symbol">
                                            <tr class="hover:bg-zinc-800/10">
                                                <td class="py-2.5 font-sans font-black text-white" x-text="pos.symbol"></td>
                                                <td class="py-2.5 text-right font-bold" x-text="pos.quantity"></td>
                                                <td class="py-2.5 text-right">
                                                    <span class="px-1.5 py-0.5 rounded text-[8px] font-black border uppercase"
                                                          :class="pos.side === 'Short' ? 'border-rose-900 bg-rose-950/20 text-rose-500' : 'border-blue-900 bg-blue-950/20 text-blue-500'"
                                                          x-text="pos.side"></span>
                                                </td>
                                                <td class="py-2.5 text-right" x-text="'$' + Number(pos.avgPrice).toFixed(2)"></td>
                                                <td class="py-2.5 text-right" x-text="'$' + Number(pos.close || pos.avgPrice).toFixed(2)"></td>
                                                <td class="py-2.5 text-right font-bold"
                                                    :class="pos.unrealized < 0 ? 'text-rose-500' : 'text-emerald-500'"
                                                    x-text="(pos.unrealized >= 0 ? '+' : '') + '$' + Number(pos.unrealized).toFixed(2)"></td>
                                            </tr>
                                        </template>
                                        <template x-if="positionsList.length === 0">
                                            <tr>
                                                <td colspan="6" class="py-8 text-center text-zinc-500 font-sans">No open positions.</td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>

                            <!-- 2. ACTIVE ORDERS TAB -->
                            <div x-show="ordersTab === 'live'">
                                <table class="w-full text-left">
                                    <thead>
                                        <tr class="text-[9px] font-extrabold text-zinc-500 uppercase tracking-wider tz-border-b">
                                            <th class="pb-2">Client Order ID</th>
                                            <th class="pb-2">Symbol</th>
                                            <th class="pb-2 text-right">Side</th>
                                            <th class="pb-2 text-right">Shares</th>
                                            <th class="pb-2 text-right">Type</th>
                                            <th class="pb-2 text-right">Limit Price</th>
                                            <th class="pb-2 text-right">Status</th>
                                            <th class="pb-2 text-right">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-zinc-900/60 text-zinc-300">
                                        <template x-for="ord in ordersList" :key="ord.clientOrderId">
                                            <tr class="hover:bg-zinc-800/10">
                                                <td class="py-2.5 text-zinc-400 select-all" x-text="ord.clientOrderId"></td>
                                                <td class="py-2.5 font-sans font-black text-white" x-text="ord.symbol"></td>
                                                <td class="py-2.5 text-right">
                                                    <span class="px-1.5 py-0.5 rounded text-[8px] font-bold border uppercase"
                                                          :class="['Sell', 'Short', 'SellShort', 'sellshort'].includes(ord.side) ? 'border-rose-900 bg-rose-950/20 text-rose-500' : 'border-blue-900 bg-blue-950/20 text-blue-500'"
                                                          x-text="ord.side === 'SellShort' || ord.side === 'sellshort' ? 'SHORT' : ord.side"></span>
                                                </td>
                                                <td class="py-2.5 text-right" x-text="ord.orderQuantity"></td>
                                                <td class="py-2.5 text-right text-zinc-400" x-text="ord.orderType"></td>
                                                <td class="py-2.5 text-right" x-text="ord.limitPrice ? '$' + Number(ord.limitPrice).toFixed(2) : 'MKT'"></td>
                                                <td class="py-2.5 text-right font-bold text-zinc-400" x-text="ord.orderStatus"></td>
                                                <td class="py-2.5 text-right">
                                                    <template x-if="['New','PendingNew','Accepted','PartiallyFilled'].includes(ord.orderStatus)">
                                                        <button @click="cancelOrder(ord.clientOrderId)"
                                                                class="px-2 py-0.5 bg-rose-900 border border-rose-800 text-rose-400 hover:bg-rose-800 rounded text-[9px] font-bold cursor-pointer transition-colors shadow">
                                                            CANCEL
                                                        </button>
                                                    </template>
                                                </td>
                                            </tr>
                                        </template>
                                        <template x-if="ordersList.length === 0">
                                            <tr>
                                                <td colspan="8" class="py-8 text-center text-zinc-500 font-sans">No active working orders.</td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>

                            <!-- 3. INACTIVE HISTORY TAB -->
                            <div x-show="ordersTab === 'inactive'">
                                <table class="w-full text-left">
                                    <thead>
                                        <tr class="text-[9px] font-extrabold text-zinc-500 uppercase tracking-wider tz-border-b">
                                            <th class="pb-2">Client Order ID</th>
                                            <th class="pb-2">Symbol</th>
                                            <th class="pb-2 text-right">Side</th>
                                            <th class="pb-2 text-right">Qty</th>
                                            <th class="pb-2 text-right">Type</th>
                                            <th class="pb-2 text-right">Limit Price</th>
                                            <th class="pb-2 text-right">Status</th>
                                            <th class="pb-2 text-right">Executed Date</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-zinc-900/60 text-zinc-300">
                                        <template x-for="ord in historicalOrdersList" :key="ord.clientOrderId">
                                            <tr class="hover:bg-zinc-800/10">
                                                <td class="py-2.5 text-zinc-500 select-all" x-text="ord.clientOrderId"></td>
                                                <td class="py-2.5 font-sans font-black text-white" x-text="ord.symbol"></td>
                                                <td class="py-2.5 text-right">
                                                    <span class="px-1.5 py-0.5 rounded text-[8px] font-bold border uppercase"
                                                          :class="['Sell', 'Short', 'SellShort', 'sellshort'].includes(ord.side) ? 'border-rose-900 bg-rose-950/20 text-rose-500' : 'border-blue-900 bg-blue-950/20 text-blue-500'"
                                                          x-text="ord.side === 'SellShort' || ord.side === 'sellshort' ? 'SHORT' : ord.side"></span>
                                                </td>
                                                <td class="py-2.5 text-right" x-text="ord.orderQuantity"></td>
                                                <td class="py-2.5 text-right text-zinc-400" x-text="ord.orderType"></td>
                                                <td class="py-2.5 text-right" x-text="ord.limitPrice ? '$' + Number(ord.limitPrice).toFixed(2) : 'MKT'"></td>
                                                <td class="py-2.5 text-right text-zinc-400 font-extrabold" x-text="ord.orderStatus"></td>
                                                <td class="py-2.5 text-right text-zinc-500" x-text="new Date(ord.created).toLocaleString()"></td>
                                            </tr>
                                        </template>
                                        <template x-if="historicalOrdersList.length === 0">
                                            <tr>
                                                <td colspan="8" class="py-8 text-center text-zinc-500 font-sans">No historical orders found.</td>
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

    <!-- Notification Toast Element -->
    <div id="neo_toast_box" class="fixed bottom-5 right-5 z-50 space-y-2 pointer-events-none"></div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const neoToast = (message, type = 'success') => {
                const toastBox = document.getElementById('neo_toast_box');
                if (!toastBox) return;
                
                const toast = document.createElement('div');
                toast.className = `px-4 py-3 rounded-xl border text-xs font-bold shadow-2xl flex items-center gap-2.5 transition-all duration-300 transform translate-y-2 opacity-0 pointer-events-auto max-w-sm `;
                if (type === 'success') {
                    toast.className += 'bg-emerald-950 border-emerald-900 text-emerald-400';
                    toast.innerHTML = '<span>✅</span>' + message;
                } else if (type === 'error') {
                    toast.className += 'bg-rose-950 border-rose-900 text-rose-400';
                    toast.innerHTML = '<span>❌</span>' + message;
                } else {
                    toast.className += 'bg-[#1b1b22] border-zinc-800 text-zinc-300';
                    toast.innerHTML = '<span>ℹ️</span>' + message;
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
            window.neoToast = neoToast;

            const container = document.getElementById('main_trade_container');
            const getAlpineScope = () => {
                if (container && window.Alpine) {
                    return Alpine.$data(container);
                }
                return null;
            };

            // Snapshot dynamic loop
            let delayMs = 1000;
            let lastSnapshotStr = '';
            
            window.pollTradeSnapshot = () => {
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
                            
                            AlpineScope.positionsList = snap.positions || [];
                            AlpineScope.ordersList = snap.orders || [];

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
                                console.log('Trade snapshot sync update:', current);
                            }
                        }
                        setTimeout(window.pollTradeSnapshot, delayMs);
                    })
                    .catch(err => {
                        console.error('Error fetching snapshot:', err);
                        delayMs = Math.min(delayMs * 2, 5000);
                        setTimeout(window.pollTradeSnapshot, delayMs);
                    });
            };
        });

        function tradePage() {
            return {
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
                ordersTab: 'positions',
                historicalOrdersList: [],
                historicalStartDate: new Date(new Date().setFullYear(new Date().getFullYear() - 1)).toISOString().split('T')[0],
                historicalPage: 1,
                historicalTotalPages: 1,
                historicalTotalOrders: 0,
                historicalLoading: false,
                fillsList: [],
                fillsStartDate: new Date(new Date().setDate(new Date().getDate() - 7)).toISOString().split('T')[0],
                fillsLoading: false,
                routesList: [{routeName: 'SMART'}],
                selectedRoute: 'SMART',
                routesLoading: false,

                orderSymbol: '',
                orderSymbolName: '',
                searchSymbolsList: [],
                searchSymbolsLoading: false,
                searchSymbolsOpen: false,
                searchSymbolsTimer: null,
                orderSecurityType: 'Stock',
                orderSide: 'Buy',
                orderQuantity: 50,
                orderType: 'Limit',
                orderLimitPrice: '0.00',
                orderTimeInForce: 'Day',
                orderIsSubmitting: false,
                orderLegs: [
                    { symbol: '', side: 'Buy', ratio: 1, openClose: 'Open' }
                ],
                etbStatus: '',
                etbLoading: false,

                // Dynamic Ticker simulation states
                tickerLast: '0.00',
                tickerChange: '0.00',
                tickerPctChange: '0.00%',
                tickerHigh: '0.00',
                tickerLow: '0.00',
                tickerClose: '0.00',
                tickerBid: '0.00',
                tickerAsk: '0.00',
                tickerVolume: '0',

                level2Quotes: [],
                notificationsList: [],
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
                        if (window.pollTradeSnapshot) {
                            window.pollTradeSnapshot();
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
                        this.totalLocateCosts = snap.totalLocateCosts;
                        this.exposure = snap.exposure;
                        this.usedLeverage = snap.usedLeverage;
                        this.marginRatio = snap.marginRatio;
                        this.marginRequirement = snap.marginRequirement;
                        this.marginDeficit = snap.marginDeficit;
                        this.positionsCount = snap.positionsCount;
                        this.openOrdersCount = snap.openOrdersCount;
                        
                        this.positionsList = snap.positions || [];
                    } else if (action === 'update' && target === 'aggCalcs') {
                        Object.assign(this, msg.aggCalcs);
                    } else if (action === 'update' && target === 'position') {
                        const p = msg.position;
                        const row = this.positionsList.find(pos => pos.symbol === p.symbol);
                        if (row) {
                            Object.assign(row, p);
                            if (p.pnlCalc) {
                                row.unrealized = p.pnlCalc.unrealizedPnL;
                                this.recalculatePnlTotals();
                            }
                        } else {
                            this.positionsList.push({
                                symbol: p.symbol,
                                quantity: p.shares || p.quantity || 100,
                                avgPrice: p.priceAvg || p.avgPrice || 0,
                                close: p.priceAvg || p.avgPrice || 0,
                                side: p.side || 'Long',
                                unrealized: p.pnlCalc ? p.pnlCalc.unrealizedPnL : 0
                            });
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
                            
                            const timeStr = new Date().toLocaleTimeString();
                            this.notificationsList.unshift({
                                time: timeStr,
                                message: `Order status update: ${mappedOrder.symbol} (${mappedOrder.side}) is ${mappedOrder.orderStatus}.`
                            });
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
                    this.fetchRoutes();

                    // Pre-fill parameters support
                    const params = new URLSearchParams(window.location.search);
                    if (params.has('prefill')) {
                        this.orderSecurityType = 'Stock';
                        this.orderSide = params.get('side') || 'Short';
                        this.orderSymbol = params.get('symbol') || '';
                        this.orderQuantity = parseInt(params.get('quantity')) || 50;
                        this.checkEtbStatus();
                        this.updateTickerMetrics(this.orderSymbol);
                        const newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
                        window.history.pushState({ path: newUrl }, '', newUrl);
                    } else {
                        // Default to MCK symbol for illustration
                        this.orderSymbol = 'MCK';
                        this.orderSymbolName = 'McKesson Corporation';
                        this.checkEtbStatus();
                        this.updateTickerMetrics('MCK');
                    }
                },

                updateTickerMetrics(symbol) {
                    if (!symbol) return;
                    let hash = 0;
                    for (let i = 0; i < symbol.length; i++) {
                        hash = symbol.charCodeAt(i) + ((hash << 5) - hash);
                    }
                    let basePrice = Math.abs(hash % 300) + 10;
                    
                    this.tickerLast = basePrice.toFixed(2);
                    this.tickerChange = (hash % 2 === 0 ? '+' : '-') + Math.abs((hash % 10) + Math.random()).toFixed(2);
                    this.tickerPctChange = (hash % 2 === 0 ? '+' : '-') + Math.abs((hash % 5) + Math.random()).toFixed(2) + '%';
                    this.tickerVolume = Math.abs((hash % 500000) * 10 + 100000).toLocaleString();
                    this.tickerHigh = (basePrice * 1.03).toFixed(2);
                    this.tickerLow = (basePrice * 0.97).toFixed(2);
                    this.tickerClose = (basePrice * 0.99).toFixed(2);
                    this.tickerBid = (basePrice - 0.05).toFixed(2);
                    this.tickerAsk = (basePrice + 0.05).toFixed(2);
                    this.orderLimitPrice = this.tickerLast;

                    // Generate Level 2 quote book MM bids/asks
                    const mms = ['CMCO', 'BATS', 'NYSE', 'ARCA', 'EDGA', 'EDGX', 'NSD'];
                    this.level2Quotes = mms.map((mm, idx) => {
                        const bidDiff = (idx * 0.02 + Math.random() * 0.03);
                        const askDiff = (idx * 0.02 + Math.random() * 0.03);
                        return {
                            mmid: mm,
                            bid: (basePrice - bidDiff),
                            ask: (basePrice + askDiff),
                            size: Math.abs((hash + idx) % 15) + 1
                        };
                    });
                },

                fetchRoutes() {
                    this.routesLoading = true;
                    const accountParam = this.accountName ? `?account=${this.accountName}` : '';
                    fetch(`/broker/routes${accountParam}`)
                        .then(res => res.json())
                        .then(data => {
                            if (data.success && data.routes && data.routes.length > 0) {
                                this.routesList = data.routes;
                                const routeNames = this.routesList.map(r => r.routeName);
                                if (routeNames.includes('SMART')) {
                                    this.selectedRoute = 'SMART';
                                } else {
                                    this.selectedRoute = routeNames[0];
                                }
                            } else {
                                this.routesList = [{routeName: 'SMART'}];
                                this.selectedRoute = 'SMART';
                            }
                        })
                        .catch(err => {
                            console.error('Error fetching routes:', err);
                            this.routesList = [{routeName: 'SMART'}];
                            this.selectedRoute = 'SMART';
                        })
                        .finally(() => {
                            this.routesLoading = false;
                        });
                },

                searchSymbols() {
                    this.checkEtbStatus();
                    let q = this.orderSymbol.trim();
                    if (q.length < 1) {
                        this.searchSymbolsList = [];
                        this.searchSymbolsOpen = false;
                        return;
                    }
                    this.searchSymbolsLoading = true;
                    this.searchSymbolsOpen = true;
                    clearTimeout(this.searchSymbolsTimer);
                    this.searchSymbolsTimer = setTimeout(() => {
                        fetch(`/broker/symbols/search?q=${encodeURIComponent(q)}`)
                            .then(res => res.json())
                            .then(data => {
                                if (data.success && this.searchSymbolsOpen) {
                                    this.searchSymbolsList = data.results || [];
                                }
                            })
                            .catch(err => console.error('Symbol search error:', err))
                            .finally(() => {
                                this.searchSymbolsLoading = false;
                            });
                    }, 250);
                },

                checkEtbStatus() {
                    const sym = this.orderSymbol.trim().toUpperCase();
                    if (!sym) {
                        this.etbStatus = '';
                        return;
                    }
                    this.etbLoading = true;
                    const accountParam = this.accountName ? `&account=${this.accountName}` : '';
                    fetch(`/broker/locate/check-etb?symbol=${sym}${accountParam}`)
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                this.etbStatus = data.isEasyToBorrow ? 'ETB' : 'HTB';
                            } else {
                                this.etbStatus = '';
                            }
                        })
                        .catch(() => {
                            this.etbStatus = '';
                        })
                        .finally(() => {
                            this.etbLoading = false;
                        });
                },

                submitOrder() {
                    const sym = this.orderSymbol.trim().toUpperCase();
                    if (!sym) {
                        window.neoToast('Please enter a symbol', 'error');
                        return;
                    }

                    this.orderIsSubmitting = true;
                    
                    const timeStr = new Date().toLocaleTimeString();
                    this.notificationsList.unshift({
                        time: timeStr,
                        message: `Order requested: Your MCK order has been requested.`
                    });

                    const body = {
                        symbol: sym,
                        security_type: this.orderSecurityType,
                        side: this.orderSecurityType === 'Mleg' ? null : this.orderSide,
                        quantity: this.orderQuantity,
                        order_type: this.orderType,
                        limit_price: this.orderType === 'Limit' ? this.orderLimitPrice : null,
                        time_in_force: this.orderTimeInForce,
                        route: this.selectedRoute,
                        account_id: this.accountName
                    };

                    fetch('/broker/order', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify(body)
                    })
                    .then(async res => {
                        const data = await res.json();
                        if (!res.ok) {
                            throw new Error(data.message || 'Failed to submit order');
                        }
                        return data;
                    })
                    .then(data => {
                        if (data.success) {
                            const doneTime = new Date().toLocaleTimeString();
                            this.notificationsList.unshift({
                                time: doneTime,
                                message: `Order placed: Your ${body.order_type} ${body.side} order of ${body.quantity} ${sym} has been placed.`
                            });

                            window.neoToast(data.message || 'Order executed successfully!', 'success');
                            this.orderSymbol = '';
                            this.etbStatus = '';
                            if (window.pollTradeSnapshot) {
                                window.pollTradeSnapshot();
                            }
                        } else {
                            window.neoToast(data.message || 'Failed to place order', 'error');
                        }
                    })
                    .catch(err => {
                        window.neoToast(err.message || 'Server error occurred', 'error');
                        this.notificationsList.unshift({
                            time: new Date().toLocaleTimeString(),
                            message: `Order failed: ${err.message}`
                        });
                    })
                    .finally(() => {
                        this.orderIsSubmitting = false;
                    });
                },

                cancelOrder(clientOrderId) {
                    fetch('/broker/order/cancel', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            client_order_id: clientOrderId,
                            account_id: this.accountName
                        })
                    })
                    .then(async res => {
                        const data = await res.json();
                        if (!res.ok) {
                            throw new Error(data.message || 'Failed to cancel order');
                        }
                        return data;
                    })
                    .then(data => {
                        if (data.success) {
                            window.neoToast('Order cancelled successfully!', 'success');
                            this.notificationsList.unshift({
                                time: new Date().toLocaleTimeString(),
                                message: `Order cancelled: Client Order ID ${clientOrderId} has been cancelled.`
                            });
                            if (window.pollTradeSnapshot) {
                                window.pollTradeSnapshot();
                            }
                        } else {
                            window.neoToast(data.message || 'Failed to cancel order', 'error');
                        }
                    })
                    .catch(err => {
                        window.neoToast(err.message || 'Server error occurred', 'error');
                    });
                },

                fetchHistoricalOrders() {
                    this.historicalLoading = true;
                    const accountParam = this.accountName ? `&account=${this.accountName}` : '';
                    fetch(`/broker/orders-history?start_date=${this.historicalStartDate}&page=${this.historicalPage}${accountParam}`)
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                this.historicalOrdersList = data.orders || [];
                                if (data.pagination) {
                                    this.historicalPage = data.pagination.currentPage || 1;
                                    this.historicalTotalPages = data.pagination.totalPages || 1;
                                    this.historicalTotalOrders = data.pagination.total || 0;
                                }
                            } else {
                                window.neoToast(data.message || 'Failed to fetch historical orders', 'error');
                            }
                        })
                        .catch(err => {
                            console.error('Error fetching historical orders:', err);
                        })
                        .finally(() => {
                            this.historicalLoading = false;
                        });
                }
            };
        }
    </script>
</x-app-layout>
