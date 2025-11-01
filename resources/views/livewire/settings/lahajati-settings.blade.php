<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Lahajati Voice Settings')"
        :subheading="__('Manage your voice preferences for text-to-speech generation')">

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

        <!-- Tabs Navigation -->
        <div class="mb-6 border-b border-gray-200 dark:border-gray-700">
            <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                <button type="button" wire:click="$set('activeTab', 'voices')"
                    class="{{ $activeTab === 'voices' ? 'border-indigo-500 text-indigo-600 dark:border-indigo-400 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:border-gray-600 dark:hover:text-gray-300' }} whitespace-nowrap border-b-2 px-1 py-4 text-sm font-medium">
                    {{ __('Voices') }}
                </button>
                <button type="button" wire:click="$set('activeTab', 'performances')"
                    class="{{ $activeTab === 'performances' ? 'border-indigo-500 text-indigo-600 dark:border-indigo-400 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:border-gray-600 dark:hover:text-gray-300' }} whitespace-nowrap border-b-2 px-1 py-4 text-sm font-medium">
                    {{ __('Performances') }}
                </button>
                <button type="button" wire:click="$set('activeTab', 'dialects')"
                    class="{{ $activeTab === 'dialects' ? 'border-indigo-500 text-indigo-600 dark:border-indigo-400 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:border-gray-600 dark:hover:text-gray-300' }} whitespace-nowrap border-b-2 px-1 py-4 text-sm font-medium">
                    {{ __('Dialects') }}
                </button>
            </nav>
        </div>

        <!-- Voices Tab -->
        @if ($activeTab === 'voices')
            <div class="space-y-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('Available Voices') }}</h3>
                        <flux:text class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            {{ __('Sync voices from Lahajati and add them to your preferences') }}
                        </flux:text>
                    </div>
                    <flux:button wire:click="syncVoices" :disabled="$isSyncingVoices" variant="primary">
                        @if ($isSyncingVoices)
                            <span class="inline-flex items-center">
                                <svg class="mr-2 h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                {{ __('Syncing...') }}
                            </span>
                        @else
                            {{ __('Sync Voices') }}
                        @endif
                    </flux:button>
                </div>

                @if (count($userVoices) > 0)
                    <div>
                        <h4 class="mb-3 text-sm font-medium text-gray-900 dark:text-white">{{ __('My Voices') }}</h4>
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                            @foreach ($userVoices as $userVoice)
                                <div class="rounded-lg border p-4 {{ $userVoice->is_default ? 'border-indigo-500 bg-indigo-50 dark:border-indigo-400 dark:bg-indigo-900/20' : 'border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800' }}">
                                    @if ($userVoice->is_default)
                                        <div class="mb-2">
                                            <span class="inline-block rounded bg-indigo-100 px-2 py-1 text-xs font-medium text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">
                                                {{ __('Default') }}
                                            </span>
                                        </div>
                                    @endif
                                    <div>
                                        <h5 class="font-medium text-gray-900 dark:text-white">
                                            {{ $userVoice->lahajatiVoice->name }}
                                        </h5>
                                        @if ($userVoice->lahajatiVoice->gender)
                                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                                {{ __('Gender: ' . $userVoice->lahajatiVoice->gender) }}
                                            </p>
                                        @endif
                                    </div>
                                    <div class="mt-3 flex flex-wrap gap-2">
                                        @if (!$userVoice->is_default)
                                            <flux:button wire:click="setDefaultVoice({{ $userVoice->id }})" variant="ghost" size="sm">
                                                {{ __('Set Default') }}
                                            </flux:button>
                                        @endif
                                        <flux:button wire:click="removeVoice({{ $userVoice->id }})" variant="danger" size="sm">
                                            {{ __('Remove') }}
                                        </flux:button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div>
                    <h4 class="mb-3 text-sm font-medium text-gray-900 dark:text-white">{{ __('All Voices') }}</h4>
                    @if (count($voices) > 0)
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                            @foreach ($voices as $voice)
                                <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
                                    <h5 class="font-medium text-gray-900 dark:text-white">{{ $voice->name }}</h5>
                                    @if ($voice->gender)
                                        <flux:text class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                            {{ __('Gender: ' . $voice->gender) }}
                                        </flux:text>
                                    @endif
                                    @if ($voice->description)
                                        <flux:text class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                                            {{ $voice->description }}
                                        </flux:text>
                                    @endif
                                    <div class="mt-3">
                                        @php
                                            $isAdded = $userVoices->contains(function ($uv) use ($voice) {
                                                return $uv->lahajati_voice_id === $voice->id;
                                            });
                                        @endphp
                                        @if ($isAdded)
                                            <flux:button disabled variant="ghost" size="sm">
                                                {{ __('Added') }}
                                            </flux:button>
                                        @else
                                            <flux:button wire:click="addVoice({{ $voice->id }})" variant="primary"
                                                size="sm">
                                                {{ __('Add to My Voices') }}
                                            </flux:button>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <flux:text class="text-sm text-gray-500 dark:text-gray-400">
                            {{ __('No voices available. Click "Sync Voices" to fetch from Lahajati.') }}
                        </flux:text>
                    @endif
                </div>
            </div>
        @endif

        <!-- Performances Tab -->
        @if ($activeTab === 'performances')
            <div class="space-y-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('Available Performances') }}
                        </h3>
                        <flux:text class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            {{ __('Sync performance styles from Lahajati and add them to your preferences') }}
                        </flux:text>
                    </div>
                    <flux:button wire:click="syncPerformances" :disabled="$isSyncingPerformances" variant="primary">
                        @if ($isSyncingPerformances)
                            <span class="inline-flex items-center">
                                <svg class="mr-2 h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                {{ __('Syncing...') }}
                            </span>
                        @else
                            {{ __('Sync Performances') }}
                        @endif
                    </flux:button>
                </div>

                @if (count($userPerformances) > 0)
                    <div>
                        <h4 class="mb-3 text-sm font-medium text-gray-900 dark:text-white">{{ __('My Performances') }}</h4>
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                            @foreach ($userPerformances as $userPerformance)
                                <div class="rounded-lg border p-4 {{ $userPerformance->is_default ? 'border-indigo-500 bg-indigo-50 dark:border-indigo-400 dark:bg-indigo-900/20' : 'border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800' }}">
                                    @if ($userPerformance->is_default)
                                        <div class="mb-2">
                                            <span class="inline-block rounded bg-indigo-100 px-2 py-1 text-xs font-medium text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">
                                                {{ __('Default') }}
                                            </span>
                                        </div>
                                    @endif
                                    <div>
                                        <h5 class="font-medium text-gray-900 dark:text-white">
                                            {{ $userPerformance->lahajatiPerformance->name }}
                                        </h5>
                                    </div>
                                    <div class="mt-3 flex flex-wrap gap-2">
                                        @if (!$userPerformance->is_default)
                                            <flux:button wire:click="setDefaultPerformance({{ $userPerformance->id }})" variant="ghost" size="sm">
                                                {{ __('Set Default') }}
                                            </flux:button>
                                        @endif
                                        <flux:button wire:click="removePerformance({{ $userPerformance->id }})" variant="danger" size="sm">
                                            {{ __('Remove') }}
                                        </flux:button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div>
                    <h4 class="mb-3 text-sm font-medium text-gray-900 dark:text-white">{{ __('All Performances') }}</h4>
                    @if (count($performances) > 0)
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                            @foreach ($performances as $performance)
                                <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
                                    <h5 class="font-medium text-gray-900 dark:text-white">{{ $performance->name }}</h5>
                                    @if ($performance->description)
                                        <flux:text class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                                            {{ $performance->description }}
                                        </flux:text>
                                    @endif
                                    <div class="mt-3">
                                        @php
                                            $isAdded = $userPerformances->contains(function ($up) use ($performance) {
                                                return $up->lahajati_performance_id === $performance->id;
                                            });
                                        @endphp
                                        @if ($isAdded)
                                            <flux:button disabled variant="ghost" size="sm">
                                                {{ __('Added') }}
                                            </flux:button>
                                        @else
                                            <flux:button wire:click="addPerformance({{ $performance->id }})"
                                                variant="primary" size="sm">
                                                {{ __('Add to My Performances') }}
                                            </flux:button>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <flux:text class="text-sm text-gray-500 dark:text-gray-400">
                            {{ __('No performances available. Click "Sync Performances" to fetch from Lahajati.') }}
                        </flux:text>
                    @endif
                </div>
            </div>
        @endif

        <!-- Dialects Tab -->
        @if ($activeTab === 'dialects')
            <div class="space-y-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('Available Dialects') }}</h3>
                        <flux:text class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            {{ __('Sync dialects from Lahajati and add them to your preferences') }}
                        </flux:text>
                    </div>
                    <flux:button wire:click="syncDialects" :disabled="$isSyncingDialects" variant="primary">
                        @if ($isSyncingDialects)
                            <span class="inline-flex items-center">
                                <svg class="mr-2 h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                {{ __('Syncing...') }}
                            </span>
                        @else
                            {{ __('Sync Dialects') }}
                        @endif
                    </flux:button>
                </div>

                @if (count($userDialects) > 0)
                    <div>
                        <h4 class="mb-3 text-sm font-medium text-gray-900 dark:text-white">{{ __('My Dialects') }}</h4>
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                            @foreach ($userDialects as $userDialect)
                                <div class="rounded-lg border p-4 {{ $userDialect->is_default ? 'border-indigo-500 bg-indigo-50 dark:border-indigo-400 dark:bg-indigo-900/20' : 'border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800' }}">
                                    @if ($userDialect->is_default)
                                        <div class="mb-2">
                                            <span class="inline-block rounded bg-indigo-100 px-2 py-1 text-xs font-medium text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">
                                                {{ __('Default') }}
                                            </span>
                                        </div>
                                    @endif
                                    <div>
                                        <h5 class="font-medium text-gray-900 dark:text-white">
                                            {{ $userDialect->lahajatiDialect->name }}
                                        </h5>
                                    </div>
                                    <div class="mt-3 flex flex-wrap gap-2">
                                        @if (!$userDialect->is_default)
                                            <flux:button wire:click="setDefaultDialect({{ $userDialect->id }})" variant="ghost" size="sm">
                                                {{ __('Set Default') }}
                                            </flux:button>
                                        @endif
                                        <flux:button wire:click="removeDialect({{ $userDialect->id }})" variant="danger" size="sm">
                                            {{ __('Remove') }}
                                        </flux:button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div>
                    <h4 class="mb-3 text-sm font-medium text-gray-900 dark:text-white">{{ __('All Dialects') }}</h4>
                    @if (count($dialects) > 0)
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                            @foreach ($dialects as $dialect)
                                <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
                                    <h5 class="font-medium text-gray-900 dark:text-white">{{ $dialect->name }}</h5>
                                    @if ($dialect->description)
                                        <flux:text class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                                            {{ $dialect->description }}
                                        </flux:text>
                                    @endif
                                    <div class="mt-3">
                                        @php
                                            $isAdded = $userDialects->contains(function ($ud) use ($dialect) {
                                                return $ud->lahajati_dialect_id === $dialect->id;
                                            });
                                        @endphp
                                        @if ($isAdded)
                                            <flux:button disabled variant="ghost" size="sm">
                                                {{ __('Added') }}
                                            </flux:button>
                                        @else
                                            <flux:button wire:click="addDialect({{ $dialect->id }})" variant="primary"
                                                size="sm">
                                                {{ __('Add to My Dialects') }}
                                            </flux:button>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <flux:text class="text-sm text-gray-500 dark:text-gray-400">
                            {{ __('No dialects available. Click "Sync Dialects" to fetch from Lahajati.') }}
                        </flux:text>
                    @endif
                </div>
            </div>
        @endif
    </x-settings.layout>
</section>
