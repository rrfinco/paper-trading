<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="mb-6 text-center sm:text-left select-none">
        <h3 class="text-2xl font-black tracking-tight text-slate-900">Welcome Back</h3>
        <p class="text-xs text-slate-400 font-medium mt-1">Please enter your credentials to access your trading portal.</p>
    </div>

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <!-- Email Address -->
        <div class="space-y-1.5">
            <label for="email" class="text-[9px] font-extrabold uppercase tracking-wider text-slate-400">Email Address</label>
            <div class="relative">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs">✉</span>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                       class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-9 pr-4 py-2.5 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:border-indigo-600 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 transition-all font-semibold" 
                       placeholder="your@email.com" />
            </div>
            @if ($errors->has('email'))
                <p class="text-[10px] text-red-500 font-bold mt-1">{{ $errors->first('email') }}</p>
            @endif
        </div>

        <!-- Password -->
        <div class="space-y-1.5">
            <div class="flex justify-between items-center">
                <label for="password" class="text-[9px] font-extrabold uppercase tracking-wider text-slate-400">Password</label>
                @if (Route::has('password.request'))
                    <a class="text-[10px] text-slate-400 hover:text-indigo-600 transition-colors font-bold" href="{{ route('password.request') }}">
                        Forgot password?
                    </a>
                @endif
            </div>
            <div class="relative">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs">🔒</span>
                <input id="password" type="password" name="password" required autocomplete="current-password"
                       class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-9 pr-4 py-2.5 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:border-indigo-600 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 transition-all font-semibold" 
                       placeholder="••••••••" />
            </div>
            @if ($errors->has('password'))
                <p class="text-[10px] text-red-500 font-bold mt-1">{{ $errors->first('password') }}</p>
            @endif
        </div>

        <!-- Remember Me -->
        <div class="block select-none">
            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <input id="remember_me" type="checkbox" name="remember" 
                       class="rounded border-slate-200 bg-slate-50 text-indigo-600 shadow-sm focus:ring-indigo-500/25 focus:ring-offset-white cursor-pointer" />
                <span class="ms-2 text-xs text-slate-500 font-bold">{{ __('Remember me') }}</span>
            </label>
        </div>

        <!-- Submit actions -->
        <div class="pt-2 space-y-4">
            <button type="submit" 
                    class="w-full py-3 bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-700 hover:to-violet-700 text-white rounded-xl text-xs font-extrabold uppercase tracking-wider hover:shadow-lg hover:shadow-indigo-600/15 transition-all select-none duration-200 cursor-pointer flex items-center justify-center">
                Sign In
            </button>

            <div class="text-center pt-3 select-none border-t border-slate-100">
                <span class="text-xs text-slate-400 font-bold">New to our platform? </span>
                <a class="text-xs text-indigo-600 hover:text-indigo-700 hover:underline transition-colors font-black" href="{{ route('register') }}">
                    Create an Account
                </a>
            </div>
        </div>
    </form>
</x-guest-layout>
