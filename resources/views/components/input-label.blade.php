@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-semibold text-[11px] uppercase tracking-wider text-zinc-400 mb-1.5']) }}>
    {{ $value ?? $slot }}
</label>
