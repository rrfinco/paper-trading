@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-zinc-200 bg-zinc-50/50 text-zinc-800 placeholder-zinc-400 focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20 focus:bg-white rounded-xl shadow-sm focus:outline-none transition-all']) }}>
