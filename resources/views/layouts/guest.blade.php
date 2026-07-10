<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            body {
                font-family: 'Plus Jakarta Sans', sans-serif;
                background-color: #fafbfd;
                background-image: radial-gradient(#e2e8f0 1.5px, transparent 1.5px);
                background-size: 28px 28px;
            }
            h1, h2, h3, h4, h5, h6 {
                font-family: 'Outfit', sans-serif;
            }
        </style>
    </head>
    <body class="antialiased text-slate-800">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 relative overflow-hidden">
            <!-- Glowing background bubbles -->
            <div class="absolute top-1/3 left-1/3 -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] bg-gradient-to-tr from-indigo-500/10 to-violet-500/10 rounded-full blur-[100px] pointer-events-none"></div>
            <div class="absolute bottom-1/4 right-10 w-[400px] h-[400px] bg-gradient-to-tr from-sky-400/8 to-indigo-400/8 rounded-full blur-[80px] pointer-events-none"></div>
            
            <div class="relative z-10 flex flex-col items-center">
                <a href="/" class="flex items-center gap-2.5 mb-6 select-none">
                    <div class="h-10 w-10 rounded-xl bg-gradient-to-tr from-indigo-600 to-violet-700 flex items-center justify-center shadow-lg shadow-indigo-600/20">
                        <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                    </div>
                    <span class="text-xl font-black tracking-tight text-slate-900">Paper<span class="text-indigo-600">Trading</span><span class="text-[10px] font-extrabold px-1.5 py-0.5 rounded-md bg-indigo-50 text-indigo-600 border border-indigo-200/50 ml-1.5 uppercase">Pro</span></span>
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-1 px-8 py-8 bg-white border border-slate-200/80 shadow-2xl sm:rounded-3xl relative z-10">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
