<div class="flex h-full w-full flex-1 flex-col gap-6">
        {{-- Welcome Section --}}
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ __('messages.Welcome back') }}, {{ Auth::user()->name }}!</h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">{{ __('messages.Here\'s an overview of your products and activity.') }}</p>
        </div>

        {{-- Flash Messages --}}
        @if (session('message'))
            <div class="rounded-lg border border-green-200 bg-green-50 p-4 text-sm text-green-800 dark:border-green-800 dark:bg-green-900/20 dark:text-green-400">
                {{ session('message') }}
            </div>
        @endif

        {{-- Statistics Cards --}}
        <div class="grid gap-4 md:grid-cols-3">
            {{-- Total Products Card --}}
            <div class="relative overflow-hidden rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-zinc-900">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('messages.Total Products') }}</p>
                        <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $totalProducts }}</p>
                    </div>
                    <div class="rounded-full bg-blue-100 p-3 dark:bg-blue-900/30">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Total AI Images Card --}}
            <div class="relative overflow-hidden rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-zinc-900">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('messages.AI Images Generated') }}</p>
                        <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $totalImages }}</p>
                    </div>
                    <div class="rounded-full bg-green-100 p-3 dark:bg-green-900/30">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Total Copies Card --}}
            <div class="relative overflow-hidden rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-zinc-900">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('messages.Copies Generated') }}</p>
                        <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $totalCopies }}</p>
                    </div>
                    <div class="rounded-full bg-purple-100 p-3 dark:bg-purple-900/30">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- Recent Products Section --}}
        <div class="relative flex-1 overflow-hidden rounded-xl border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-zinc-900">
            <div class="p-6">
                <div class="mb-6 flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">{{ __('messages.Recent Products') }}</h2>
                    <flux:button :href="route('products.create')" wire:navigate variant="primary" icon="plus" size="sm">
                        {{ __('messages.New Product') }}
                    </flux:button>
                </div>

                @if ($recentProducts->isEmpty())
                    {{-- Empty State --}}
                    <div class="py-12 text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-7 w-7 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                        <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">{{ __('messages.No products yet') }}</h3>
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                            {{ __('messages.Get started by creating your first product.') }}
                        </p>
                        <div class="mt-6">
                            <flux:button :href="route('products.create')" wire:navigate variant="primary" icon="plus">
                                {{ __('messages.Create Your First Product') }}
                            </flux:button>
                        </div>
                    </div>
                @else
                    {{-- Products Table --}}
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="border-b border-gray-200 dark:border-gray-700">
                                <tr>
                                    <th class="pb-3 text-left text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('messages.Product') }}</th>
                                    <th class="pb-3 text-left text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('messages.Type') }}</th>
                                    <th class="pb-3 text-left text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('messages.Images') }}</th>
                                    <th class="pb-3 text-left text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('messages.Copies') }}</th>
                                    <th class="pb-3 text-left text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('messages.Created') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach ($recentProducts as $product)
                                    <tr class="group hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                        <td class="py-4">
                                            <div class="flex items-center gap-3">
                                                @if ($product->main_image_url)
                                                    <img src="{{ asset('storage/' . $product->main_image_url) }}" alt="{{ $product->name }}" class="h-9 w-9 rounded-lg object-cover">
                                                @else
                                                    <div class="flex h-5 w-5 items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-800">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                        </svg>
                                                    </div>
                                                @endif
                                                <div>
                                                    <p class="font-medium text-gray-900 dark:text-white">{{ $product->name }}</p>
                                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ Str::limit($product->description_user, 50) }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-4">
                                            <flux:badge color="zinc" size="sm">{{ ucfirst($product->type) }}</flux:badge>
                                        </td>
                                        <td class="py-4">
                                            <span class="text-sm text-gray-900 dark:text-white">{{ $product->images->count() }}</span>
                                        </td>
                                        <td class="py-4">
                                            <span class="text-sm text-gray-900 dark:text-white">{{ $product->copies->count() }}</span>
                                        </td>
                                        <td class="py-4">
                                            <span class="text-sm text-gray-600 dark:text-gray-400">{{ $product->created_at->diffForHumans() }}</span>
                                        </td>
                                        <td class="py-4">
                                            <div class="flex items-center justify-end gap-2">
                                                <flux:button :href="route('products.show', $product)" wire:navigate variant="ghost" size="sm">
                                                    {{ __('messages.View') }}
                                                </flux:button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- View All Link --}}
                    @if ($totalProducts > 5)
                        <div class="mt-6 text-center">
                            <flux:button :href="route('products.index')" wire:navigate variant="ghost">
                                {{ __('messages.View All Products') }} ({{ $totalProducts }})
                            </flux:button>
                        </div>
                    @endif
                @endif
            </div>
        </div>
</div>
