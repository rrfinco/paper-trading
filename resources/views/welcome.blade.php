<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Paper Trading Pro - Premium Paper Trading & Broker Terminal</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #030206;
            background-image: 
                radial-gradient(at 0% 0%, rgba(99, 102, 241, 0.08) 0px, transparent 50%), 
                radial-gradient(at 50% 0%, rgba(6, 182, 212, 0.06) 0px, transparent 50%),
                radial-gradient(at 100% 0%, rgba(139, 92, 246, 0.08) 0px, transparent 50%),
                linear-gradient(rgba(255, 255, 255, 0.004) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.004) 1px, transparent 1px);
            background-size: 100% 100%, 100% 100%, 100% 100%, 32px 32px, 32px 32px;
        }
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Outfit', sans-serif;
        }
        .mono-font {
            font-family: 'JetBrains Mono', monospace;
        }
        .glow-effect {
            position: relative;
        }
        .glow-effect::after {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at 50% 30%, rgba(99, 102, 241, 0.12) 0%, transparent 70%);
            pointer-events: none;
            z-index: -1;
        }
        .glass-panel {
            background: rgba(10, 9, 16, 0.65);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
        .glass-card {
            background: rgba(18, 16, 28, 0.7);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.06);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .glass-card:hover {
            border-color: rgba(99, 102, 241, 0.3);
            box-shadow: 0 10px 30px -10px rgba(99, 102, 241, 0.15);
            transform: translateY(-2px);
        }
        .gradient-text {
            background: linear-gradient(135deg, #a5b4fc 0%, #818cf8 40%, #67e8f9 85%, #22d3ee 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .gradient-button {
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 50%, #06b6d4 100%);
            transition: all 0.3s ease;
        }
        .gradient-button:hover {
            opacity: 0.95;
            box-shadow: 0 0 25px rgba(99, 102, 241, 0.35);
        }
        
        @keyframes marquee {
            0% { transform: translateX(0%); }
            100% { transform: translateX(-50%); }
        }
        .animate-marquee {
            display: inline-flex;
            animation: marquee 25s linear infinite;
        }
        .animate-marquee:hover {
            animation-play-state: paused;
        }
        
        /* Shimmer effect */
        .shimmer {
            position: relative;
            overflow: hidden;
        }
        .shimmer::before {
            content: '';
            position: absolute;
            top: 0;
            left: -150%;
            width: 50%;
            height: 100%;
            background: linear-gradient(
                to right,
                transparent,
                rgba(255, 255, 255, 0.05),
                transparent
            );
            transform: skewX(-25deg);
            animation: shine 4s infinite;
        }
        @keyframes shine {
            100% { left: 150%; }
        }
    </style>
</head>
<body class="antialiased text-zinc-300 selection:bg-indigo-500 selection:text-white overflow-x-hidden min-h-screen flex flex-col">

    <!-- Top Live Ticker -->
    <div class="w-full bg-zinc-950/90 border-b border-zinc-900/60 overflow-hidden py-2 text-[11px] font-medium tracking-wide select-none z-50">
        <div class="flex whitespace-nowrap animate-marquee">
            <span class="mx-6 text-zinc-400 font-semibold flex items-center gap-1.5">AAPL <span class="text-emerald-400 font-bold font-mono">$214.32 (+1.45%)</span></span>
            <span class="mx-6 text-zinc-400 font-semibold flex items-center gap-1.5">TSLA <span class="text-rose-500 font-bold font-mono">$187.90 (-2.38%)</span></span>
            <span class="mx-6 text-zinc-400 font-semibold flex items-center gap-1.5">NVDA <span class="text-emerald-400 font-bold font-mono">$127.40 (+4.82%)</span></span>
            <span class="mx-6 text-zinc-400 font-semibold flex items-center gap-1.5">MSFT <span class="text-emerald-400 font-bold font-mono">$442.15 (+0.72%)</span></span>
            <span class="mx-6 text-zinc-400 font-semibold flex items-center gap-1.5">BTCUSD <span class="text-emerald-400 font-bold font-mono">$67,230 (+1.85%)</span></span>
            <span class="mx-6 text-zinc-400 font-semibold flex items-center gap-1.5">ETHUSD <span class="text-rose-500 font-bold font-mono">$3,485 (-0.42%)</span></span>
            <span class="mx-6 text-zinc-400 font-semibold flex items-center gap-1.5">AMZN <span class="text-emerald-400 font-bold font-mono">$189.08 (+1.12%)</span></span>
            <span class="mx-6 text-zinc-400 font-semibold flex items-center gap-1.5">GOOGL <span class="text-rose-500 font-bold font-mono">$176.45 (-0.95%)</span></span>
            <!-- Repeating elements for loop -->
            <span class="mx-6 text-zinc-400 font-semibold flex items-center gap-1.5">AAPL <span class="text-emerald-400 font-bold font-mono">$214.32 (+1.45%)</span></span>
            <span class="mx-6 text-zinc-400 font-semibold flex items-center gap-1.5">TSLA <span class="text-rose-500 font-bold font-mono">$187.90 (-2.38%)</span></span>
            <span class="mx-6 text-zinc-400 font-semibold flex items-center gap-1.5">NVDA <span class="text-emerald-400 font-bold font-mono">$127.40 (+4.82%)</span></span>
            <span class="mx-6 text-zinc-400 font-semibold flex items-center gap-1.5">MSFT <span class="text-emerald-400 font-bold font-mono">$442.15 (+0.72%)</span></span>
            <span class="mx-6 text-zinc-400 font-semibold flex items-center gap-1.5">BTCUSD <span class="text-emerald-400 font-bold font-mono">$67,230 (+1.85%)</span></span>
            <span class="mx-6 text-zinc-400 font-semibold flex items-center gap-1.5">ETHUSD <span class="text-rose-500 font-bold font-mono">$3,485 (-0.42%)</span></span>
            <span class="mx-6 text-zinc-400 font-semibold flex items-center gap-1.5">AMZN <span class="text-emerald-400 font-bold font-mono">$189.08 (+1.12%)</span></span>
            <span class="mx-6 text-zinc-400 font-semibold flex items-center gap-1.5">GOOGL <span class="text-rose-500 font-bold font-mono">$176.45 (-0.95%)</span></span>
        </div>
    </div>

    <!-- Header Navbar -->
    <header class="sticky top-0 z-40 glass-panel border-b border-zinc-900/60 bg-zinc-950/70">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-18 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="h-9 w-9 rounded-xl bg-gradient-to-tr from-indigo-600 to-cyan-500 flex items-center justify-center shadow-lg shadow-indigo-600/20">
                    <svg class="w-5.5 h-5.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                </div>
                <span class="text-lg font-black tracking-tight text-white select-none">
                    Paper<span class="text-indigo-400">Trading</span>
                    <span class="text-[9px] font-extrabold px-1.5 py-0.5 rounded-md bg-indigo-950 text-indigo-400 border border-indigo-800/40 ml-1.5 uppercase">Pro</span>
                </span>
            </div>

            <nav class="hidden md:flex items-center gap-6 text-[10px] font-extrabold uppercase tracking-widest text-zinc-400">
                <a href="#features" class="hover:text-white transition-colors">Features</a>
                <a href="#simulator" class="hover:text-white transition-colors">Simulators</a>
                <a href="#terminal-preview" class="hover:text-white transition-colors">Terminal UI</a>
                <a href="#locates-desk" class="hover:text-white transition-colors">Locates</a>
                <a href="#api-logs" class="hover:text-white transition-colors">API Logs</a>
                <a href="#pricing" class="hover:text-white transition-colors">Tiers</a>
            </nav>

            <div class="flex items-center gap-4">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="inline-flex items-center px-4 py-2 text-xs font-bold uppercase tracking-wide text-white bg-indigo-600 hover:bg-indigo-500 rounded-xl transition-all shadow-md shadow-indigo-600/10">
                            Launch Terminal
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-xs font-bold uppercase tracking-wider text-zinc-400 hover:text-white transition-colors">Log in</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="inline-flex items-center px-4 py-2.5 text-xs font-bold uppercase tracking-wider text-white gradient-button rounded-xl shadow-lg">
                                Create Account
                            </a>
                        @endif
                    @endauth
                @endif
            </div>
        </div>
    </header>

    <main class="flex-grow">
        <!-- Hero Section -->
        <section class="relative pt-20 pb-16 overflow-hidden glow-effect">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_20%_25%,rgba(99,102,241,0.06),transparent_40%)]"></div>
            <div class="absolute right-0 bottom-1/4 w-[500px] h-[500px] bg-indigo-900/5 rounded-full blur-[120px] pointer-events-none"></div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                    
                    <!-- Left: Text & Pitch -->
                    <div class="lg:col-span-5 text-center lg:text-left">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-950/40 border border-indigo-800/40 text-indigo-400 text-xs font-semibold mb-6 select-none">
                            <span class="w-1.5 h-1.5 rounded-full bg-cyan-400 animate-pulse"></span>
                            Pro Sandbox & Live API Bridge
                        </div>
                        
                        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black tracking-tight text-white mb-6 leading-tight">
                            Smart Paper Trading & Broker <span class="gradient-text">Execution</span>
                        </h1>
                        
                        <p class="text-sm sm:text-base text-zinc-400 mb-8 leading-relaxed max-w-lg mx-auto lg:mx-0">
                            A high-density trading workspace. Connect your TradeZero keys for live balances, advanced charting, and instant order routing, or trade risk-free using our integrated Developer Sandbox.
                        </p>
                        
                        <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4 mb-8">
                            <a href="{{ route('register') }}" class="w-full sm:w-auto px-6 py-3.5 text-xs font-bold uppercase tracking-wider text-white gradient-button rounded-xl text-center shadow-xl">
                                Start Paper Trading
                            </a>
                            <a href="#simulator" class="w-full sm:w-auto px-6 py-3.5 text-xs font-bold uppercase tracking-wider text-zinc-300 bg-zinc-900/80 hover:bg-zinc-800 border border-zinc-800/80 rounded-xl text-center transition-colors">
                                Try Live Demo
                            </a>
                        </div>
                        
                        <div class="grid grid-cols-3 gap-4 pt-6 border-t border-zinc-900 text-center lg:text-left select-none">
                            <div>
                                <div class="text-xl font-extrabold text-white font-mono">0.0s</div>
                                <div class="text-[9px] text-zinc-500 uppercase font-semibold mt-0.5">Execution Latency</div>
                            </div>
                            <div>
                                <div class="text-xl font-extrabold text-cyan-400 font-mono">$100K</div>
                                <div class="text-[9px] text-zinc-500 uppercase font-semibold mt-0.5">Virtual Cash</div>
                            </div>
                            <div>
                                <div class="text-xl font-extrabold text-indigo-400 font-mono">WebSocket</div>
                                <div class="text-[9px] text-zinc-500 uppercase font-semibold mt-0.5">Stream Sync</div>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Terminal Showcase -->
                    <div class="lg:col-span-7">
                        <div class="glass-panel p-3.5 sm:p-4 rounded-2xl border border-zinc-800/50 shadow-2xl relative shimmer">
                            <!-- Window header bar -->
                            <div class="flex items-center justify-between pb-3.5 border-b border-zinc-900 text-xs px-2 select-none">
                                <div class="flex items-center gap-1.5">
                                    <span class="w-2.5 h-2.5 rounded-full bg-rose-500/85"></span>
                                    <span class="w-2.5 h-2.5 rounded-full bg-yellow-500/85"></span>
                                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500/85"></span>
                                </div>
                                <div class="px-6 py-0.5 rounded-md bg-zinc-950/80 border border-zinc-900 text-[10px] text-zinc-500 font-mono">
                                    http://localhost:8090/trade
                                </div>
                                <div class="flex items-center gap-1">
                                    <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                    <span class="text-[9px] text-emerald-400 font-bold uppercase tracking-wider">Live Bridge</span>
                                </div>
                            </div>
                            
                            <!-- Terminal Mockup Panel -->
                            <div class="bg-[#0b0a11]/90 rounded-xl mt-3 p-4">
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4 select-none">
                                    <!-- Balance Card -->
                                    <div class="p-3.5 rounded-lg bg-zinc-900/40 border border-zinc-800/85">
                                        <div class="text-[9px] text-zinc-500 font-bold uppercase tracking-wider">Account Value</div>
                                        <div class="text-xl font-black text-white mt-1 mono-font" id="hero-equity">$100,000.00</div>
                                        <div class="text-[10px] text-emerald-400 font-bold mt-1.5 flex items-center gap-0.5" id="hero-day-pnl">
                                            ▲ +$0.00 (0.00%)
                                        </div>
                                    </div>
                                    <!-- Buying Power -->
                                    <div class="p-3.5 rounded-lg bg-zinc-900/40 border border-zinc-800/85">
                                        <div class="text-[9px] text-zinc-500 font-bold uppercase tracking-wider">Buying Power</div>
                                        <div class="text-xl font-black text-cyan-400 mt-1 mono-font" id="hero-bp">$400,000.00</div>
                                        <div class="text-[10px] text-zinc-500 font-semibold mt-1.5">4x Intraday Leverage</div>
                                    </div>
                                    <!-- Active Trades -->
                                    <div class="p-3.5 rounded-lg bg-zinc-900/40 border border-zinc-800/85">
                                        <div class="text-[9px] text-zinc-500 font-bold uppercase tracking-wider">Open Positions</div>
                                        <div class="text-xl font-black text-white mt-1 mono-font" id="hero-pos-count">0</div>
                                        <div class="text-[10px] text-zinc-500 font-semibold mt-1.5">Risk Exposure: $0.00</div>
                                    </div>
                                </div>

                                <!-- Dynamic SVG Line Chart -->
                                <div class="p-2.5 rounded-lg bg-zinc-950 border border-zinc-900/80 mb-4 relative">
                                    <div class="absolute top-2.5 left-3.5 flex items-center gap-1.5 text-[9px] font-bold text-zinc-500 uppercase tracking-wider select-none">
                                        <span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span>
                                        Equity Curve (Simulator)
                                    </div>
                                    <div class="h-28 flex items-end pt-3">
                                        <svg viewBox="0 0 500 120" class="w-full h-full text-indigo-400" preserveAspectRatio="none">
                                            <defs>
                                                <linearGradient id="glow-grad" x1="0" y1="0" x2="0" y2="1">
                                                    <stop offset="0%" stop-color="rgba(99, 102, 241, 0.25)"/>
                                                    <stop offset="100%" stop-color="rgba(99, 102, 241, 0)"/>
                                                </linearGradient>
                                            </defs>
                                            <path id="glow-path-fill" d="M0,80 L500,80 L500,120 L0,120 Z" fill="url(#glow-grad)"></path>
                                            <path id="glow-path-stroke" d="M0,80 L500,80" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"></path>
                                        </svg>
                                    </div>
                                </div>

                                <!-- Terminal Quick View Tab bar -->
                                <div class="flex items-center justify-between text-[10px] text-zinc-500 font-semibold border-b border-zinc-900 pb-2 select-none">
                                    <div class="flex gap-4">
                                        <span class="text-white border-b-2 border-indigo-500 pb-1.5">Positions Manager</span>
                                        <span>Order Logs</span>
                                        <span>Locates Desk</span>
                                    </div>
                                    <span class="text-[9px] bg-zinc-900 border border-zinc-800 text-zinc-400 px-2 py-0.5 rounded uppercase">Paper Account</span>
                                </div>

                                <!-- Simulated Positions Table -->
                                <div class="pt-2 text-[11px] overflow-hidden max-h-24 min-h-20 flex flex-col justify-center">
                                    <table class="w-full text-left" id="hero-positions-table">
                                        <thead>
                                            <tr class="text-zinc-600 font-bold uppercase tracking-wider text-[9px] select-none border-b border-zinc-900/40">
                                                <th class="py-1">Symbol</th>
                                                <th class="py-1 text-right">Shares</th>
                                                <th class="py-1 text-right">Avg Price</th>
                                                <th class="py-1 text-right">Current</th>
                                                <th class="py-1 text-right">P&L</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-zinc-900/20 font-medium" id="hero-positions-body">
                                            <tr id="empty-pos-row">
                                                <td colspan="5" class="py-4 text-center text-zinc-600 text-xs">No active simulated holdings. Use the Demo widget below to buy shares!</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- Interactive Simulator Widget Section -->
        <section id="simulator" class="py-16 border-y border-zinc-900/60 bg-zinc-950/40 relative">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="text-center max-w-2xl mx-auto mb-10">
                    <h2 class="text-2xl sm:text-3xl font-black tracking-tight text-white mb-3">Live Interactive Mock Trading</h2>
                    <p class="text-xs sm:text-sm text-zinc-400 leading-relaxed">
                        Practice executing orders inside this sandbox module. Prices fluctuate live, equity chart renders dynamically, and positions reconcile in real-time.
                    </p>
                </div>

                <div class="max-w-4xl mx-auto grid grid-cols-1 md:grid-cols-12 gap-6 items-stretch">
                    <!-- Ticket Controller Card -->
                    <div class="md:col-span-5 glass-card p-5 rounded-xl border border-zinc-800/80 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between pb-3 border-b border-zinc-900 select-none">
                                <span class="text-xs font-bold text-zinc-400 uppercase tracking-wide">Dynamic Order Entry</span>
                                <span class="h-2 w-2 rounded-full bg-indigo-500 animate-pulse"></span>
                            </div>

                            <!-- Buy / Sell Toggles -->
                            <div class="grid grid-cols-2 gap-2 mt-4 select-none">
                                <button onclick="setSimSide('BUY')" id="btn-buy" class="py-2 rounded-lg text-xs font-black uppercase tracking-wider text-black bg-indigo-400 transition-all">
                                    BUY (Long)
                                </button>
                                <button onclick="setSimSide('SELL')" id="btn-sell" class="py-2 rounded-lg text-xs font-black uppercase tracking-wider text-zinc-500 bg-zinc-900 border border-zinc-800 transition-all hover:bg-zinc-800">
                                    SELL (Short)
                                </button>
                            </div>

                            <!-- Form parameters -->
                            <div class="space-y-4 mt-5">
                                <div class="grid grid-cols-2 gap-4">
                                    <!-- Symbol Select -->
                                    <div class="space-y-1">
                                        <label class="text-[9px] font-bold text-zinc-500 uppercase tracking-wider">Ticker Symbol</label>
                                        <select id="sim-symbol" onchange="updateSimSymbol()" class="w-full bg-zinc-950 border border-zinc-800/80 rounded-lg text-xs text-white p-2.5 focus:outline-none focus:border-indigo-500 font-semibold font-mono">
                                            <option value="NVDA">NVDA</option>
                                            <option value="AAPL">AAPL</option>
                                            <option value="TSLA">TSLA</option>
                                            <option value="MSFT">MSFT</option>
                                            <option value="BTCUSD">BTCUSD</option>
                                        </select>
                                    </div>
                                    <!-- Quantity Input -->
                                    <div class="space-y-1">
                                        <label class="text-[9px] font-bold text-zinc-500 uppercase tracking-wider">Shares (Qty)</label>
                                        <input type="number" id="sim-qty" value="100" min="1" class="w-full bg-zinc-950 border border-zinc-800/80 rounded-lg text-xs text-white p-2.5 focus:outline-none focus:border-indigo-500 font-semibold font-mono text-center" />
                                    </div>
                                </div>

                                <!-- Current Market Data block -->
                                <div class="p-3 bg-zinc-950/80 border border-zinc-900 rounded-lg flex items-center justify-between text-xs select-none">
                                    <div class="space-y-0.5">
                                        <span class="text-[9px] text-zinc-500 uppercase font-bold tracking-wider">Last Traded Price</span>
                                        <div class="text-sm font-extrabold text-white tracking-tight font-mono" id="sim-ticker-val">$127.40</div>
                                    </div>
                                    <div class="text-right space-y-0.5">
                                        <span class="text-[9px] text-zinc-500 uppercase font-bold tracking-wider">Spread/Ask</span>
                                        <div class="text-xs text-zinc-400 font-mono" id="sim-ticker-ask">$127.42</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Submit Button -->
                        <div class="pt-4 border-t border-zinc-900 mt-6">
                            <button onclick="executeSimTrade()" class="w-full py-3.5 bg-indigo-500 hover:bg-indigo-400 text-white rounded-xl text-xs font-black uppercase tracking-wider hover:shadow-lg transition-all flex items-center justify-center gap-1.5 shadow-md shadow-indigo-600/10" id="sim-submit-btn">
                                Execute Mock BUY Order
                            </button>
                            
                            <!-- Custom interactive status feedback toasts -->
                            <div class="mt-2 text-[10px] text-center font-bold text-zinc-500 select-none h-4" id="sim-toast">
                                Click execute to submit orders.
                            </div>
                        </div>
                    </div>

                    <!-- Live feed watchlist/Market Desk -->
                    <div class="md:col-span-7 glass-card p-5 rounded-xl border border-zinc-800/80 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between pb-3 border-b border-zinc-900 select-none">
                                <span class="text-xs font-bold text-zinc-400 uppercase tracking-wide">Live Simulated Feed Watchlist</span>
                                <span class="text-[10px] text-cyan-400 font-bold uppercase tracking-wider font-mono" id="sim-clock">12:00:00 PM</span>
                            </div>
                            
                            <div class="space-y-2 mt-4">
                                <!-- AAPL Row -->
                                <div class="p-2.5 rounded-lg border border-zinc-900 bg-zinc-950/30 flex items-center justify-between text-xs hover:border-zinc-800 transition-colors">
                                    <div class="flex items-center gap-3 select-none">
                                        <span class="h-6 w-6 rounded bg-zinc-900 text-zinc-400 flex items-center justify-center font-black">🍎</span>
                                        <div>
                                            <div class="font-black text-white">AAPL</div>
                                            <div class="text-[9px] text-zinc-500 uppercase font-semibold">Apple Inc.</div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="font-extrabold text-white font-mono" id="row-price-AAPL">$214.32</div>
                                        <div class="text-[10px] text-emerald-400 font-bold font-mono" id="row-change-AAPL">+1.45%</div>
                                    </div>
                                </div>
                                <!-- TSLA Row -->
                                <div class="p-2.5 rounded-lg border border-zinc-900 bg-zinc-950/30 flex items-center justify-between text-xs hover:border-zinc-800 transition-colors">
                                    <div class="flex items-center gap-3 select-none">
                                        <span class="h-6 w-6 rounded bg-zinc-900 text-zinc-400 flex items-center justify-center font-black">⚡</span>
                                        <div>
                                            <div class="font-black text-white">TSLA</div>
                                            <div class="text-[9px] text-zinc-500 uppercase font-semibold">Tesla Motors</div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="font-extrabold text-white font-mono" id="row-price-TSLA">$187.90</div>
                                        <div class="text-[10px] text-rose-500 font-bold font-mono" id="row-change-TSLA">-2.38%</div>
                                    </div>
                                </div>
                                <!-- NVDA Row -->
                                <div class="p-2.5 rounded-lg border border-zinc-900 bg-zinc-950/30 flex items-center justify-between text-xs hover:border-zinc-800 transition-colors">
                                    <div class="flex items-center gap-3 select-none">
                                        <span class="h-6 w-6 rounded bg-zinc-900 text-zinc-400 flex items-center justify-center font-black">🟢</span>
                                        <div>
                                            <div class="font-black text-white">NVDA</div>
                                            <div class="text-[9px] text-zinc-500 uppercase font-semibold">Nvidia Corp.</div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="font-extrabold text-white font-mono" id="row-price-NVDA">$127.40</div>
                                        <div class="text-[10px] text-emerald-400 font-bold font-mono" id="row-change-NVDA">+4.82%</div>
                                    </div>
                                </div>
                                <!-- MSFT Row -->
                                <div class="p-2.5 rounded-lg border border-zinc-900 bg-zinc-950/30 flex items-center justify-between text-xs hover:border-zinc-800 transition-colors">
                                    <div class="flex items-center gap-3 select-none">
                                        <span class="h-6 w-6 rounded bg-zinc-900 text-zinc-400 flex items-center justify-center font-black">💻</span>
                                        <div>
                                            <div class="font-black text-white">MSFT</div>
                                            <div class="text-[9px] text-zinc-500 uppercase font-semibold">Microsoft Corp.</div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="font-extrabold text-white font-mono" id="row-price-MSFT">$442.15</div>
                                        <div class="text-[10px] text-emerald-400 font-bold font-mono" id="row-change-MSFT">+0.72%</div>
                                    </div>
                                </div>
                                <!-- BTCUSD Row -->
                                <div class="p-2.5 rounded-lg border border-zinc-900 bg-zinc-950/30 flex items-center justify-between text-xs hover:border-zinc-800 transition-colors">
                                    <div class="flex items-center gap-3 select-none">
                                        <span class="h-6 w-6 rounded bg-zinc-900 text-zinc-400 flex items-center justify-center font-black">₿</span>
                                        <div>
                                            <div class="font-black text-white">BTCUSD</div>
                                            <div class="text-[9px] text-zinc-500 uppercase font-semibold">Bitcoin Index</div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="font-extrabold text-white font-mono" id="row-price-BTCUSD">$67,230.00</div>
                                        <div class="text-[10px] text-emerald-400 font-bold font-mono" id="row-change-BTCUSD">+1.85%</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="p-3 bg-indigo-950/30 border border-indigo-900/30 rounded-lg text-[10px] text-indigo-300 leading-relaxed select-none mt-4">
                            <span class="font-extrabold text-indigo-400 block mb-0.5">🚀 Real Trading Accounts:</span>
                            Creating a real user profile lets you bind live TradeZero brokerage accounts via secure API pipelines, replacing this sandbox module with real-time exchange access.
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Interactive Level 2 & Locates Section -->
        <section id="locates-desk" class="py-16 border-b border-zinc-900/60 bg-zinc-950/20 relative">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="text-center max-w-2xl mx-auto mb-12">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-cyan-950/40 border border-cyan-800/40 text-cyan-400 text-xs font-semibold mb-4 select-none">
                        🔥 Locates & Level 2 Book
                    </div>
                    <h2 class="text-2xl sm:text-3xl font-black tracking-tight text-white mb-3">Locates Desk & Level 2 Depth</h2>
                    <p class="text-xs sm:text-sm text-zinc-400 leading-relaxed">
                        Query Hard-To-Borrow locate quotes or inspect Level 2 bid/ask ladders updating live to replicate dynamic brokerage setups.
                    </p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-stretch">
                    <!-- Left: Locates Checker -->
                    <div class="lg:col-span-6 glass-card p-6 rounded-xl border border-zinc-800/80 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center gap-2.5 pb-3.5 border-b border-zinc-900 select-none">
                                <span class="h-6 w-6 rounded bg-zinc-900 text-zinc-400 flex items-center justify-center font-bold">🔍</span>
                                <h3 class="text-sm font-black text-white">HTB Locates Query Desk</h3>
                            </div>
                            
                            <p class="text-[11px] text-zinc-400 mt-3 leading-relaxed select-none">
                                Test shorting availability: Enter standard tickers like <code>GME</code>, <code>AMC</code>, or <code>TSLA</code> to request active locate borrow quotes.
                            </p>

                            <div class="space-y-4 mt-5">
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="space-y-1">
                                        <label class="text-[9px] font-bold text-zinc-500 uppercase tracking-wider">Symbol to Borrow</label>
                                        <input type="text" id="locate-sym" value="GME" class="w-full bg-zinc-950 border border-zinc-800/80 rounded-lg text-xs text-white p-2.5 focus:outline-none focus:border-indigo-500 font-semibold font-mono uppercase text-center" />
                                    </div>
                                    <div class="space-y-1">
                                        <label class="text-[9px] font-bold text-zinc-500 uppercase tracking-wider">Requested Shares</label>
                                        <input type="number" id="locate-qty" value="500" min="100" step="100" class="w-full bg-zinc-950 border border-zinc-800/80 rounded-lg text-xs text-white p-2.5 focus:outline-none focus:border-indigo-500 font-semibold font-mono text-center" />
                                    </div>
                                </div>

                                <button onclick="requestMockLocate()" class="w-full py-3.5 bg-cyan-600 hover:bg-cyan-500 text-white rounded-xl text-xs font-black uppercase tracking-wider hover:shadow-lg transition-all flex items-center justify-center shadow-md shadow-cyan-600/10">
                                    Request Borrow Quote
                                </button>
                            </div>

                            <!-- Quote output area -->
                            <div class="p-4 rounded-lg bg-zinc-950 border border-zinc-900 mt-5 hidden" id="locate-result-panel">
                                <div class="flex justify-between text-xs border-b border-zinc-900/60 pb-2">
                                    <span class="font-bold text-white uppercase" id="res-symbol">GME</span>
                                    <span class="font-mono text-cyan-400 font-bold" id="res-status">HTB (Hard-to-Borrow)</span>
                                </div>
                                <div class="grid grid-cols-2 gap-4 pt-3 text-[11px] font-mono text-zinc-400">
                                    <div>Locate Rate: <span class="text-white font-bold" id="res-rate">$0.145 / share</span></div>
                                    <div>Total Cost: <span class="text-indigo-400 font-bold" id="res-cost">$72.50</span></div>
                                    <div>Availability: <span class="text-emerald-400 font-bold">High (35,000 available)</span></div>
                                    <div>Valid For: <span class="text-zinc-500">60 seconds</span></div>
                                </div>
                                <div class="mt-4 flex gap-2">
                                    <button onclick="acceptMockLocate()" class="flex-1 py-2 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold rounded-lg uppercase transition-colors">Accept Quote</button>
                                    <button onclick="cancelMockLocate()" class="py-2 px-3 bg-zinc-900 hover:bg-zinc-800 text-zinc-400 text-xs font-bold rounded-lg uppercase transition-colors">Cancel</button>
                                </div>
                            </div>
                        </div>

                        <!-- Locate Status Banner -->
                        <div class="mt-4 text-[10px] text-center font-bold text-zinc-500 select-none h-4" id="locate-toast">
                            Verify HTB borrow rates directly in seconds.
                        </div>
                    </div>

                    <!-- Right: Level 2 depth visualizer -->
                    <div class="lg:col-span-6 glass-card p-6 rounded-xl border border-zinc-800/80 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between pb-3.5 border-b border-zinc-900 select-none">
                                <div class="flex items-center gap-2.5">
                                    <span class="h-6 w-6 rounded bg-zinc-900 text-zinc-400 flex items-center justify-center font-bold">📊</span>
                                    <h3 class="text-sm font-black text-white">Level 2 Market Depth Ladder</h3>
                                </div>
                                <span class="text-[9px] text-zinc-500 font-mono" id="l2-symbol-header">Ticker: NVDA</span>
                            </div>

                            <div class="grid grid-cols-2 gap-4 mt-4 font-mono text-[10px]">
                                <!-- Bids Column -->
                                <div>
                                    <div class="text-[9px] font-bold text-zinc-500 uppercase tracking-wider select-none mb-1.5">Bids (Buy Orders)</div>
                                    <table class="w-full text-left">
                                        <thead>
                                            <tr class="text-zinc-600 font-bold border-b border-zinc-900/60">
                                                <th class="pb-1">MMID</th>
                                                <th class="pb-1 text-right">Price</th>
                                                <th class="pb-1 text-right">Size</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-zinc-900/10 font-medium" id="l2-bids-body">
                                            <!-- Dynamically populated -->
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Asks Column -->
                                <div>
                                    <div class="text-[9px] font-bold text-zinc-500 uppercase tracking-wider select-none mb-1.5">Asks (Sell Orders)</div>
                                    <table class="w-full text-left">
                                        <thead>
                                            <tr class="text-zinc-600 font-bold border-b border-zinc-900/60">
                                                <th class="pb-1">MMID</th>
                                                <th class="pb-1 text-right">Price</th>
                                                <th class="pb-1 text-right">Size</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-zinc-900/10 font-medium" id="l2-asks-body">
                                            <!-- Dynamically populated -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="p-3 bg-zinc-950/80 border border-zinc-900 rounded-lg text-[9px] text-zinc-500 leading-relaxed mt-5 select-none">
                            <strong class="text-indigo-400">💡 Level 2 Feeds:</strong> Market Makers dynamically shift quotes around the active simulator's price. Select a ticker above to pivot the book.
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Technical Analysis / Chart Indicators Showcase -->
        <section id="terminal-preview" class="py-16 border-b border-zinc-900/60 relative">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                    
                    <!-- Left: Detail Specs -->
                    <div class="lg:col-span-5 text-center lg:text-left">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-950/40 border border-indigo-800/40 text-indigo-400 text-xs font-semibold mb-6 select-none">
                            📈 Technical Workspace
                        </div>
                        <h2 class="text-3xl font-black text-white mb-6 leading-tight">Advanced Indicators & Custom Overlays</h2>
                        <p class="text-sm text-zinc-400 mb-6 leading-relaxed">
                            Integrate professional TradingView indicators and charts with complete responsive callbacks. Switch between candle intervals, map Moving Averages (EMA, SMA), overlays, and volume desks instantly.
                        </p>
                        <div class="space-y-4">
                            <div class="p-4 rounded-xl border border-zinc-900 bg-zinc-950/40 flex items-start gap-4">
                                <span class="h-8 w-8 rounded-lg bg-indigo-950 border border-indigo-800/60 text-indigo-400 flex items-center justify-center font-bold select-none">📊</span>
                                <div class="text-left">
                                    <h4 class="text-xs font-black text-white uppercase tracking-wider">Multi-Interval Candlesticks</h4>
                                    <p class="text-[11px] text-zinc-500 mt-1">Adjust chart bounds between 1m, 5m, 15m, Hourly, or Daily scales directly inside panels.</p>
                                </div>
                            </div>
                            <div class="p-4 rounded-xl border border-zinc-900 bg-zinc-950/40 flex items-start gap-4">
                                <span class="h-8 w-8 rounded-lg bg-indigo-950 border border-indigo-800/60 text-indigo-400 flex items-center justify-center font-bold select-none">🎯</span>
                                <div class="text-left">
                                    <h4 class="text-xs font-black text-white uppercase tracking-wider">Technical Oscillators</h4>
                                    <p class="text-[11px] text-zinc-500 mt-1">Plot relative strength indexes (RSI), MACD crossovers, Bollinger Bands, and support zones in real-time.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Indicators Chart Simulator -->
                    <div class="lg:col-span-7">
                        <div class="glass-card p-5 rounded-xl border border-zinc-800/80 relative overflow-hidden">
                            <div class="flex items-center justify-between pb-3 border-b border-zinc-900 select-none">
                                <div class="flex items-center gap-2">
                                    <span class="text-white font-extrabold text-xs" id="chart-show-symbol">NVDA</span>
                                    <span class="text-[10px] text-zinc-500 font-mono">1-Min Candles</span>
                                </div>
                                <!-- Chart Toggles -->
                                <div class="flex gap-2">
                                    <button onclick="toggleIndicator('VOL')" id="btn-chart-vol" class="px-2.5 py-1 rounded bg-indigo-600 text-white text-[9px] font-extrabold uppercase transition-colors">Volume</button>
                                    <button onclick="toggleIndicator('RSI')" id="btn-chart-rsi" class="px-2.5 py-1 rounded bg-zinc-900 text-zinc-400 text-[9px] font-extrabold uppercase transition-colors hover:bg-zinc-800">RSI Indicator</button>
                                    <button onclick="toggleIndicator('MA')" id="btn-chart-ma" class="px-2.5 py-1 rounded bg-zinc-900 text-zinc-400 text-[9px] font-extrabold uppercase transition-colors hover:bg-zinc-800">Moving Avg</button>
                                </div>
                            </div>

                            <!-- Candlestick Visual block -->
                            <div class="h-56 mt-4 relative bg-zinc-950/80 rounded-lg p-4 flex flex-col justify-between">
                                <!-- Main Candlestick Chart representation -->
                                <div class="h-32 w-full relative flex items-end justify-between px-2 pt-2 border-b border-zinc-900/60">
                                    <!-- Dynamic Candlestick Mock components (we can render dynamic SVG shapes) -->
                                    <div class="absolute inset-0 p-4 flex items-end">
                                        <svg viewBox="0 0 500 120" class="w-full h-full text-indigo-400" preserveAspectRatio="none">
                                            <!-- Chart Line indicator overlay -->
                                            <path id="ma-path-line" d="M 0 65 Q 125 50 250 85 T 500 25" fill="none" stroke="#22d3ee" stroke-width="1.5" class="hidden opacity-80" stroke-dasharray="4 3"></path>
                                            
                                            <!-- Mock candlesticks -->
                                            <g fill="#10b981" stroke="#10b981" stroke-width="1.5" id="chart-green-candles">
                                                <line x1="25" y1="20" x2="25" y2="80"></line>
                                                <rect x="18" y="30" width="14" height="40" rx="1"></rect>
                                                <line x1="125" y1="40" x2="125" y2="100"></line>
                                                <rect x="118" y="50" width="14" height="35" rx="1"></rect>
                                                <line x1="325" y1="10" x2="325" y2="90"></line>
                                                <rect x="318" y="20" width="14" height="50" rx="1"></rect>
                                                <line x1="425" y1="5" x2="425" y2="70"></line>
                                                <rect x="418" y="10" width="14" height="45" rx="1"></rect>
                                            </g>
                                            <g fill="#ef4444" stroke="#ef4444" stroke-width="1.5" id="chart-red-candles">
                                                <line x1="225" y1="45" x2="225" y2="110"></line>
                                                <rect x="218" y="60" width="14" height="30" rx="1"></rect>
                                                <line x1="475" y1="35" x2="475" y2="95"></line>
                                                <rect x="468" y="45" width="14" height="35" rx="1"></rect>
                                            </g>
                                        </svg>
                                    </div>
                                    <span class="absolute top-2 right-3 text-[8px] font-mono text-zinc-600 select-none">High: $129.50</span>
                                    <span class="absolute bottom-2 right-3 text-[8px] font-mono text-zinc-600 select-none">Low: $125.10</span>
                                </div>

                                <!-- Volume Bars Sub-Panel -->
                                <div class="h-10 w-full relative flex items-end justify-between px-2" id="chart-volume-panel">
                                    <div class="h-8 w-6 bg-emerald-500/30 rounded-t border-t border-emerald-500/50"></div>
                                    <div class="h-4 w-6 bg-emerald-500/30 rounded-t border-t border-emerald-500/50"></div>
                                    <div class="h-6 w-6 bg-rose-500/30 rounded-t border-t border-rose-500/50"></div>
                                    <div class="h-5 w-6 bg-emerald-500/30 rounded-t border-t border-emerald-500/50"></div>
                                    <div class="h-8 w-6 bg-emerald-500/30 rounded-t border-t border-emerald-500/50"></div>
                                    <div class="h-7 w-6 bg-rose-500/30 rounded-t border-t border-rose-500/50"></div>
                                    <span class="absolute top-1 left-2 text-[8px] font-mono text-zinc-600 select-none uppercase">Volume histogram</span>
                                </div>

                                <!-- RSI Sub-Panel -->
                                <div class="h-10 w-full relative hidden border-t border-zinc-900 bg-zinc-950/90 pt-1" id="chart-rsi-panel">
                                    <span class="absolute top-0.5 left-2 text-[8px] font-mono text-indigo-400 select-none">RSI (14): 58.42</span>
                                    <svg viewBox="0 0 500 30" class="w-full h-full text-indigo-400" preserveAspectRatio="none">
                                        <line x1="0" y1="9" x2="500" y2="9" stroke="rgba(255,255,255,0.05)" stroke-dasharray="2 2"></line>
                                        <line x1="0" y1="21" x2="500" y2="21" stroke="rgba(255,255,255,0.05)" stroke-dasharray="2 2"></line>
                                        <path d="M 0 15 Q 125 5 250 25 T 500 12" fill="none" stroke="rgba(129, 140, 248, 0.8)" stroke-width="1.5"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- Platform Modules & Core Features -->
        <section id="features" class="py-20 relative overflow-hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="text-center max-w-3xl mx-auto mb-16">
                    <h2 class="text-3xl font-black tracking-tight text-white mb-4">Ultimate SaaS Broker Features</h2>
                    <p class="text-sm text-zinc-400 leading-relaxed">
                        Replicating institutional terminal workflows with customizable charts, order desk widgets, locates tools, and secure multi-account dashboards.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Feature 1 -->
                    <div class="p-6 rounded-xl glass-card">
                        <div class="h-10 w-10 rounded-lg bg-indigo-950 border border-indigo-800/60 flex items-center justify-center text-indigo-400 mb-5 font-bold select-none">
                            🔒
                        </div>
                        <h3 class="text-base font-black text-white mb-2">Non-Custodial Bridge</h3>
                        <p class="text-zinc-400 text-xs leading-relaxed">
                            We act entirely as an execution wrapper. We do not process deposits, execute payouts, or hold user securities. You retain complete control of assets.
                        </p>
                    </div>

                    <!-- Feature 2 -->
                    <div class="p-6 rounded-xl glass-card">
                        <div class="h-10 w-10 rounded-lg bg-indigo-950 border border-indigo-800/60 flex items-center justify-center text-indigo-400 mb-5 font-bold select-none">
                            ⚡
                        </div>
                        <h3 class="text-base font-black text-white mb-2">Zero-Latency WebSockets</h3>
                        <p class="text-zinc-400 text-xs leading-relaxed">
                            Stream accounts metrics, working balances, fill notifications, and live chart triggers with automatic REST polling fallback if networks drop out.
                        </p>
                    </div>

                    <!-- Feature 3 -->
                    <div class="p-6 rounded-xl glass-card">
                        <div class="h-10 w-10 rounded-lg bg-indigo-950 border border-indigo-800/60 flex items-center justify-center text-indigo-400 mb-5 font-bold select-none">
                            🎮
                        </div>
                        <h3 class="text-base font-black text-white mb-2">Paper Trading Sandbox</h3>
                        <p class="text-zinc-400 text-xs leading-relaxed">
                            Practice trading strategies and order setups with virtual funds without risking actual account capital using predefined mock variables.
                        </p>
                    </div>

                    <!-- Feature 4 -->
                    <div class="p-6 rounded-xl glass-card">
                        <div class="h-10 w-10 rounded-lg bg-indigo-950 border border-indigo-800/60 flex items-center justify-center text-indigo-400 mb-5 font-bold select-none">
                            📊
                        </div>
                        <h3 class="text-base font-black text-white mb-2">Interactive TradingView</h3>
                        <p class="text-zinc-400 text-xs leading-relaxed">
                            Full TradingView candlestick integration that responds to active symbol clicks and autocomplete symbol searches automatically.
                        </p>
                    </div>

                    <!-- Feature 5 -->
                    <div class="p-6 rounded-xl glass-card">
                        <div class="h-10 w-10 rounded-lg bg-indigo-950 border border-indigo-800/60 flex items-center justify-center text-indigo-400 mb-5 font-bold select-none">
                            📈
                        </div>
                        <h3 class="text-base font-black text-white mb-2">Pro Locates Desk</h3>
                        <p class="text-zinc-400 text-xs leading-relaxed">
                            Request quotes for Hard-to-Borrow (HTB) assets directly on the locates panel. Review borrow prices, accept quotes, or list inventory.
                        </p>
                    </div>

                    <!-- Feature 6 -->
                    <div class="p-6 rounded-xl glass-card">
                        <div class="h-10 w-10 rounded-lg bg-indigo-950 border border-indigo-800/60 flex items-center justify-center text-indigo-400 mb-5 font-bold select-none">
                            👑
                        </div>
                        <h3 class="text-base font-black text-white mb-2">Admin Performance Desk</h3>
                        <p class="text-zinc-400 text-xs leading-relaxed">
                            A centralized administration control panel allowing managers to paginate traders, view account lists, locate costs, and check risk balances.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Real-time API Logs Console Terminal Section -->
        <section id="api-logs" class="py-16 border-t border-zinc-900/60 bg-zinc-950/30 relative">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                    
                    <!-- Left: Terminal window mockup -->
                    <div class="lg:col-span-7 order-last lg:order-first">
                        <div class="rounded-xl border border-zinc-800 bg-black/90 p-4 font-mono text-[10.5px] leading-relaxed shadow-2xl relative select-none">
                            <div class="absolute top-3.5 right-4 flex items-center gap-1.5 text-[8.5px] font-bold text-zinc-600 uppercase tracking-widest">
                                <span class="h-1.5 w-1.5 rounded-full bg-cyan-400 animate-ping"></span>
                                REST Gateway Output
                            </div>
                            <!-- Tab header -->
                            <div class="flex items-center gap-1.5 border-b border-zinc-900 pb-3 mb-3 text-zinc-500 font-bold uppercase tracking-wider text-[9px]">
                                <span class="h-2 w-2 rounded-full bg-zinc-800"></span>
                                API Connection Handshake logs
                            </div>
                            
                            <!-- Logs lines console -->
                            <div class="h-44 overflow-y-hidden space-y-1.5" id="api-logs-console">
                                <div class="text-zinc-500">[12:00:00] INITIALIZING TRADEZERO GATEWAY POOL...</div>
                                <div class="text-indigo-400">[12:00:01] POST https://webapi.tradezero.com/v1/api/token - 200 OK (110ms)</div>
                                <div class="text-zinc-400">[12:00:01] Token successfully mapped. Session context verified.</div>
                                <div class="text-emerald-400">[12:00:02] WS CONNECTED: wss://webapi.tradezero.com/relay - Established</div>
                                <div class="text-indigo-400">[12:00:02] GET https://webapi.tradezero.com/v1/api/accounts - 200 OK (85ms)</div>
                                <div class="text-zinc-400">[12:00:02] Discovered account: AccountID: TZ-DEMO-TEST | Margin Cap: 4.0x</div>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Description -->
                    <div class="lg:col-span-5 text-center lg:text-left">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-950/40 border border-indigo-800/40 text-indigo-400 text-xs font-semibold mb-6 select-none">
                            🔑 API Integration
                        </div>
                        <h2 class="text-3xl font-black text-white mb-6 leading-tight">Lightning REST & WebSocket Gateway</h2>
                        <p class="text-sm text-zinc-400 leading-relaxed mb-6">
                            Our backend framework interfaces directly with official TradeZero endpoints. Trade executions bypass third-party hops, processing purchases or locates directly through broker pipelines.
                        </p>
                        <div class="flex gap-6 justify-center lg:justify-start text-xs font-mono select-none">
                            <div>
                                <span class="block text-xl font-bold text-white">85ms</span>
                                <span class="text-[9px] text-zinc-500 uppercase font-semibold">Average REST Latency</span>
                            </div>
                            <div class="border-l border-zinc-900 pl-6">
                                <span class="block text-xl font-bold text-cyan-400">10ms</span>
                                <span class="text-[9px] text-zinc-500 uppercase font-semibold">WS Broadcast Time</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Account Tiers and Pricing Grid -->
        <section id="pricing" class="py-20 border-t border-zinc-900/60 bg-zinc-950/20 relative">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="text-center max-w-2xl mx-auto mb-16">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-950/40 border border-indigo-800/40 text-indigo-400 text-xs font-semibold mb-4 select-none">
                        💎 Platform Workspace Tiers
                    </div>
                    <h2 class="text-3xl font-black tracking-tight text-white mb-4">Choose Your Account Tier</h2>
                    <p class="text-sm text-zinc-400 leading-relaxed">
                        Start testing zero-risk strategies in the Developer Sandbox or connect live API gateways to unlock full routing channels.
                    </p>
                </div>

                <div class="max-w-4xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-8 items-stretch">
                    <!-- Sandbox Free Plan -->
                    <div class="glass-card p-8 rounded-2xl border border-zinc-800/65 flex flex-col justify-between relative overflow-hidden">
                        <div>
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="text-lg font-black text-white">Developer Sandbox</h3>
                                    <p class="text-[10px] text-zinc-500 uppercase font-bold tracking-wider mt-1">Practice & Strategy Test</p>
                                </div>
                                <span class="px-2.5 py-0.5 rounded-full bg-zinc-900 text-zinc-400 text-[9px] font-black uppercase tracking-wider select-none border border-zinc-800">Free Tier</span>
                            </div>
                            
                            <div class="my-6">
                                <span class="text-4xl font-extrabold text-white font-mono">$0</span>
                                <span class="text-xs text-zinc-500"> / forever</span>
                            </div>

                            <ul class="space-y-3.5 text-xs text-zinc-300 border-t border-zinc-900 pt-6">
                                <li class="flex items-center gap-3">
                                    <span class="text-indigo-400 font-bold">✔</span> $100,000 virtual cash paper balances
                                </li>
                                <li class="flex items-center gap-3">
                                    <span class="text-indigo-400 font-bold">✔</span> Real-time simulated watchlists
                                </li>
                                <li class="flex items-center gap-3">
                                    <span class="text-indigo-400 font-bold">✔</span> Local SVG charting tool
                                </li>
                                <li class="flex items-center gap-3">
                                    <span class="text-indigo-400 font-bold">✔</span> Fully operational order tickets (BUY/SELL)
                                </li>
                            </ul>
                        </div>

                        <div class="mt-8 pt-6 border-t border-zinc-900">
                            <a href="{{ route('register') }}" class="block w-full py-3 bg-zinc-900 hover:bg-zinc-800 border border-zinc-850 text-zinc-300 hover:text-white rounded-xl text-xs font-bold uppercase tracking-wider text-center transition-colors">
                                Launch Sandbox Free
                            </a>
                        </div>
                    </div>

                    <!-- Broker Connect Pro Plan -->
                    <div class="glass-card p-8 rounded-2xl border border-indigo-500/25 flex flex-col justify-between relative overflow-hidden">
                        <div class="absolute -top-3.5 -right-3.5 w-16 h-16 bg-indigo-500/10 rounded-full blur-xl pointer-events-none"></div>
                        
                        <div>
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="text-lg font-black text-white">Live Broker Connect</h3>
                                    <p class="text-[10px] text-indigo-400 uppercase font-bold tracking-wider mt-1">Live Account Integrations</p>
                                </div>
                                <span class="px-2.5 py-0.5 rounded-full bg-indigo-950 text-indigo-400 text-[9px] font-black uppercase tracking-wider select-none border border-indigo-800/40">PRO TIER</span>
                            </div>
                            
                            <div class="my-6">
                                <span class="text-4xl font-extrabold text-white font-mono">$29</span>
                                <span class="text-xs text-zinc-500"> / month</span>
                            </div>

                            <ul class="space-y-3.5 text-xs text-zinc-300 border-t border-zinc-900 pt-6">
                                <li class="flex items-center gap-3">
                                    <span class="text-indigo-400 font-bold">✔</span> Connect own TradeZero API credentials
                                </li>
                                <li class="flex items-center gap-3">
                                    <span class="text-indigo-400 font-bold">✔</span> Live REST synchronization with real-time polling
                                </li>
                                <li class="flex items-center gap-3">
                                    <span class="text-indigo-400 font-bold">✔</span> Real-time account balances & positions sync
                                </li>
                                <li class="flex items-center gap-3">
                                    <span class="text-indigo-400 font-bold">✔</span> Level 2 Market Depth & order desks
                                </li>
                                <li class="flex items-center gap-3">
                                    <span class="text-indigo-400 font-bold">✔</span> Integrated HTB Locates query and execution desk
                                </li>
                            </ul>
                        </div>

                        <div class="mt-8 pt-6 border-t border-zinc-900">
                            <a href="{{ route('register') }}" class="block w-full py-3 text-xs font-bold uppercase tracking-wider text-white gradient-button rounded-xl text-center shadow-lg">
                                Connect Live Broker
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Detailed Accordion FAQs -->
        <section class="py-16 border-t border-zinc-900/60 bg-zinc-950/30">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="text-center max-w-2xl mx-auto mb-12 select-none">
                    <h2 class="text-2xl sm:text-3xl font-black tracking-tight text-white mb-3">Frequently Asked Questions</h2>
                    <p class="text-xs sm:text-sm text-zinc-400 leading-relaxed">
                        Got questions about API security, paper accounts, or connections? We've compiled detailed details below.
                    </p>
                </div>

                <div class="space-y-4">
                    <!-- Q1 -->
                    <div class="p-4 rounded-xl border border-zinc-900 bg-zinc-950/40">
                        <h4 class="text-xs font-black text-white uppercase tracking-wider select-none">Is my TradeZero API Key secure on this platform?</h4>
                        <p class="text-[11px] text-zinc-400 mt-2 leading-relaxed">
                            Yes. All credentials, API Keys, and secret salts are encrypted at rest using industry-standard Laravel AES-256-CBC cipher blocks. Credentials are only decrypted temporarily client-side during API gateway execution handshakes.
                        </p>
                    </div>
                    <!-- Q2 -->
                    <div class="p-4 rounded-xl border border-zinc-900 bg-zinc-950/40">
                        <h4 class="text-xs font-black text-white uppercase tracking-wider select-none">Do I need a paid TradeZero account to register?</h4>
                        <p class="text-[11px] text-zinc-400 mt-2 leading-relaxed">
                            No. You can trade completely risk-free using our Sandbox configuration by entering mock keys containing <code>demo</code>, <code>mock</code>, or <code>test</code> (e.g. <code>TZ-DEMO-TEST</code>). This triggers pre-loaded virtual cash profiles.
                        </p>
                    </div>
                    <!-- Q3 -->
                    <div class="p-4 rounded-xl border border-zinc-900 bg-zinc-950/40">
                        <h4 class="text-xs font-black text-white uppercase tracking-wider select-none">What assets are supported for short locates?</h4>
                        <p class="text-[11px] text-zinc-400 mt-2 leading-relaxed">
                            Locates support all US-listed equities. Tickers categorized as Hard-To-Borrow (HTB) will retrieve active locate pricing quotes via the locate desk. Easy-To-Borrow (ETB) symbols bypass rates and can be shorted instantly.
                        </p>
                    </div>
                    <!-- Q4 -->
                    <div class="p-4 rounded-xl border border-zinc-900 bg-zinc-950/40">
                        <h4 class="text-xs font-black text-white uppercase tracking-wider select-none">Does the platform support options trading?</h4>
                        <p class="text-[11px] text-zinc-400 mt-2 leading-relaxed">
                            Currently, our dashboard focus lies on US stocks equity cash and margin trading (both long and short/cover actions). Advanced options configurations are scheduled for future roadmap releases.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Sandbox Developer Mode details -->
        <section id="sandbox" class="py-16 border-t border-zinc-900/60 bg-zinc-950/30">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="max-w-3xl mx-auto text-center">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-cyan-950/40 border border-cyan-800/40 text-cyan-400 text-xs font-semibold mb-6 select-none">
                        💡 Risk-Free Sandbox Mode
                    </div>
                    <h2 class="text-2xl sm:text-3xl font-black text-white mb-4">Zero-Capital Developer Accounts</h2>
                    <p class="text-sm text-zinc-400 leading-relaxed mb-6">
                        Don't have TradeZero API credentials? You can register and experience all terminal panels instantly using standard paper trading credentials.
                    </p>
                    <div class="p-5 bg-indigo-950/30 border border-indigo-900/30 rounded-2xl text-xs text-indigo-300 max-w-xl mx-auto leading-relaxed select-none">
                        During account registration, enter any TradeZero API Key containing <code>demo</code>, <code>mock</code>, or <code>test</code> (e.g. <code>TZ-DEMO-TEST</code>). Our service automatically triggers sandbox configs with virtual balances.
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="border-t border-zinc-900/60 bg-zinc-950 py-10 text-center text-[11px] text-zinc-500 font-medium select-none">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between gap-6">
            <p>© 2026 Paper Trading Platform. Developed under client-broker interface rules.</p>
            <p>Disclaimer: This platform is not a registered broker-dealer. Execution occurs via linked APIs owned by each client.</p>
        </div>
    </footer>

    <!-- Interactive Simulator JS Logic -->
    <script>
        // Prices state variables
        const symbolPrices = {
            AAPL: { price: 214.32, change: 1.45 },
            TSLA: { price: 187.90, change: -2.38 },
            NVDA: { price: 127.40, change: 4.82 },
            MSFT: { price: 442.15, change: 0.72 },
            BTCUSD: { price: 67230.00, change: 1.85 }
        };

        const mmids = ['NSDQ', 'NYSE', 'ARCA', 'BATS', 'EDGE'];

        let simSide = 'BUY';
        let simSelectedSymbol = 'NVDA';
        let simBalance = 100000.00;
        let simPositions = {};
        
        // Chart history tracking (starts at 100000)
        let equityHistory = [100000, 100000, 100000, 100000, 100000, 100000];
        let currentLocateQuote = null;

        // Technical Chart settings
        let chartIndicators = {
            VOL: true,
            RSI: false,
            MA: false
        };

        // Updates selected side (BUY/SELL)
        function setSimSide(side) {
            simSide = side;
            const btnBuy = document.getElementById('btn-buy');
            const btnSell = document.getElementById('btn-sell');
            const submitBtn = document.getElementById('sim-submit-btn');

            if (side === 'BUY') {
                btnBuy.className = "py-2 rounded-lg text-xs font-black uppercase tracking-wider text-black bg-indigo-400 transition-all";
                btnSell.className = "py-2 rounded-lg text-xs font-black uppercase tracking-wider text-zinc-500 bg-zinc-900 border border-zinc-800 transition-all hover:bg-zinc-800";
                submitBtn.innerText = "Execute Mock BUY Order";
                submitBtn.className = "w-full py-3.5 bg-indigo-500 hover:bg-indigo-400 text-white rounded-xl text-xs font-black uppercase tracking-wider hover:shadow-lg transition-all flex items-center justify-center gap-1.5 shadow-md shadow-indigo-600/10";
            } else {
                btnSell.className = "py-2 rounded-lg text-xs font-black uppercase tracking-wider text-black bg-indigo-400 transition-all";
                btnBuy.className = "py-2 rounded-lg text-xs font-black uppercase tracking-wider text-zinc-500 bg-zinc-900 border border-zinc-800 transition-all hover:bg-zinc-800";
                submitBtn.innerText = "Execute Mock SELL Order";
                submitBtn.className = "w-full py-3.5 bg-rose-600 hover:bg-rose-500 text-white rounded-xl text-xs font-black uppercase tracking-wider hover:shadow-lg transition-all flex items-center justify-center gap-1.5 shadow-md shadow-rose-600/10";
            }
        }

        // Ticker Change update
        function updateSimSymbol() {
            simSelectedSymbol = document.getElementById('sim-symbol').value;
            document.getElementById('l2-symbol-header').innerText = `Ticker: ${simSelectedSymbol}`;
            document.getElementById('chart-show-symbol').innerText = simSelectedSymbol;
            renderTickerView();
            renderLevel2();
        }

        function renderTickerView() {
            const data = symbolPrices[simSelectedSymbol];
            document.getElementById('sim-ticker-val').innerText = `$${data.price.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
            document.getElementById('sim-ticker-ask').innerText = `$${(data.price + 0.02).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
        }

        // Execute Mock Trade
        function executeSimTrade() {
            const qty = parseInt(document.getElementById('sim-qty').value);
            if (isNaN(qty) || qty <= 0) {
                showSimToast("Please enter a valid shares quantity.", "text-rose-400");
                return;
            }

            const currentPrice = symbolPrices[simSelectedSymbol].price;
            const cost = currentPrice * qty;

            if (simSide === 'BUY') {
                if (cost > simBalance) {
                    showSimToast("Insufficient sandbox balance for this trade.", "text-rose-400");
                    return;
                }

                // Add to balance
                simBalance -= cost;
                
                // Add to positions
                if (simPositions[simSelectedSymbol]) {
                    const existing = simPositions[simSelectedSymbol];
                    const totalQty = existing.qty + qty;
                    const totalCost = (existing.avgPrice * existing.qty) + cost;
                    existing.qty = totalQty;
                    existing.avgPrice = totalCost / totalQty;
                } else {
                    simPositions[simSelectedSymbol] = {
                        symbol: simSelectedSymbol,
                        qty: qty,
                        avgPrice: currentPrice
                    };
                }

                showSimToast(`Bought ${qty} shares of ${simSelectedSymbol} at $${currentPrice.toFixed(2)}`, "text-emerald-400");
                appendApiLog(`POST /v1/api/accounts/orders - Executed Market BUY ${qty} shares of ${simSelectedSymbol} at $${currentPrice.toFixed(2)}`);
            } else { // SELL (Short cover or selling shares)
                const position = simPositions[simSelectedSymbol];
                if (!position || position.qty < qty) {
                    showSimToast(`Insufficient shares of ${simSelectedSymbol} to execute sell.`, "text-rose-400");
                    return;
                }

                simBalance += cost;
                position.qty -= qty;

                if (position.qty === 0) {
                    delete simPositions[simSelectedSymbol];
                }

                showSimToast(`Sold ${qty} shares of ${simSelectedSymbol} at $${currentPrice.toFixed(2)}`, "text-rose-400");
                appendApiLog(`POST /v1/api/accounts/orders - Executed Market SELL ${qty} shares of ${simSelectedSymbol} at $${currentPrice.toFixed(2)}`);
            }

            recalculateEquity();
            renderPositions();
        }

        // Show execution feed log
        function showSimToast(text, colorClass) {
            const toastEl = document.getElementById('sim-toast');
            toastEl.innerText = text;
            toastEl.className = `mt-2 text-[10px] text-center font-bold select-none h-4 ${colorClass}`;
            setTimeout(() => {
                if (toastEl.innerText === text) {
                    toastEl.innerText = "Click execute to submit orders.";
                    toastEl.className = "mt-2 text-[10px] text-center font-bold text-zinc-500 select-none h-4";
                }
            }, 3000);
        }

        // Render Open positions
        function renderPositions() {
            const tbody = document.getElementById('hero-positions-body');
            tbody.innerHTML = '';

            const keys = Object.keys(simPositions);
            if (keys.length === 0) {
                tbody.innerHTML = `
                    <tr id="empty-pos-row">
                        <td colspan="5" class="py-4 text-center text-zinc-600 text-xs">No active simulated holdings. Use the Demo widget below to buy shares!</td>
                    </tr>`;
                document.getElementById('hero-pos-count').innerText = '0';
                return;
            }

            let activeCount = 0;
            keys.forEach(symbol => {
                const pos = simPositions[symbol];
                const curPrice = symbolPrices[symbol].price;
                const pnl = (curPrice - pos.avgPrice) * pos.qty;
                const pnlClass = pnl >= 0 ? 'text-emerald-400' : 'text-rose-500';
                const pnlPrefix = pnl >= 0 ? '+' : '';

                activeCount += pos.qty;

                const tr = document.createElement('tr');
                tr.className = "border-b border-zinc-900/40 text-zinc-300";
                tr.innerHTML = `
                    <td class="py-2.5 font-black font-mono text-white">${symbol}</td>
                    <td class="py-2.5 text-right font-mono">${pos.qty}</td>
                    <td class="py-2.5 text-right font-mono text-zinc-500">$${pos.avgPrice.toFixed(2)}</td>
                    <td class="py-2.5 text-right font-mono text-indigo-400">$${curPrice.toFixed(2)}</td>
                    <td class="py-2.5 text-right font-mono font-bold ${pnlClass}">${pnlPrefix}$${pnl.toFixed(2)}</td>
                `;
                tbody.appendChild(tr);
            });

            document.getElementById('hero-pos-count').innerText = keys.length.toString();
        }

        // Render level 2 ladders
        function renderLevel2() {
            const bidsBody = document.getElementById('l2-bids-body');
            const asksBody = document.getElementById('l2-asks-body');
            
            if (!bidsBody || !asksBody) return;

            bidsBody.innerHTML = '';
            asksBody.innerHTML = '';

            const basePrice = symbolPrices[simSelectedSymbol].price;
            
            // Build 5 rows of bids
            mmids.forEach((mm, i) => {
                const price = basePrice - (i * 0.01 + 0.01);
                const size = Math.floor(Math.random() * 20) + 1;
                
                const tr = document.createElement('tr');
                tr.className = "border-b border-zinc-900/10 text-zinc-400";
                tr.innerHTML = `
                    <td class="py-1.5 font-bold text-zinc-500">${mm}</td>
                    <td class="py-1.5 text-right font-mono text-emerald-400">$${price.toFixed(2)}</td>
                    <td class="py-1.5 text-right font-mono">${size * 100}</td>
                `;
                bidsBody.appendChild(tr);
            });

            // Build 5 rows of asks
            [...mmids].reverse().forEach((mm, i) => {
                const price = basePrice + (i * 0.01 + 0.01);
                const size = Math.floor(Math.random() * 20) + 1;
                
                const tr = document.createElement('tr');
                tr.className = "border-b border-zinc-900/10 text-zinc-400";
                tr.innerHTML = `
                    <td class="py-1.5 font-bold text-zinc-500">${mm}</td>
                    <td class="py-1.5 text-right font-mono text-rose-500">$${price.toFixed(2)}</td>
                    <td class="py-1.5 text-right font-mono">${size * 100}</td>
                `;
                asksBody.appendChild(tr);
            });
        }

        // Request Locate Quote
        function requestMockLocate() {
            const sym = document.getElementById('locate-sym').value.toUpperCase().trim();
            const qty = parseInt(document.getElementById('locate-qty').value);
            
            if (!sym) {
                showLocateToast("Please specify a symbol to query locates.", "text-rose-400");
                return;
            }
            if (isNaN(qty) || qty <= 0) {
                showLocateToast("Please specify requested share bounds.", "text-rose-400");
                return;
            }

            // Simulate locate availability
            const isHtb = sym === 'GME' || sym === 'AMC' || sym === 'KOSS' || sym === 'SPY' || Math.random() > 0.5;
            
            const panel = document.getElementById('locate-result-panel');
            panel.classList.remove('hidden');

            const rate = isHtb ? (Math.random() * 0.25 + 0.05) : 0.00;
            const cost = rate * qty;

            document.getElementById('res-symbol').innerText = sym;
            document.getElementById('res-status').innerText = isHtb ? "HTB (Hard-to-Borrow)" : "ETB (Easy-to-Borrow)";
            document.getElementById('res-status').className = isHtb ? "font-mono text-cyan-400 font-bold" : "font-mono text-emerald-400 font-bold";
            document.getElementById('res-rate').innerText = isHtb ? `$${rate.toFixed(3)} / share` : "Free / No locate needed";
            document.getElementById('res-cost').innerText = isHtb ? `$${cost.toFixed(2)}` : "$0.00";

            currentLocateQuote = { symbol: sym, qty: qty, cost: cost, isHtb: isHtb };
            
            showLocateToast(`Retrieved locate parameters for ${sym}.`, "text-cyan-400");
            appendApiLog(`POST /v1/api/locate/quote - Requested borrow for ${qty} shares of ${sym}. Rate matches: $${rate.toFixed(3)}`);
        }

        function acceptMockLocate() {
            if (!currentLocateQuote) return;
            const quote = currentLocateQuote;
            
            if (quote.cost > simBalance) {
                showLocateToast("Insufficient balance to buy locates.", "text-rose-400");
                return;
            }

            simBalance -= quote.cost;
            showLocateToast(`Locate accepted! Secured ${quote.qty} shares of ${quote.symbol}.`, "text-emerald-400");
            appendApiLog(`POST /v1/api/locate/accept - Reserved borrow inventory for ${quote.qty} shares of ${quote.symbol} | Cost: $${quote.cost.toFixed(2)}`);
            
            document.getElementById('locate-result-panel').classList.add('hidden');
            currentLocateQuote = null;
            recalculateEquity();
        }

        function cancelMockLocate() {
            document.getElementById('locate-result-panel').classList.add('hidden');
            showLocateToast("Locate quote cancelled.", "text-zinc-500");
            currentLocateQuote = null;
        }

        function showLocateToast(text, colorClass) {
            const toastEl = document.getElementById('locate-toast');
            toastEl.innerText = text;
            toastEl.className = `mt-4 text-[10px] text-center font-bold select-none h-4 ${colorClass}`;
        }

        // Live API console logs simulator
        function appendApiLog(text) {
            const consoleEl = document.getElementById('api-logs-console');
            if (!consoleEl) return;
            const now = new Date();
            const timeStr = now.toTimeString().split(' ')[0];
            
            const logLine = document.createElement('div');
            logLine.className = "text-indigo-400 transition-all";
            logLine.innerText = `[${timeStr}] ${text}`;
            consoleEl.appendChild(logLine);
            
            // Keep logs to max 12
            if (consoleEl.children.length > 8) {
                consoleEl.removeChild(consoleEl.children[0]);
            }
        }

        // Flashing randomized API background logs
        function simulateBackgroundLogs() {
            const randomEndpoints = [
                () => `GET https://webapi.tradezero.com/v1/api/accounts/positions - 200 OK (${Math.floor(Math.random() * 50) + 40}ms)`,
                () => `GET https://webapi.tradezero.com/v1/api/routes - 200 OK (${Math.floor(Math.random() * 40) + 30}ms)`,
                () => `WS RECV: {"type":"quote_update","symbol":"${simSelectedSymbol}","bid":${(symbolPrices[simSelectedSymbol].price - 0.01).toFixed(2)},"ask":${(symbolPrices[simSelectedSymbol].price + 0.01).toFixed(2)}}`,
                () => `GET https://webapi.tradezero.com/v1/api/locate/inventory - 200 OK (${Math.floor(Math.random() * 80) + 50}ms)`
            ];

            setInterval(() => {
                const index = Math.floor(Math.random() * randomEndpoints.length);
                const log = randomEndpoints[index]();
                
                const consoleEl = document.getElementById('api-logs-console');
                if (!consoleEl) return;
                
                const now = new Date();
                const timeStr = now.toTimeString().split(' ')[0];
                
                const logLine = document.createElement('div');
                logLine.className = log.startsWith('WS') ? "text-zinc-500" : "text-zinc-400";
                logLine.innerText = `[${timeStr}] ${log}`;
                consoleEl.appendChild(logLine);
                
                if (consoleEl.children.length > 8) {
                    consoleEl.removeChild(consoleEl.children[0]);
                }
            }, 5000);
        }

        // Toggle indicators on mock chart
        function toggleIndicator(type) {
            const btnVol = document.getElementById('btn-chart-vol');
            const btnRsi = document.getElementById('btn-chart-rsi');
            const btnMa = document.getElementById('btn-chart-ma');
            
            const rsiPanel = document.getElementById('chart-rsi-panel');
            const volPanel = document.getElementById('chart-volume-panel');
            const maLine = document.getElementById('ma-path-line');

            if (type === 'VOL') {
                chartIndicators.VOL = !chartIndicators.VOL;
                btnVol.className = chartIndicators.VOL ? "px-2.5 py-1 rounded bg-indigo-600 text-white text-[9px] font-extrabold uppercase transition-colors" : "px-2.5 py-1 rounded bg-zinc-900 text-zinc-400 text-[9px] font-extrabold uppercase transition-colors hover:bg-zinc-800";
                if (chartIndicators.VOL) volPanel.classList.remove('hidden');
                else volPanel.classList.add('hidden');
            } else if (type === 'RSI') {
                chartIndicators.RSI = !chartIndicators.RSI;
                btnRsi.className = chartIndicators.RSI ? "px-2.5 py-1 rounded bg-indigo-600 text-white text-[9px] font-extrabold uppercase transition-colors" : "px-2.5 py-1 rounded bg-zinc-900 text-zinc-400 text-[9px] font-extrabold uppercase transition-colors hover:bg-zinc-800";
                if (chartIndicators.RSI) rsiPanel.classList.remove('hidden');
                else rsiPanel.classList.add('hidden');
            } else if (type === 'MA') {
                chartIndicators.MA = !chartIndicators.MA;
                btnMa.className = chartIndicators.MA ? "px-2.5 py-1 rounded bg-indigo-600 text-white text-[9px] font-extrabold uppercase transition-colors" : "px-2.5 py-1 rounded bg-zinc-900 text-zinc-400 text-[9px] font-extrabold uppercase transition-colors hover:bg-zinc-800";
                if (chartIndicators.MA) maLine.classList.remove('hidden');
                else maLine.classList.add('hidden');
            }
        }

        // Live prices fluctuation
        function fluctuatePrices() {
            Object.keys(symbolPrices).forEach(sym => {
                const item = symbolPrices[sym];
                // Random fluctuation (-0.3% to +0.35%)
                const changePct = (Math.random() * 0.65 - 0.3) / 100;
                item.price = item.price * (1 + changePct);
                
                // Keep prices positive
                if (item.price < 1) item.price = 1;

                // Update ticker changes
                const sign = Math.random() > 0.45 ? 1 : -1;
                const displayChange = (Math.random() * 5).toFixed(2);
                item.change = parseFloat((sign * displayChange));

                // Update views
                const rowPriceEl = document.getElementById(`row-price-${sym}`);
                const rowChangeEl = document.getElementById(`row-change-${sym}`);

                if (rowPriceEl) {
                    const decimals = sym === 'BTCUSD' ? 2 : 2;
                    rowPriceEl.innerText = `$${item.price.toLocaleString(undefined, {minimumFractionDigits: decimals, maximumFractionDigits: decimals})}`;
                    
                    const signChar = item.change >= 0 ? '+' : '';
                    rowChangeEl.innerText = `${signChar}${item.change.toFixed(2)}%`;
                    rowChangeEl.className = item.change >= 0 ? 'text-[10px] text-emerald-400 font-bold font-mono' : 'text-[10px] text-rose-500 font-bold font-mono';
                }
            });

            renderTickerView();
            recalculateEquity();
            renderPositions();
            renderLevel2();
        }

        // Recalculates Net Equity (Balance + Position Values)
        function recalculateEquity() {
            let holdingsVal = 0;
            let initialCost = 0;
            
            Object.keys(simPositions).forEach(sym => {
                const pos = simPositions[sym];
                const curPrice = symbolPrices[sym].price;
                holdingsVal += curPrice * pos.qty;
                initialCost += pos.avgPrice * pos.qty;
            });

            const currentEquity = simBalance + holdingsVal;
            const dayPnl = currentEquity - 100000.00;
            const dayPnlPct = (dayPnl / 100000.00) * 100;
            
            // Format views
            document.getElementById('hero-equity').innerText = `$${currentEquity.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
            document.getElementById('hero-bp').innerText = `$${(currentEquity * 4).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
            
            const pnlPrefix = dayPnl >= 0 ? '▲ +' : '▼ ';
            const pnlClass = dayPnl >= 0 ? 'text-[10px] text-emerald-400 font-bold mt-1.5 flex items-center gap-0.5' : 'text-[10px] text-rose-500 font-bold mt-1.5 flex items-center gap-0.5';
            if (document.getElementById('hero-day-pnl')) {
                document.getElementById('hero-day-pnl').innerText = `${pnlPrefix}$${Math.abs(dayPnl).toFixed(2)} (${dayPnlPct.toFixed(2)}%)`;
                document.getElementById('hero-day-pnl').className = pnlClass;
            }

            // Log history
            equityHistory.push(currentEquity);
            if (equityHistory.length > 25) {
                equityHistory.shift();
            }
            renderEquityChart();
        }

        // Render dynamic SVG path for chart
        function renderEquityChart() {
            const strokePath = document.getElementById('glow-path-stroke');
            const fillPath = document.getElementById('glow-path-fill');
            
            if (!strokePath || !fillPath) return;

            const N = equityHistory.length;
            const maxVal = Math.max(...equityHistory);
            const minVal = Math.min(...equityHistory);
            const range = maxVal - minVal || 1.0;
            
            // Padding
            const pad = range * 0.15;
            const high = maxVal + pad;
            const low = minVal - pad;

            const points = equityHistory.map((val, idx) => {
                const x = (idx / (N - 1)) * 500;
                // invert y because svg 0 is top
                const y = 100 - ((val - low) / (high - low)) * 80;
                return `${x},${y}`;
            });

            const strokeD = `M ${points.join(' L ')}`;
            const fillD = `${strokeD} L 500,120 L 0,120 Z`;

            strokePath.setAttribute('d', strokeD);
            fillPath.setAttribute('d', fillD);
        }

        // Live Clock ticking
        function startClock() {
            setInterval(() => {
                const clockEl = document.getElementById('sim-clock');
                if (clockEl) {
                    clockEl.innerText = new Date().toLocaleTimeString();
                }
            }, 1000);
        }

        // Loop setups
        startClock();
        setInterval(fluctuatePrices, 1500);
        simulateBackgroundLogs();
        renderTickerView();
        renderLevel2();
        renderEquityChart();
    </script>
</body>
</html>
