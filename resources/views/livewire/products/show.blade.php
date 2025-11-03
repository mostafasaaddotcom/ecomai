<div class="flex h-full w-full flex-1 flex-col gap-4">
    {{-- Tab Navigation --}}
    <x-product-tabs :product="$product" active="show" />

    {{-- Product Image Card --}}
    @if ($product->main_image_url)
        <div class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-zinc-900">
            <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">{{ __('Product Image') }}</h2>
            <div class="flex justify-start">
                <img
                    src="{{ asset('storage/' . $product->main_image_url) }}"
                    alt="{{ $product->name }}"
                    class="rounded-lg object-cover shadow-lg"
                    style="width: 160px; height: 160px; max-width: 160px; max-height: 160px;"
                >
            </div>
        </div>
    @endif

    {{-- Product Information Card --}}
    <div class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-zinc-900">
        <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">{{ __('Product Information') }}</h2>

        <div class="space-y-4">
            {{-- Product Type --}}
            <div>
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Product Type') }}</p>
                <div class="mt-1">
                    <flux:badge :color="$product->type === 'physical' ? 'blue' : 'green'" size="sm">
                        {{ ucfirst($product->type) }}
                    </flux:badge>
                </div>
            </div>

            {{-- Store Link URL --}}
            @if ($product->store_link_url)
                <div>
                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Store Link') }}</p>
                    <div class="mt-1">
                        <a
                            href="{{ $product->store_link_url }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="inline-flex items-center gap-1 text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 hover:underline"
                        >
                            {{ $product->store_link_url }}
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                        </a>
                    </div>
                </div>
            @endif

            {{-- User Description --}}
            @if ($product->description_user)
                <div>
                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('User Description') }}</p>
                    <p class="mt-1 text-sm text-gray-900 dark:text-white whitespace-pre-wrap">{{ $product->description_user }}</p>
                </div>
            @endif

            {{-- AI Description --}}
            @if ($product->description_ai)
                <div>
                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('AI Description') }}</p>
                    <p class="mt-1 text-sm text-gray-900 dark:text-white whitespace-pre-wrap">{{ $product->description_ai }}</p>
                </div>
            @endif

            {{-- Analysis Status --}}
            <div>
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Analysis Status') }}</p>
                <div class="mt-1">
                    @if ($product->hasAnalysis())
                        <div class="inline-flex items-center gap-2">
                            <flux:badge color="green" size="sm">
                                {{ __('Completed') }}
                            </flux:badge>
                            <a
                                href="{{ route('products.analysis', $product) }}"
                                wire:navigate
                                class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                            >
                                {{ __('View Analysis') }} →
                            </a>
                        </div>
                    @else
                        <div class="inline-flex items-center gap-2">
                            <flux:badge color="gray" size="sm">
                                {{ __('Not Started') }}
                            </flux:badge>
                            <a
                                href="{{ route('products.analysis', $product) }}"
                                wire:navigate
                                class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                            >
                                {{ __('Generate Analysis') }} →
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Metadata Card --}}
    <div class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-zinc-900">
        <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">{{ __('Metadata') }}</h2>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <div>
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Created') }}</p>
                <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $product->created_at->format('F j, Y \a\t g:i A') }}</p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Last Updated') }}</p>
                <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $product->updated_at->format('F j, Y \a\t g:i A') }}</p>
            </div>
        </div>
    </div>

    {{-- Quick Actions Card --}}
    <div class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-zinc-900">
        <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">{{ __('Quick Actions') }}</h2>

        <div class="flex flex-wrap gap-3">
            <flux:button :href="route('products.edit', $product)" wire:navigate variant="primary" icon="pencil">
                {{ __('Edit Product') }}
            </flux:button>

            <flux:button :href="route('products.analysis', $product)" wire:navigate variant="primary" icon="chart-bar">
                {{ __('View Analysis') }}
            </flux:button>
        </div>
    </div>
</div>
