<nav x-data="{ open: false }" class="bg-white/90 border-b border-zinc-200/80 sticky top-0 z-40 backdrop-blur-md">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                        <div class="h-9 w-9 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-700 flex items-center justify-center shadow-lg shadow-blue-600/20">
                            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                        </div>
                        <span class="text-xl font-black tracking-tight text-zinc-900">Trade<span class="text-blue-600">Zero</span></span>
                    </a>
                </div>
 
                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard', ['tab' => 'overview'])" :active="request()->routeIs('dashboard') && request('tab', 'overview') === 'overview'"
                        class="!text-zinc-600 hover:!text-blue-600 font-extrabold text-xs uppercase tracking-wider transition-all duration-150 {{ request()->routeIs('dashboard') && request('tab', 'overview') === 'overview' ? '!border-blue-600 !text-blue-600' : '!border-transparent' }}">
                        {{ __('Dashboard Overview') }}
                    </x-nav-link>
                </div>
            </div>
 
            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-4 py-2 border border-zinc-200 text-sm font-semibold rounded-xl text-zinc-700 bg-white hover:text-zinc-900 hover:border-zinc-300 focus:outline-none transition ease-in-out duration-150 cursor-pointer shadow-sm">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile Settings') }}
                        </x-dropdown-link>

                        <!-- Disconnect Broker Keys -->
                        @if(!is_null(Auth::user()->tradezero_key_id))
                            <form method="POST" action="{{ route('broker.disconnect') }}">
                                @csrf
                                <x-dropdown-link :href="route('broker.disconnect')"
                                        onclick="event.preventDefault();
                                                    this.closest('form').submit();"
                                        class="text-red-400 font-semibold hover:text-red-300">
                                    {{ __('Disconnect Broker') }}
                                </x-dropdown-link>
                            </form>
                        @endif

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard', ['tab' => 'overview'])" :active="request()->routeIs('dashboard') && request('tab', 'overview') === 'overview'"
                class="!text-zinc-700 hover:!text-blue-600 font-bold text-xs">
                {{ __('Dashboard Overview') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-zinc-200">
            <div class="px-4">
                <div class="font-medium text-base text-zinc-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-zinc-550">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile Settings') }}
                </x-responsive-nav-link>

                <!-- Disconnect Broker Keys -->
                @if(!is_null(Auth::user()->tradezero_key_id))
                    <form method="POST" action="{{ route('broker.disconnect') }}">
                        @csrf
                        <x-responsive-nav-link :href="route('broker.disconnect')"
                                onclick="event.preventDefault();
                                            this.closest('form').submit();"
                                class="text-red-600 font-bold">
                            {{ __('Disconnect Broker') }}
                        </x-responsive-nav-link>
                    </form>
                @endif

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
