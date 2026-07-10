<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center px-5 py-2.5 bg-blue-600 border border-transparent rounded-xl font-bold text-sm text-white hover:bg-blue-700 active:bg-blue-800 focus:outline-none transition ease-in-out duration-150 shadow-md shadow-blue-500/10 cursor-pointer']) }}>
    {{ $slot }}
</button>
