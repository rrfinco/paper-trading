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

    <!-- Custom Style Overrides for Premium Light SaaS Locates Panel -->
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

    <div x-data="locatesPage()" id="main_locates_container" class="min-h-screen bg-[#f4f5f8] text-zinc-800 flex antialiased">

        <!-- ── LEFT SIDEBAR PANEL ── -->
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
                    
                    <a href="{{ route('dashboard') }}" class="w-full flex items-center rounded-xl text-xs font-bold tracking-wide text-zinc-500 hover:text-zinc-900 hover:bg-zinc-50" :class="sidebarCollapsed ? 'justify-center py-3 px-0' : 'gap-3.5 px-4 py-3'">
                        <span class="text-lg">📊</span>
                        <span x-show="!sidebarCollapsed">Dashboard</span>
                    </a>

                    <a href="{{ route('trade') }}" class="w-full flex items-center rounded-xl text-xs font-bold tracking-wide text-zinc-500 hover:text-zinc-900 hover:bg-zinc-50" :class="sidebarCollapsed ? 'justify-center py-3 px-0' : 'gap-3.5 px-4 py-3'">
                        <span class="text-lg">📈</span>
                        <span x-show="!sidebarCollapsed">Trade Entry</span>
                    </a>

                    <button class="w-full flex items-center rounded-xl text-xs font-bold tracking-wide glowing-sidebar-item active" :class="sidebarCollapsed ? 'justify-center py-3 px-0' : 'gap-3.5 px-4 py-3'">
                        <span class="text-lg">🔑</span>
                        <span x-show="!sidebarCollapsed">Locates Manager</span>
                    </button>
                </nav>
            </div>
        </aside>

        <!-- ── MAIN BODY CONTENT CONTAINER ── -->
        <div class="flex-1 flex flex-col min-w-0 overflow-y-auto">

            <!-- ── TOP BAR ── -->
            <header class="h-20 border-b border-zinc-200 px-6 md:px-8 flex items-center justify-between gap-6 shrink-0 bg-white/90 backdrop-blur-md sticky top-0 z-30">
                <div class="flex items-center gap-4">
                    <!-- Collapse Toggle Button -->
                    <button @click="sidebarCollapsed = !sidebarCollapsed" class="p-2 hover:bg-zinc-100 text-zinc-650 hover:text-zinc-900 border border-zinc-200/80 rounded-xl cursor-pointer shadow-sm select-none" title="Toggle Sidebar">
                        <svg class="w-4 h-4" :class="sidebarCollapsed ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                    <h2 class="text-lg font-black text-zinc-800">Locates & Borrows Manager</h2>
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

            <!-- ── MAIN CONTENT GRID ── -->
            <main class="flex-1 p-6 md:p-8 space-y-6 max-w-7xl w-full mx-auto">
                
                <!-- Filters Panel -->
                <div class="premium-card p-4 bg-white flex flex-col sm:flex-row gap-4 items-center justify-between">
                    <div class="flex items-center gap-2 select-none">
                        <span class="text-lg">🔍</span>
                        <span class="text-xs font-extrabold text-zinc-700 uppercase tracking-wider">Locates Filter Desk</span>
                    </div>
                    <div class="flex flex-wrap gap-3 w-full sm:w-auto">
                        <div class="flex items-center gap-1.5 flex-1 sm:flex-initial">
                            <span class="text-[9px] font-extrabold text-zinc-400 uppercase tracking-wider">Symbol:</span>
                            <input type="text" x-model="filterSymbol" placeholder="Search Symbol..." 
                                   class="w-full sm:w-36 bg-zinc-50 border border-zinc-200 rounded-lg px-2.5 py-1 text-xs text-zinc-800 font-bold focus:outline-none focus:border-blue-500/50 uppercase tracking-widest" />
                        </div>
                        <div class="flex items-center gap-1.5 flex-1 sm:flex-initial">
                            <span class="text-[9px] font-extrabold text-zinc-400 uppercase tracking-wider">Type:</span>
                            <select x-model="filterType" class="w-full sm:w-36 bg-zinc-50 border border-zinc-200 text-zinc-800 rounded-lg text-xs font-bold px-2.5 py-1 focus:outline-none cursor-pointer">
                                <option value="all">All Types</option>
                                <option value="3">Pre-Borrow</option>
                                <option value="4">Single Use</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

                    <!-- Left Column - Request Form & History Logs -->
                    <div class="lg:col-span-7 space-y-6">

                        <!-- Request Locate Card -->
                        <div class="premium-card p-6 bg-white">
                            <h4 class="text-xs font-extrabold text-zinc-800 uppercase tracking-wider mb-4 select-none">Request Locate Quote</h4>
                            
                            <div class="bg-zinc-50 p-4 rounded-xl border border-zinc-200/60 flex flex-col sm:flex-row sm:items-end justify-between gap-4">
                                <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="text-[9px] font-extrabold text-zinc-400 block uppercase tracking-wider mb-1 select-none">Locate Symbol</label>
                                        <div class="relative" @click.away="searchSymbolsOpen = false">
                                            <input type="text" x-model="locateSymbol" @input="locateSymbolName = ''; searchSymbols()" @focus="searchSymbols()" placeholder="e.g. AMC" 
                                                   class="w-full bg-white border border-zinc-200 rounded-xl px-3 py-2 text-xs text-zinc-800 placeholder-zinc-400 focus:outline-none focus:border-blue-500/50 uppercase tracking-widest font-bold" />
                                            
                                            <!-- Autocomplete Dropdown -->
                                            <div x-show="searchSymbolsOpen && searchSymbolsList.length > 0" 
                                                 class="absolute left-0 right-0 z-50 mt-1 bg-white border border-zinc-200 rounded-xl shadow-lg max-h-60 overflow-y-auto font-sans"
                                                 x-transition>
                                                <template x-for="item in searchSymbolsList" :key="item.symbol">
                                                    <button type="button" @click="locateSymbol = item.symbol; locateSymbolName = item.name; searchSymbolsOpen = false;"
                                                            class="w-full text-left px-4 py-2.5 text-xs hover:bg-zinc-50 transition-colors flex items-center justify-between border-b border-zinc-50 last:border-b-0 cursor-pointer select-none">
                                                        <div>
                                                            <span class="font-bold text-zinc-900" x-text="item.symbol"></span>
                                                            <span class="text-zinc-400 ml-2" x-text="item.name"></span>
                                                        </div>
                                                        <span class="text-[9px] bg-zinc-100 text-zinc-500 px-1.5 py-0.5 rounded font-bold uppercase tracking-wider">Stock</span>
                                                    </button>
                                                </template>
                                            </div>
                                        </div>
                                        <!-- Selected Symbol Name Display -->
                                        <template x-if="locateSymbolName">
                                            <div class="text-[10px] text-blue-600 font-extrabold mt-1.5 uppercase tracking-wide select-none" x-text="locateSymbolName"></div>
                                        </template>
                                    </div>
                                    <div>
                                        <label class="text-[9px] font-extrabold text-zinc-400 block uppercase tracking-wider mb-1 select-none">Requested Quantity</label>
                                        <div class="flex gap-2">
                                            <input type="number" x-model.number="locateQuantity" min="100" step="100"
                                                   class="w-full bg-white border border-zinc-200 rounded-xl px-3 py-2 text-xs text-zinc-800 focus:outline-none focus:border-blue-500/50 font-bold font-mono" />
                                            <button @click="locateQuantity = Math.max(100, (parseInt(locateQuantity || 0) + 100))" class="px-2 py-1 bg-zinc-200/80 hover:bg-zinc-300/80 text-zinc-755 rounded-lg text-[9px] font-bold font-mono border border-zinc-300 cursor-pointer select-none">
                                                +100
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <button @click="requestLocate()" :disabled="locateIsRequesting"
                                        class="h-10 px-5 bg-gradient-to-r from-blue-600 to-indigo-700 hover:from-blue-700 hover:to-indigo-800 text-white font-extrabold text-xs uppercase tracking-wider rounded-xl shadow-md flex items-center justify-center gap-2 cursor-pointer disabled:opacity-50 transition-all select-none">
                                    <span x-show="locateIsRequesting" class="w-3.5 h-3.5 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                                    <span x-text="locateIsRequesting ? 'Requesting...' : 'Request Quote'"></span>
                                </button>
                            </div>
                        </div>

                        <!-- Locate History Card -->
                        <div class="premium-card p-6 bg-white">
                            <h4 class="text-xs font-extrabold text-zinc-800 uppercase tracking-wider mb-4 select-none">Locate Quote History Log</h4>

                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-xs border-collapse">
                                    <thead>
                                        <tr class="border-b border-zinc-200 text-zinc-500 text-[9px] font-extrabold uppercase tracking-widest">
                                            <th class="pb-2">Symbol</th>
                                            <th class="pb-2 text-right">Shares</th>
                                            <th class="pb-2 text-right">Type</th>
                                            <th class="pb-2 text-right">Rate/Share</th>
                                            <th class="pb-2 text-right">Status</th>
                                            <th class="pb-2 text-right">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-zinc-100 font-mono text-zinc-650">
                                        <template x-for="quote in filteredHistoryList" :key="quote.quoteReqID">
                                            <tr class="hover:bg-zinc-50 transition-colors">
                                                <td class="py-3 font-sans font-black text-zinc-800" x-text="quote.symbol"></td>
                                                <td class="py-3 text-right" x-text="quote.quantity"></td>
                                                <td class="py-3 text-right">
                                                    <span class="px-1.5 py-0.5 text-[8px] font-bold rounded uppercase border"
                                                          :class="quote.locateType === 4 ? 'border-orange-250 bg-orange-50 text-orange-600' : 'border-blue-250 bg-blue-50 text-blue-600'"
                                                          x-text="quote.locateType === 4 ? 'Single Use' : 'Pre-Borrow'"></span>
                                                </td>
                                                <td class="py-3 text-right font-bold text-zinc-700" x-text="'$' + Number(quote.locatePrice).toFixed(4)"></td>
                                                <td class="py-3 text-right font-black">
                                                    <span class="px-1.5 py-0.5 text-[8px] font-bold rounded uppercase border"
                                                          :class="{
                                                              'border-emerald-250 bg-emerald-50 text-emerald-600': quote.locateStatus === 50,
                                                              'border-orange-250 bg-orange-50 text-orange-600': quote.locateStatus === 65,
                                                              'border-zinc-250 bg-zinc-50 text-zinc-500': [54, 67].includes(quote.locateStatus),
                                                              'border-rose-250 bg-rose-50 text-rose-600': quote.locateStatus === 56
                                                          }"
                                                          x-text="quote.locateStatus === 50 ? 'Filled' : (quote.locateStatus === 65 ? 'Offered' : (quote.locateStatus === 56 ? 'Rejected' : (quote.locateStatus === 67 ? 'Expired' : 'Pending')))"></span>
                                                </td>
                                                <td class="py-3 text-right">
                                                    <template x-if="quote.locateStatus === 65">
                                                        <div class="flex gap-1 justify-end">
                                                            <button @click="acceptLocateQuote(quote.quoteReqID)" class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded text-[9px] font-bold cursor-pointer transition-colors shadow-sm select-none">
                                                                Accept
                                                            </button>
                                                            <button @click="cancelLocateQuote(quote.quoteReqID)" class="px-2.5 py-1 border border-zinc-300 hover:bg-zinc-100 text-zinc-650 rounded text-[9px] font-bold cursor-pointer transition-colors shadow-sm select-none">
                                                                Cancel
                                                            </button>
                                                        </div>
                                                    </template>
                                                    <template x-if="quote.locateStatus !== 65">
                                                        <span class="text-[9px] text-zinc-400 italic" x-text="quote.text || '-'"></span>
                                                    </template>
                                                </td>
                                            </tr>
                                        </template>
                                        <template x-if="filteredHistoryList.length === 0">
                                            <tr>
                                                <td colspan="6" class="py-6 text-center text-zinc-400 font-sans">No matching locate quotes found.</td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column - Active Inventory -->
                    <div class="lg:col-span-5">
                        <div class="premium-card p-6 bg-white">
                            <div class="flex justify-between items-center pb-3 mb-4 select-none">
                                <h4 class="text-xs font-extrabold text-zinc-800 uppercase tracking-wider">Active Borrows Inventory</h4>
                                <span class="px-2 py-0.5 bg-blue-50 border border-blue-200 text-blue-600 text-[9px] font-bold rounded-lg" x-text="filteredInventoryList.length + ' Borrow(s)'"></span>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-xs border-collapse">
                                    <thead>
                                        <tr class="border-b border-zinc-200 text-zinc-500 text-[9px] font-extrabold uppercase tracking-widest">
                                            <th class="pb-2">Symbol</th>
                                            <th class="pb-2 text-right">Available</th>
                                            <th class="pb-2 text-right">Type</th>
                                            <th class="pb-2 text-right">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-zinc-100 font-mono text-zinc-650">
                                        <template x-for="inv in filteredInventoryList" :key="inv.quoteReqID">
                                            <tr class="hover:bg-zinc-50 transition-colors">
                                                <td class="py-3 font-sans font-black text-zinc-800">
                                                    <span x-text="inv.symbol"></span>
                                                    <span class="text-[8px] text-zinc-400 block" x-text="'$' + Number(inv.locatePrice).toFixed(4)"></span>
                                                </td>
                                                <td class="py-3 text-right font-black text-blue-600" x-text="inv.available"></td>
                                                <td class="py-3 text-right">
                                                    <span class="px-1.5 py-0.5 text-[8px] font-bold rounded uppercase border"
                                                          :class="inv.locateType === 4 ? 'border-orange-250 bg-orange-50 text-orange-600' : 'border-blue-250 bg-blue-50 text-blue-600'"
                                                          x-text="inv.locateType === 4 ? 'Single Use' : 'Pre-Borrow'"></span>
                                                </td>
                                                <td class="py-3 text-right">
                                                    <div class="flex gap-1 justify-end">
                                                        <button @click="prefillShortRedirect(inv.symbol, inv.available)" class="px-2 py-0.5 border border-orange-250 bg-orange-50 text-orange-600 hover:bg-orange-100 rounded text-[9px] font-bold cursor-pointer transition-colors shadow-sm select-none">
                                                            Prefill Short
                                                        </button>
                                                        <button @click="sellBackLocatePrompt(inv)" class="px-2 py-0.5 border border-rose-250 bg-rose-50 text-rose-600 hover:bg-rose-100 rounded text-[9px] font-bold cursor-pointer transition-colors shadow-sm select-none">
                                                            Return
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        </template>
                                        <template x-if="filteredInventoryList.length === 0">
                                            <tr>
                                                <td colspan="4" class="py-6 text-center text-zinc-400 font-sans">No matching active borrows.</td>
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

    <!-- NeoToast Notifications Component -->
    <script>
        if (!window.neoToast) {
            window.neoToast = function(message, type = 'info') {
                const toastContainer = document.getElementById('neotoast-container') || (() => {
                    const c = document.createElement('div');
                    c.id = 'neotoast-container';
                    c.className = 'fixed bottom-5 right-5 z-50 flex flex-col gap-2 pointer-events-none';
                    document.body.appendChild(c);
                    return c;
                })();

                const t = document.createElement('div');
                t.className = `px-4 py-3 rounded-xl border text-xs font-bold shadow-lg flex items-center gap-2.5 transition-all duration-300 transform translate-y-2 opacity-0 pointer-events-auto max-w-sm `;
                if (type === 'success') {
                    t.className += 'bg-emerald-50 border-emerald-250 text-emerald-700';
                    t.innerHTML = '<span>✅</span>' + message;
                } else if (type === 'error') {
                    t.className += 'bg-rose-50 border-rose-250 text-rose-700';
                    t.innerHTML = '<span>❌</span>' + message;
                } else {
                    t.className += 'bg-zinc-50 border-zinc-250 text-zinc-700';
                    t.innerHTML = '<span>ℹ️</span>' + message;
                }

                toastContainer.appendChild(t);
                setTimeout(() => {
                    t.classList.remove('translate-y-2', 'opacity-0');
                }, 10);

                setTimeout(() => {
                    t.classList.add('translate-y-2', 'opacity-0');
                    setTimeout(() => t.remove(), 300);
                }, 4000);
            };
        }
    </script>

    <script>
        function locatesPage() {
            return {
                sidebarCollapsed: false,
                accountName: '{{ $primaryAccount->name }}',
                
                locateSymbol: '',
                locateSymbolName: '',
                searchSymbolsList: [],
                searchSymbolsLoading: false,
                searchSymbolsOpen: false,
                searchSymbolsTimer: null,
                locateQuantity: 100,
                locateHistoryList: [],
                locateInventoryList: [],
                locateIsRequesting: false,

                // Filtering state
                filterSymbol: '',
                filterType: 'all',

                init() {
                    this.fetchLocates();

                    // Poll locates list every 5 seconds
                    setInterval(() => {
                        this.fetchLocates();
                    }, 5000);
                },

                searchSymbols() {
                    let q = this.locateSymbol.trim();
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

                // Filters computed helper lists
                get filteredHistoryList() {
                    const sym = this.filterSymbol.trim().toUpperCase();
                    return this.locateHistoryList.filter(quote => {
                        const matchesSym = sym === '' || quote.symbol.includes(sym);
                        const matchesType = this.filterType === 'all' || String(quote.locateType) === String(this.filterType);
                        return matchesSym && matchesType;
                    });
                },

                get filteredInventoryList() {
                    const sym = this.filterSymbol.trim().toUpperCase();
                    return this.locateInventoryList.filter(inv => {
                        const matchesSym = sym === '' || inv.symbol.includes(sym);
                        const matchesType = this.filterType === 'all' || String(inv.locateType) === String(this.filterType);
                        return matchesSym && matchesType;
                    });
                },

                fetchLocates() {
                    const accountParam = this.accountName ? `?account=${this.accountName}` : '';
                    
                    // Fetch history
                    fetch(`/broker/locate/history${accountParam}`)
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                this.locateHistoryList = data.locateHistory || [];
                            }
                        })
                        .catch(err => console.error('Error fetching history:', err));

                    // Fetch inventory
                    fetch(`/broker/locate/inventory${accountParam}`)
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                this.locateInventoryList = data.locateInventory || [];
                            }
                        })
                        .catch(err => console.error('Error fetching inventory:', err));
                },

                requestLocate() {
                    const sym = this.locateSymbol.trim().toUpperCase();
                    if (!sym) {
                        window.neoToast('Please enter a symbol', 'error');
                        return;
                    }
                    if (!this.locateQuantity || this.locateQuantity < 100) {
                        window.neoToast('Minimum locate quantity is 100', 'error');
                        return;
                    }

                    this.locateIsRequesting = true;
                    const body = {
                        symbol: sym,
                        quantity: this.locateQuantity,
                        account_id: this.accountName
                    };

                    fetch('/broker/locate/quote', {
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
                        if (!res.ok) throw new Error(data.message || 'Failed to request quote');
                        return data;
                    })
                    .then(data => {
                        if (data.success) {
                            window.neoToast(data.message || 'Locate quote requested!', 'success');
                            this.locateSymbol = '';
                            this.fetchLocates();
                        } else {
                            window.neoToast(data.message || 'Request failed', 'error');
                        }
                    })
                    .catch(err => {
                        window.neoToast(err.message || 'Server error', 'error');
                    })
                    .finally(() => {
                        this.locateIsRequesting = false;
                    });
                },

                acceptLocateQuote(quoteReqId) {
                    fetch('/broker/locate/accept', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            quote_req_id: quoteReqId,
                            account_id: this.accountName
                        })
                    })
                    .then(async res => {
                        const data = await res.json();
                        if (!res.ok) throw new Error(data.message || 'Failed to accept quote');
                        return data;
                    })
                    .then(data => {
                        if (data.success) {
                            window.neoToast('Locate quote accepted!', 'success');
                            this.fetchLocates();
                        } else {
                            window.neoToast(data.message || 'Failed to accept quote', 'error');
                        }
                    })
                    .catch(err => {
                        window.neoToast(err.message || 'Server error', 'error');
                    });
                },

                cancelLocateQuote(quoteReqId) {
                    fetch('/broker/locate/cancel', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            quote_req_id: quoteReqId,
                            account_id: this.accountName
                        })
                    })
                    .then(async res => {
                        const data = await res.json();
                        if (!res.ok) throw new Error(data.message || 'Failed to cancel quote');
                        return data;
                    })
                    .then(data => {
                        if (data.success) {
                            window.neoToast('Locate quote cancelled!', 'success');
                            this.fetchLocates();
                        } else {
                            window.neoToast(data.message || 'Failed to cancel quote', 'error');
                        }
                    })
                    .catch(err => {
                        window.neoToast(err.message || 'Server error', 'error');
                    });
                },

                sellBackLocatePrompt(inv) {
                    const inputQty = prompt(`Enter quantity of ${inv.symbol} to return (Max: ${inv.available}):`, inv.available);
                    if (inputQty === null) return; 

                    const qty = parseInt(inputQty);
                    if (isNaN(qty) || qty <= 0 || qty > inv.available) {
                        window.neoToast('Invalid return quantity entered', 'error');
                        return;
                    }

                    fetch('/broker/locate/sell-back', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            symbol: inv.symbol,
                            quantity: qty,
                            quote_req_id: inv.quoteReqID,
                            locate_type: inv.locateType,
                            account_id: this.accountName
                        })
                    })
                    .then(async res => {
                        const data = await res.json();
                        if (!res.ok) throw new Error(data.message || 'Failed to return locates');
                        return data;
                    })
                    .then(data => {
                        if (data.success) {
                            window.neoToast(`Returned ${qty} shares of ${inv.symbol}!`, 'success');
                            this.fetchLocates();
                        } else {
                            window.neoToast(data.message || 'Return failed', 'error');
                        }
                    })
                    .catch(err => {
                        window.neoToast(err.message || 'Server error', 'error');
                    });
                },

                prefillShortRedirect(symbol, quantity) {
                    // Redirect to trade page with prefill parameters
                    window.location.href = `/trade?prefill=true&symbol=${symbol}&quantity=${quantity}&side=Short&account=${this.accountName}`;
                }
            };
        }
    </script>
</x-app-layout>
