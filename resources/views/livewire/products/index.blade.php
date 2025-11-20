<div class="flex h-full w-full flex-1 flex-col gap-4">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">{{ __('products.products') }}</h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ __('Manage your products') }}</p>
        </div>
        <flux:button :href="route('products.create')" wire:navigate variant="primary" icon="plus">
            {{ __('Add Product') }}
        </flux:button>
    </div>

    @if (session('message'))
        <div class="mb-4 rounded-lg border border-green-200 bg-green-50 p-4 text-sm text-green-800 dark:border-green-800 dark:bg-green-900/20 dark:text-green-400">
            {{ session('message') }}
        </div>
    @endif

    <div class="rounded-xl border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-zinc-900">
        @if ($products->isEmpty())
            <div class="p-12 text-center">
                <p class="text-gray-500 dark:text-gray-400">{{ __('No products yet. Create your first product!') }}</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-neutral-200 bg-neutral-50 text-xs uppercase text-neutral-700 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">{{ __('Image') }}</th>
                            <th scope="col" class="px-6 py-3">{{ __('Name') }}</th>
                            <th scope="col" class="px-6 py-3">{{ __('Type') }}</th>
                            <th scope="col" class="px-6 py-3">{{ __('Created') }}</th>
                            <th scope="col" class="px-6 py-3 text-right ">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($products as $product)
                            <tr class="border-b border-neutral-200 bg-white hover:bg-neutral-50 dark:border-neutral-700 dark:bg-zinc-900 dark:hover:bg-neutral-800" wire:key="product-{{ $product->id }}">
                                <td class="px-6 py-4">
                                    @if ($product->main_image_url)
                                        <img src="{{ asset('storage/' . $product->main_image_url) }}" alt="{{ $product->name }}" class="h-12 w-12 rounded object-cover" width="50" height="50">
                                    @else
                                        <div class="flex h-12 w-12 items-center justify-center rounded bg-gray-200 dark:bg-gray-700">
                                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ __('No image') }}</span>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                    <a href="{{ route('products.show', $product) }}" wire:navigate class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                        {{ $product->name }}
                                    </a>
                                </td>
                                <td class="px-6 py-4">
                                    <flux:badge :color="$product->type === 'physical' ? 'blue' : 'green'" size="sm">
                                        {{ ucfirst($product->type) }}
                                    </flux:badge>
                                </td>
                                <td class="px-6 py-4 text-gray-700 dark:text-gray-300">{{ $product->created_at->format('M d, Y') }}</td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <flux:button :href="route('products.show', $product)" wire:navigate size="sm" variant="primary" icon="eye">
                                            {{ __('View') }}
                                        </flux:button>
                                        <flux:button :href="route('products.edit', $product)" wire:navigate size="sm" variant="ghost" icon="pencil">
                                            {{ __('Edit') }}
                                        </flux:button>
                                        <flux:button wire:click="delete({{ $product->id }})" wire:confirm="Are you sure you want to delete this product?" size="sm" variant="danger" icon="trash">
                                            {{ __('Delete') }}
                                        </flux:button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="border-t border-neutral-200 p-4 dark:border-neutral-700">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</div>
