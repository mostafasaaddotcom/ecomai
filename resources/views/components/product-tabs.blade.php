@props(['product', 'active' => 'show'])

<div class="mb-4">
    {{-- Product Header --}}
    <div class="mb-4">
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $product->name }}</h1>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ __('Manage your product') }}</p>
    </div>

    {{-- Tab Navigation --}}
    <div class="flex items-center gap-2 border-b border-neutral-200 dark:border-neutral-700">
        {{-- Overview Tab --}}
        <a
            href="{{ route('products.show', $product) }}"
            wire:navigate
            class="inline-flex items-center gap-2 border-b-2 px-4 py-3 text-sm font-medium transition-colors {{ $active === 'show' ? 'border-blue-500 text-blue-600 dark:border-blue-400 dark:text-blue-400' : 'border-transparent text-gray-600 hover:border-gray-300 hover:text-gray-900 dark:text-gray-400 dark:hover:border-gray-600 dark:hover:text-gray-200' }}"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
            {{ __('Overview') }}
        </a>

        {{-- Analysis Tab --}}
        <a
            href="{{ route('products.analysis', $product) }}"
            wire:navigate
            class="inline-flex items-center gap-2 border-b-2 px-4 py-3 text-sm font-medium transition-colors {{ $active === 'analysis' ? 'border-blue-500 text-blue-600 dark:border-blue-400 dark:text-blue-400' : 'border-transparent text-gray-600 hover:border-gray-300 hover:text-gray-900 dark:text-gray-400 dark:hover:border-gray-600 dark:hover:text-gray-200' }}"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
            {{ __('Analysis') }}
        </a>

        {{-- Copywriting Tab --}}
        <a
            href="{{ route('products.copywriting', $product) }}"
            wire:navigate
            class="inline-flex items-center gap-2 border-b-2 px-4 py-3 text-sm font-medium transition-colors {{ $active === 'copywriting' ? 'border-blue-500 text-blue-600 dark:border-blue-400 dark:text-blue-400' : 'border-transparent text-gray-600 hover:border-gray-300 hover:text-gray-900 dark:text-gray-400 dark:hover:border-gray-600 dark:hover:text-gray-200' }}"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
            {{ __('Copywriting') }}
        </a>

        {{-- Images Tab --}}
        <a
            href="{{ route('products.images', $product) }}"
            wire:navigate
            class="inline-flex items-center gap-2 border-b-2 px-4 py-3 text-sm font-medium transition-colors {{ $active === 'images' ? 'border-blue-500 text-blue-600 dark:border-blue-400 dark:text-blue-400' : 'border-transparent text-gray-600 hover:border-gray-300 hover:text-gray-900 dark:text-gray-400 dark:hover:border-gray-600 dark:hover:text-gray-200' }}"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            {{ __('Images') }}
        </a>

        {{-- Spacer to push action buttons to the right --}}
        <div class="ml-auto flex items-center gap-2 pb-3">
            <flux:button :href="route('products.index')" wire:navigate size="sm" variant="ghost" icon="arrow-left">
                {{ __('Back') }}
            </flux:button>
        </div>
    </div>
</div>
