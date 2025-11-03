<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Store Settings')" :subheading="__('Connect your e-commerce stores')">

        <!-- Flash Messages -->
        @if (session()->has('message'))
            <div class="mb-4 rounded-md bg-green-50 p-4 dark:bg-green-900/20">
                <flux:text class="text-sm text-green-800 dark:text-green-200">
                    {{ session('message') }}
                </flux:text>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mb-4 rounded-md bg-red-50 p-4 dark:bg-red-900/20">
                <flux:text class="text-sm text-red-800 dark:text-red-200">
                    {{ session('error') }}
                </flux:text>
            </div>
        @endif

        <!-- Connected Stores List -->
        @if (count($stores) > 0)
            <div class="mb-8">
                <h3 class="mb-4 text-lg font-medium text-gray-900 dark:text-white">{{ __('Connected Stores') }}</h3>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    @foreach ($stores as $store)
                        <div
                            class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2">
                                        <h5 class="font-medium text-gray-900 dark:text-white">
                                            {{ $store->name }}
                                        </h5>
                                        <span
                                            class="inline-block rounded bg-blue-100 px-2 py-0.5 text-xs font-medium text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                            {{ ucfirst($store->platform) }}
                                        </span>
                                    </div>
                                    @if ($store->store_link)
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                            <a href="{{ $store->store_link }}" target="_blank"
                                                class="hover:underline">{{ $store->store_link }}</a>
                                        </p>
                                    @endif
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                        {{ __('Added on') }} {{ $store->created_at->format('M d, Y') }}
                                    </p>
                                </div>
                                <flux:button wire:click="deleteStore({{ $store->id }})"
                                    wire:confirm="{{ __('Are you sure you want to delete this store?') }}"
                                    variant="danger" size="sm">
                                    {{ __('Delete') }}
                                </flux:button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Add Store Form -->
        <div>
            <h3 class="mb-4 text-lg font-medium text-gray-900 dark:text-white">{{ __('Add Store') }}</h3>

            <!-- Platform Selection -->
            <div class="mb-6">
                <flux:select wire:model.live="platform" :label="__('Platform')">
                    <option value="woocommerce">WooCommerce</option>
                    <option value="easyorders">EasyOrders</option>
                    <option value="shopify">Shopify</option>
                </flux:select>
            </div>

            @if ($platform === 'shopify')
                <!-- Shopify Coming Soon Message -->
                <div class="rounded-lg border border-gray-200 bg-gray-50 p-6 text-center dark:border-gray-700 dark:bg-gray-800">
                    <div class="mb-2">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                    </div>
                    <h4 class="mb-1 text-lg font-medium text-gray-900 dark:text-white">{{ __('Coming Soon') }}</h4>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ __('Shopify integration is currently under development. Check back soon!') }}
                    </p>
                </div>
            @else
                <!-- Store Form -->
                <form wire:submit="save" class="space-y-6">
                    <!-- Store Name (Common for all platforms) -->
                    <div>
                        <flux:input wire:model="name" :label="__('Store Name')" type="text"
                            :placeholder="__('e.g., My Online Store')" required />
                        @error('name')
                            <flux:text class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</flux:text>
                        @enderror
                    </div>

                    <!-- Store Link (Common for all platforms) -->
                    <div>
                        <flux:input wire:model="store_link" :label="__('Store Link')" type="url"
                            :placeholder="__('https://your-store.com')" required />
                        @error('store_link')
                            <flux:text class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</flux:text>
                        @enderror
                    </div>

                    @if ($platform === 'woocommerce')
                        <!-- WooCommerce Fields -->
                        <div>
                            <flux:input wire:model="consumer_key" :label="__('Consumer Key')" type="text"
                                :placeholder="__('Enter your WooCommerce consumer key')" required />
                            @error('consumer_key')
                                <flux:text class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</flux:text>
                            @enderror
                        </div>

                        <div>
                            <flux:input wire:model="consumer_secret" :label="__('Consumer Secret')" type="password"
                                autocomplete="off" :placeholder="__('Enter your WooCommerce consumer secret')"
                                required />
                            @error('consumer_secret')
                                <flux:text class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</flux:text>
                            @enderror
                            <flux:text class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                {{ __('Your credentials are stored securely and never shared') }}
                            </flux:text>
                        </div>
                    @elseif($platform === 'easyorders')
                        <!-- EasyOrders Fields -->
                        <div>
                            <flux:input wire:model="api_key" :label="__('API Key')" type="password" autocomplete="off"
                                :placeholder="__('Enter your EasyOrders API key')" required />
                            @error('api_key')
                                <flux:text class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</flux:text>
                            @enderror
                            <flux:text class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                {{ __('Your credentials are stored securely and never shared') }}
                            </flux:text>
                        </div>
                    @endif

                    <div class="flex items-center justify-end">
                        <flux:button variant="primary" type="submit">{{ __('Add Store') }}</flux:button>
                    </div>
                </form>
            @endif
        </div>
    </x-settings.layout>
</section>
