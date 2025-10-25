<div class="flex h-full w-full flex-1 flex-col gap-4">
    {{-- Tab Navigation --}}
    <x-product-tabs :product="$product" active="copywriting" />

    {{-- Flash Messages --}}
    @if (session('message'))
        <div class="rounded-lg border border-green-200 bg-green-50 p-4 text-sm text-green-800 dark:border-green-800 dark:bg-green-900/20 dark:text-green-400">
            {{ session('message') }}
        </div>
    @endif

    @if (session('error'))
        <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-800 dark:border-red-800 dark:bg-red-900/20 dark:text-red-400">
            {{ session('error') }}
        </div>
    @endif

    {{-- Analysis Check Alert --}}
    @if (!$product->hasAnalysis())
        <div class="rounded-xl border border-yellow-200 bg-yellow-50 p-6 dark:border-yellow-800 dark:bg-yellow-900/20">
            <div class="flex items-start gap-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-600 dark:text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-yellow-900 dark:text-yellow-100">{{ __('Analysis Required') }}</h3>
                    <p class="mt-1 text-sm text-yellow-800 dark:text-yellow-300">
                        {{ __('Please generate a product analysis first before creating copies. The analysis provides essential information for generating high-quality copy.') }}
                    </p>
                    <div class="mt-4">
                        <flux:button :href="route('products.analysis', $product)" wire:navigate variant="primary" icon="chart-bar">
                            {{ __('Generate Analysis') }}
                        </flux:button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Generate Copies Button --}}
    <div class="flex justify-between items-center">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Product Copies') }}</h2>
        <flux:modal.trigger name="generate-copies-modal">
            <flux:button
                variant="primary"
                icon="sparkles"
                :disabled="!$product->hasAnalysis()"
                x-data=""
                x-on:click.prevent="$dispatch('open-modal', 'generate-copies-modal')"
            >
                {{ __('Generate New Copies') }}
            </flux:button>
        </flux:modal.trigger>
    </div>

    {{-- Generate Copies Modal --}}
    <flux:modal name="generate-copies-modal" class="max-w-4xl">
        <form wire:submit="generateCopies" class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('Generate Copies') }}</flux:heading>
                <flux:subheading>{{ __('Configure your copywriting parameters and generate AI-powered product copies') }}</flux:subheading>
            </div>

            {{-- Language Selection --}}
            <div>
                <flux:label>{{ __('Language / Dialect') }}</flux:label>
                <select
                    wire:model="language"
                    class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                    required
                >
                    <option value="">{{ __('Select language or dialect...') }}</option>
                    @foreach ($languages as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Copy Type Cards --}}
            <div>
                <flux:label>{{ __('Copy Types') }}</flux:label>
                <p class="mt-1 mb-3 text-xs text-gray-500 dark:text-gray-400">{{ __('Select how many copies of each type to generate') }}</p>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                    {{-- UGC Card --}}
                    <div class="rounded-lg border-2 border-blue-200 bg-blue-50 p-4 dark:border-blue-800 dark:bg-blue-900/20">
                        <div class="mb-3 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <h3 class="font-medium text-blue-900 dark:text-blue-100">{{ __('User Generated Content') }}</h3>
                        </div>
                        <div class="flex items-center justify-center gap-3">
                            <button
                                type="button"
                                wire:click="decrementCount('ugc')"
                                class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-200 text-blue-900 hover:bg-blue-300 dark:bg-blue-800 dark:text-blue-100 dark:hover:bg-blue-700"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                </svg>
                            </button>
                            <span class="text-2xl font-bold text-blue-900 dark:text-blue-100">{{ $ugcCount }}</span>
                            <button
                                type="button"
                                wire:click="incrementCount('ugc')"
                                class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-200 text-blue-900 hover:bg-blue-300 dark:bg-blue-800 dark:text-blue-100 dark:hover:bg-blue-700"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Expert Card --}}
                    <div class="rounded-lg border-2 border-green-200 bg-green-50 p-4 dark:border-green-800 dark:bg-green-900/20">
                        <div class="mb-3 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                            </svg>
                            <h3 class="font-medium text-green-900 dark:text-green-100">{{ __('Expert Voice') }}</h3>
                        </div>
                        <div class="flex items-center justify-center gap-3">
                            <button
                                type="button"
                                wire:click="decrementCount('expert')"
                                class="flex h-8 w-8 items-center justify-center rounded-full bg-green-200 text-green-900 hover:bg-green-300 dark:bg-green-800 dark:text-green-100 dark:hover:bg-green-700"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                </svg>
                            </button>
                            <span class="text-2xl font-bold text-green-900 dark:text-green-100">{{ $expertCount }}</span>
                            <button
                                type="button"
                                wire:click="incrementCount('expert')"
                                class="flex h-8 w-8 items-center justify-center rounded-full bg-green-200 text-green-900 hover:bg-green-300 dark:bg-green-800 dark:text-green-100 dark:hover:bg-green-700"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Background Voice Card --}}
                    <div class="rounded-lg border-2 border-purple-200 bg-purple-50 p-4 dark:border-purple-800 dark:bg-purple-900/20">
                        <div class="mb-3 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z" />
                            </svg>
                            <h3 class="font-medium text-purple-900 dark:text-purple-100">{{ __('Background Voice') }}</h3>
                        </div>
                        <div class="flex items-center justify-center gap-3">
                            <button
                                type="button"
                                wire:click="decrementCount('background_voice')"
                                class="flex h-8 w-8 items-center justify-center rounded-full bg-purple-200 text-purple-900 hover:bg-purple-300 dark:bg-purple-800 dark:text-purple-100 dark:hover:bg-purple-700"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                </svg>
                            </button>
                            <span class="text-2xl font-bold text-purple-900 dark:text-purple-100">{{ $backgroundVoiceCount }}</span>
                            <button
                                type="button"
                                wire:click="incrementCount('background_voice')"
                                class="flex h-8 w-8 items-center justify-center rounded-full bg-purple-200 text-purple-900 hover:bg-purple-300 dark:bg-purple-800 dark:text-purple-100 dark:hover:bg-purple-700"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Formula Multi-Select --}}
            <div>
                <flux:label>{{ __('Copywriting Formulas') }}</flux:label>
                <p class="mt-1 mb-3 text-xs text-gray-500 dark:text-gray-400">{{ __('Select one or more formulas to use') }}</p>

                <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                    @foreach ($formulas as $key => $label)
                        <label class="flex items-start gap-3 rounded-lg border border-gray-200 p-3 cursor-pointer hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800 {{ in_array($key, $selectedFormulas) ? 'bg-blue-50 border-blue-500 dark:bg-blue-900/20 dark:border-blue-500' : '' }}">
                            <input
                                type="checkbox"
                                wire:model="selectedFormulas"
                                value="{{ $key }}"
                                class="mt-1 rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700"
                            >
                            <span class="text-sm text-gray-900 dark:text-white">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Total Copies Info --}}
            @if ($this->totalCopies > 0)
                <div class="rounded-lg bg-blue-50 p-4 dark:bg-blue-900/20">
                    <p class="text-sm text-blue-900 dark:text-blue-100">
                        <strong>{{ __('Total copies to generate:') }}</strong> {{ $this->totalCopies }}
                        <span class="text-xs text-blue-700 dark:text-blue-300">
                            ({{ $ugcCount + $expertCount + $backgroundVoiceCount }} {{ __('types') }} × {{ count($selectedFormulas) }} {{ __('formulas') }})
                        </span>
                    </p>
                </div>
            @endif

            {{-- Modal Footer --}}
            <div class="flex justify-end space-x-2 rtl:space-x-reverse">
                <flux:modal.close>
                    <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
                </flux:modal.close>

                <flux:button
                    type="submit"
                    variant="primary"
                    :disabled="$isGenerating || !$product->hasAnalysis()"
                    icon="sparkles"
                >
                    @if ($isGenerating)
                        <svg class="mr-2 h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        {{ __('Generating...') }}
                    @else
                        {{ __('Generate Copies') }}
                    @endif
                </flux:button>
            </div>
        </form>
    </flux:modal>

    {{-- Generated Copies Section --}}
    <div class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-zinc-900">
        @if ($product->copies->isEmpty())
            <div class="py-12 text-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">{{ __('No Copies Yet') }}</h3>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    {{ __('Use the form above to generate your first copies.') }}
                </p>
            </div>
        @else
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($product->copies as $copy)
                    <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-zinc-900">
                        {{-- Card Header with Metadata --}}
                        <div class="p-4 bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                            <div class="flex items-start justify-between mb-2">
                                <div class="flex flex-wrap items-center gap-2">
                                    <flux:badge
                                        :color="$copy->type === 'ugc' ? 'blue' : ($copy->type === 'expert' ? 'green' : 'purple')"
                                        size="sm"
                                    >
                                        {{ ucfirst(str_replace('_', ' ', $copy->type)) }}
                                    </flux:badge>
                                    <flux:badge color="gray" size="sm">
                                        {{ $copy->formula }}
                                    </flux:badge>
                                    <span class="text-xs text-gray-600 dark:text-gray-400">
                                        • {{ $languages[$copy->language] ?? $copy->language }}
                                    </span>
                                    @if ($copy->tone)
                                        <span class="text-xs text-gray-600 dark:text-gray-400">
                                            • {{ $copy->tone }}
                                        </span>
                                    @endif
                                </div>
                                <button
                                    wire:click="deleteCopy({{ $copy->id }})"
                                    wire:confirm="Are you sure you want to delete this copy?"
                                    class="text-gray-400 hover:text-red-600 dark:hover:text-red-400"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                            <div class="flex flex-wrap items-center gap-3 text-xs text-gray-500 dark:text-gray-400">
                                @if ($copy->angle)
                                    <span>{{ __('Angle:') }} <span class="text-gray-700 dark:text-gray-300">{{ $copy->angle }}</span></span>
                                @endif
                                @if ($copy->voice_url_link)
                                    <a href="{{ $copy->voice_url_link }}" target="_blank" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                        🎤 {{ __('Voice') }}
                                    </a>
                                @endif
                                <span class="ml-auto">{{ $copy->updated_at->diffForHumans() }}</span>
                            </div>
                        </div>

                        {{-- Card Content --}}
                        <div class="p-4 space-y-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Content') }}</label>
                                <textarea
                                    rows="5"
                                    class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                    wire:model.defer="copyContent.{{ $copy->id }}"
                                >{{ $copy->content }}</textarea>
                            </div>

                            <div class="flex items-center gap-2">
                                <flux:button
                                    wire:click="updateCopy({{ $copy->id }})"
                                    size="sm"
                                    variant="primary"
                                >
                                    {{ __('Update Copy') }}
                                </flux:button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
