<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Meta Profiles')" :subheading="__('Manage your Facebook and Instagram business profiles')">

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

        <!-- Connected Profiles List -->
        @if (count($userMetaProfiles) > 0)
            <div class="mb-8">
                <h3 class="mb-4 text-lg font-medium text-gray-900 dark:text-white">{{ __('Your Meta Profiles') }}</h3>
                <div class="grid grid-cols-1 gap-4">
                    @foreach ($userMetaProfiles as $profile)
                        <div
                            class="rounded-lg border p-4 {{ $profile['is_default'] ? 'border-indigo-500 bg-indigo-50 dark:border-indigo-400 dark:bg-indigo-900/20' : 'border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800' }}">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2">
                                        <h5 class="font-medium text-gray-900 dark:text-white">
                                            {{ $profile['name'] }}
                                        </h5>
                                        @if ($profile['is_default'])
                                            <span
                                                class="inline-block rounded bg-indigo-100 px-2 py-0.5 text-xs font-medium text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">
                                                {{ __('Default') }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="mt-2 grid grid-cols-1 gap-2 text-xs text-gray-600 dark:text-gray-400 sm:grid-cols-2">
                                        <div>
                                            <span class="font-medium">{{ __('Ad Account ID:') }}</span>
                                            {{ $profile['ad_account_id'] }}
                                        </div>
                                        <div>
                                            <span class="font-medium">{{ __('Facebook Page ID:') }}</span>
                                            {{ $profile['facebook_page_id'] }}
                                        </div>
                                        <div>
                                            <span class="font-medium">{{ __('Facebook Pixel:') }}</span>
                                            {{ $profile['facebook_pixel'] }}
                                        </div>
                                        @if ($profile['instagram_profile_id'])
                                            <div>
                                                <span class="font-medium">{{ __('Instagram Profile ID:') }}</span>
                                                {{ $profile['instagram_profile_id'] }}
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Campaigns and Ad Sets Count -->
                                    <div class="mt-3 flex gap-3 text-xs">
                                        <span class="rounded bg-blue-100 px-2 py-1 font-medium text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                            {{ count($profile['campaigns_available_for_duplicate'] ?? []) }} {{ __('Campaigns') }}
                                        </span>
                                        <span class="rounded bg-green-100 px-2 py-1 font-medium text-green-800 dark:bg-green-900 dark:text-green-200">
                                            {{ count($profile['ad_sets_available_for_duplicate'] ?? []) }} {{ __('Ad Sets') }}
                                        </span>
                                    </div>
                                </div>
                                <div class="flex flex-col gap-2 sm:flex-row">
                                    @if (!$profile['is_default'])
                                        <flux:button wire:click="setDefaultProfile({{ $profile['id'] }})" variant="ghost"
                                            size="sm">
                                            {{ __('Set Default') }}
                                        </flux:button>
                                    @endif
                                    <flux:button wire:click="deleteProfile({{ $profile['id'] }})"
                                        wire:confirm="{{ __('Are you sure you want to delete this profile?') }}"
                                        variant="danger" size="sm">
                                        {{ __('Delete') }}
                                    </flux:button>
                                </div>
                            </div>

                            <!-- Campaigns Section -->
                            <div class="mt-4 border-t pt-4 dark:border-gray-600">
                                <h6 class="mb-3 text-sm font-semibold text-gray-900 dark:text-white">{{ __('Campaigns') }}</h6>

                                <!-- Existing Campaigns List -->
                                @if (count($profile['campaigns_available_for_duplicate'] ?? []) > 0)
                                    <div class="mb-3 space-y-2">
                                        @foreach ($profile['campaigns_available_for_duplicate'] as $index => $campaign)
                                            <div class="flex items-center justify-between rounded bg-gray-50 p-2 dark:bg-gray-700">
                                                <div class="flex-1">
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $campaign['name'] }}</p>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400">ID: {{ $campaign['id'] }}</p>
                                                </div>
                                                <flux:button wire:click="removeCampaign({{ $profile['id'] }}, {{ $index }})"
                                                    variant="danger" size="sm">
                                                    {{ __('Remove') }}
                                                </flux:button>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                <!-- Add Campaign Form -->
                                <div class="grid grid-cols-1 gap-2 sm:grid-cols-3">
                                    <flux:input wire:model="campaign_id" type="text" placeholder="{{ __('Campaign ID') }}" />
                                    <flux:input wire:model="campaign_name" type="text" placeholder="{{ __('Campaign Name') }}" />
                                    <flux:button wire:click="addCampaign({{ $profile['id'] }})" variant="primary" size="sm">
                                        {{ __('Add Campaign') }}
                                    </flux:button>
                                </div>
                            </div>

                            <!-- Ad Sets Section -->
                            <div class="mt-4 border-t pt-4 dark:border-gray-600">
                                <h6 class="mb-3 text-sm font-semibold text-gray-900 dark:text-white">{{ __('Ad Sets') }}</h6>

                                <!-- Existing Ad Sets List -->
                                @if (count($profile['ad_sets_available_for_duplicate'] ?? []) > 0)
                                    <div class="mb-3 space-y-2">
                                        @foreach ($profile['ad_sets_available_for_duplicate'] as $index => $adSet)
                                            <div class="flex items-center justify-between rounded bg-gray-50 p-2 dark:bg-gray-700">
                                                <div class="flex-1">
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $adSet['name'] }}</p>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400">ID: {{ $adSet['id'] }}</p>
                                                </div>
                                                <flux:button wire:click="removeAdSet({{ $profile['id'] }}, {{ $index }})"
                                                    variant="danger" size="sm">
                                                    {{ __('Remove') }}
                                                </flux:button>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                <!-- Add Ad Set Form -->
                                <div class="grid grid-cols-1 gap-2 sm:grid-cols-3">
                                    <flux:input wire:model="ad_set_id" type="text" placeholder="{{ __('Ad Set ID') }}" />
                                    <flux:input wire:model="ad_set_name" type="text" placeholder="{{ __('Ad Set Name') }}" />
                                    <flux:button wire:click="addAdSet({{ $profile['id'] }})" variant="primary" size="sm">
                                        {{ __('Add Ad Set') }}
                                    </flux:button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Add Meta Profile Form -->
        <div>
            <h3 class="mb-4 text-lg font-medium text-gray-900 dark:text-white">{{ __('Add Meta Profile') }}</h3>

            <form wire:submit="save" class="space-y-6">
                <!-- Profile Name -->
                <div>
                    <flux:input wire:model="name" :label="__('Profile Name')" type="text"
                        :placeholder="__('e.g., Main Business Account')" required />
                    @error('name')
                        <flux:text class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</flux:text>
                    @enderror
                </div>

                <!-- Ad Account ID -->
                <div>
                    <flux:input wire:model="ad_account_id" :label="__('Ad Account ID')" type="text"
                        :placeholder="__('act_123456789')" required />
                    @error('ad_account_id')
                        <flux:text class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</flux:text>
                    @enderror
                </div>

                <!-- Facebook Page ID -->
                <div>
                    <flux:input wire:model="facebook_page_id" :label="__('Facebook Page ID')" type="text"
                        :placeholder="__('123456789012345')" required />
                    @error('facebook_page_id')
                        <flux:text class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</flux:text>
                    @enderror
                </div>

                <!-- Instagram Profile ID (Optional) -->
                <div>
                    <flux:input wire:model="instagram_profile_id" :label="__('Instagram Profile ID (Optional)')"
                        type="text" :placeholder="__('123456789012345')" />
                    @error('instagram_profile_id')
                        <flux:text class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</flux:text>
                    @enderror
                </div>

                <!-- Facebook Pixel -->
                <div>
                    <flux:input wire:model="facebook_pixel" :label="__('Facebook Pixel')" type="text"
                        :placeholder="__('123456789012345')" required />
                    @error('facebook_pixel')
                        <flux:text class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</flux:text>
                    @enderror
                </div>

                <!-- Access Token -->
                <div>
                    <flux:input wire:model="access_token" :label="__('Access Token')" type="password"
                        autocomplete="off" :placeholder="__('Enter your Meta access token')" required />
                    @error('access_token')
                        <flux:text class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</flux:text>
                    @enderror
                    <flux:text class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        {{ __('Your access token is stored securely using encryption') }}
                    </flux:text>
                </div>

                <div class="flex items-center justify-end">
                    <flux:button variant="primary" type="submit">{{ __('Add Profile') }}</flux:button>
                </div>
            </form>
        </div>
    </x-settings.layout>
</section>
