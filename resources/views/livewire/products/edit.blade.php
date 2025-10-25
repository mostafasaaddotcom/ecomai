<div class="flex h-full w-full flex-1 flex-col gap-4">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">{{ __('Edit Product') }}</h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ __('Update product details') }}</p>
        </div>
        <flux:button :href="route('products.index')" wire:navigate variant="ghost" icon="arrow-left">
            {{ __('Back to Products') }}
        </flux:button>
    </div>

    <div class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-zinc-900">
        <form wire:submit="update" class="space-y-6">
            <flux:input
                wire:model="name"
                :label="__('Product Name')"
                type="text"
                required
                :placeholder="__('Enter product name')"
            />

            <div>
                <flux:label>{{ __('Product Type') }}</flux:label>
                <select
                    wire:model="type"
                    class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400"
                >
                    <option value="physical">{{ __('Physical') }}</option>
                    <option value="digital">{{ __('Digital') }}</option>
                </select>
            </div>

            <div>
                <flux:label>{{ __('User Description') }}</flux:label>
                <textarea
                    wire:model="description_user"
                    rows="4"
                    class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400"
                    placeholder="{{ __('Enter product description from user perspective') }}"
                ></textarea>
            </div>

            <div>
                <flux:label>{{ __('AI Description') }}</flux:label>
                <textarea
                    wire:model="description_ai"
                    rows="4"
                    class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400"
                    placeholder="{{ __('Enter AI-generated description') }}"
                ></textarea>
            </div>

            <div>
                <flux:label>{{ __('Product Image') }}</flux:label>

                @if ($product->main_image_url)
                    <div class="mb-3">
                        <p class="mb-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Current Image:') }}</p>
                        <img src="{{ asset('storage/' . $product->main_image_url) }}" alt="{{ $product->name }}" class="h-32 w-32 rounded object-cover">
                    </div>
                @endif

                <input
                    type="file"
                    wire:model="main_image"
                    accept="image/*"
                    class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400"
                />
                @if ($main_image)
                    <div class="mt-2">
                        <p class="mb-2 text-sm text-gray-600 dark:text-gray-400">{{ __('New Image Preview:') }}</p>
                        <img src="{{ $main_image->temporaryUrl() }}" alt="Preview" class="h-32 w-32 rounded object-cover">
                    </div>
                @endif
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('Maximum file size: 2MB. Supported formats: JPG, PNG, GIF') }}</p>
            </div>

            <div class="flex items-center gap-4">
                <flux:button type="submit" variant="primary">
                    {{ __('Update Product') }}
                </flux:button>
                <flux:button :href="route('products.index')" wire:navigate variant="ghost">
                    {{ __('Cancel') }}
                </flux:button>
            </div>
        </form>
    </div>
</div>
