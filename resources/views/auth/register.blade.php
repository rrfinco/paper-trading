<x-guest-layout>
    <div class="mb-6 text-center sm:text-left select-none">
        <h3 class="text-2xl font-black tracking-tight text-slate-900">Create Account</h3>
        <p class="text-xs text-slate-400 font-medium mt-1">Register your trading profile and configure credentials.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <!-- Name -->
        <div class="space-y-1.5">
            <label for="name" class="text-[9px] font-extrabold uppercase tracking-wider text-slate-400">FullName</label>
            <div class="relative">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs">👤</span>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                       class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-9 pr-4 py-2 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:border-indigo-600 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 transition-all font-semibold" 
                       placeholder="your name" />
            </div>
            @if ($errors->has('name'))
                <p class="text-[10px] text-red-500 font-bold mt-1">{{ $errors->first('name') }}</p>
            @endif
        </div>

        <!-- Email Address -->
        <div class="space-y-1.5">
            <label for="email" class="text-[9px] font-extrabold uppercase tracking-wider text-slate-400">Email Address</label>
            <div class="relative">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs">✉</span>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username"
                       class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-9 pr-4 py-2 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:border-indigo-600 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 transition-all font-semibold" 
                       placeholder="your@email.com" />
            </div>
            @if ($errors->has('email'))
                <p class="text-[10px] text-red-500 font-bold mt-1">{{ $errors->first('email') }}</p>
            @endif
        </div>

        <!-- Password -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="space-y-1.5">
                <label for="password" class="text-[9px] font-extrabold uppercase tracking-wider text-slate-400">Password</label>
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs">🔒</span>
                    <input id="password" type="password" name="password" required autocomplete="new-password"
                           class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-9 pr-4 py-2 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:border-indigo-600 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 transition-all font-semibold" 
                           placeholder="••••••••" />
                </div>
                @if ($errors->has('password'))
                    <p class="text-[10px] text-red-500 font-bold mt-1">{{ $errors->first('password') }}</p>
                @endif
            </div>

            <!-- Confirm Password -->
            <div class="space-y-1.5">
                <label for="password_confirmation" class="text-[9px] font-extrabold uppercase tracking-wider text-slate-400">Confirm Password</label>
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs">🔒</span>
                    <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                           class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-9 pr-4 py-2 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:border-indigo-600 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 transition-all font-semibold" 
                           placeholder="••••••••" />
                </div>
                @if ($errors->has('password_confirmation'))
                    <p class="text-[10px] text-red-500 font-bold mt-1">{{ $errors->first('password_confirmation') }}</p>
                @endif
            </div>
        </div>

        <!-- TradeZero Brokerage Onboarding -->
        <div class="pt-4 border-t border-slate-100 space-y-4">
            <div>
                <h3 class="text-xs font-black text-slate-800 flex items-center gap-1.5 select-none">
                    <span>🔑</span> TradeZero API Credentials
                </h3>
                <p class="text-[10px] text-slate-400 leading-normal mt-0.5 select-none">
                    To complete registration, connect your TradeZero API keys. Accounts and real-time balances will be verified and synced.
                </p>
            </div>

            <!-- Key ID -->
            <div class="space-y-1.5">
                <label for="tradezero_key_id" class="text-[9px] font-extrabold uppercase tracking-wider text-slate-400">TZ-API-KEY-ID</label>
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs">🔑</span>
                    <input id="tradezero_key_id" type="text" name="tradezero_key_id" value="{{ old('tradezero_key_id') }}" required
                           class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-9 pr-4 py-2 text-xs text-slate-800 font-semibold font-mono placeholder-slate-400 focus:outline-none focus:border-indigo-600 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 transition-all" 
                           placeholder="e.g. TZ-DEMO-TEST" />
                </div>
                @if ($errors->has('tradezero_key_id'))
                    <p class="text-[10px] text-red-500 font-bold mt-1">{{ $errors->first('tradezero_key_id') }}</p>
                @endif
            </div>

            <!-- Secret Key -->
            <div class="space-y-1.5">
                <label for="tradezero_secret_key" class="text-[9px] font-extrabold uppercase tracking-wider text-slate-400">TZ-API-SECRET-KEY</label>
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs">🔒</span>
                    <input id="tradezero_secret_key" type="password" name="tradezero_secret_key" required
                           class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-9 pr-4 py-2 text-xs text-slate-800 font-semibold font-mono placeholder-slate-400 focus:outline-none focus:border-indigo-600 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 transition-all" 
                           placeholder="e.g. TZ-SECRET-MOCK" />
                </div>
                @if ($errors->has('tradezero_secret_key'))
                    <p class="text-[10px] text-red-500 font-bold mt-1">{{ $errors->first('tradezero_secret_key') }}</p>
                @endif
            </div>

            <div class="p-3.5 bg-indigo-50/50 border border-indigo-100/50 rounded-2xl text-[10px] text-indigo-950 leading-relaxed select-none">
                <strong class="text-indigo-600 block mb-0.5">💡 Developer Sandbox Mode:</strong>
                Use credentials containing <code>demo</code>, <code>mock</code>, or <code>test</code> (e.g. <code>TZ-DEMO-TEST</code>) to automatically register with pre-configured paper accounts.
            </div>
        </div>

        <!-- Submit Actions -->
        <div class="pt-2 space-y-4">
            <button type="submit" 
                    class="w-full py-3 bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-700 hover:to-violet-700 text-white rounded-xl text-xs font-extrabold uppercase tracking-wider hover:shadow-lg hover:shadow-indigo-600/15 transition-all select-none duration-200 cursor-pointer flex items-center justify-center">
                Register Account
            </button>

            <div class="text-center pt-3 select-none border-t border-slate-100">
                <span class="text-xs text-slate-400 font-bold">Already registered? </span>
                <a class="text-xs text-indigo-600 hover:text-indigo-700 hover:underline transition-colors font-black" href="{{ route('login') }}">
                    Sign In
                </a>
            </div>
        </div>
    </form>
</x-guest-layout>
