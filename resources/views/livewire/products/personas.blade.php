<div class="flex h-full w-full flex-1 flex-col gap-4">
    {{-- Tab Navigation --}}
    <x-product-tabs :product="$product" active="personas" />

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
                    <h3 class="text-lg font-semibold text-yellow-900 dark:text-yellow-100">{{ __('products.analysis_required') }}</h3>
                    <p class="mt-1 text-sm text-yellow-800 dark:text-yellow-300">
                        {{ __('products.analysis_required_for_personas') }}
                    </p>
                    <div class="mt-4">
                        <flux:button :href="route('products.analysis', $product)" wire:navigate variant="primary" icon="chart-bar">
                            {{ __('products.generate_analysis') }}
                        </flux:button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Generate Personas Section --}}
    <div class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-zinc-900">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('products.product_personas') }}</h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ __('products.generate_personas_description') }}</p>
            </div>
        </div>

        {{-- Personas Count Selector --}}
        <div class="flex items-center gap-6 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg mb-4">
            <div class="flex items-center gap-4 flex-1">
                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                    {{ __('products.number_of_personas') }}
                </label>
                <div class="flex items-center gap-3">
                    <button
                        type="button"
                        wire:click="decrementCount"
                        class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-200 text-blue-900 hover:bg-blue-300 dark:bg-blue-800 dark:text-blue-100 dark:hover:bg-blue-700"
                        :disabled="$isGenerating"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                        </svg>
                    </button>
                    <span class="text-2xl font-bold text-blue-900 dark:text-blue-100 min-w-[3rem] text-center">{{ $personasCount }}</span>
                    <button
                        type="button"
                        wire:click="incrementCount"
                        class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-200 text-blue-900 hover:bg-blue-300 dark:bg-blue-800 dark:text-blue-100 dark:hover:bg-blue-700"
                        :disabled="$isGenerating"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                    </button>
                </div>
            </div>

            <flux:button
                wire:click="generatePersonas"
                variant="primary"
                icon="sparkles"
                :disabled="!$product->hasAnalysis() || $isGenerating"
            >
                @if ($isGenerating)
                    <svg class="mr-2 h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    {{ __('products.generating') }}
                @else
                    {{ __('products.generate_personas') }}
                @endif
            </flux:button>
        </div>
    </div>

    {{-- Generated Personas Section --}}
    <div class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-zinc-900">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">{{ __('products.generated_personas') }}</h3>

        @if ($product->personas->isEmpty())
            <div class="py-12 text-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">{{ __('products.no_personas_yet') }}</h3>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    {{ __('products.generate_personas_to_see_profiles') }}
                </p>
            </div>
        @else
            <div class="space-y-4">
                @foreach ($product->personas as $persona)
                    <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-zinc-900 overflow-hidden">
                        {{-- Persona Header --}}
                        <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                            <button
                                type="button"
                                wire:click="toggleExpansion({{ $persona->id }})"
                                class="flex-1 flex items-center gap-3 text-left"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 dark:text-blue-400 transition-transform {{ $expandedPersonaId === $persona->id ? 'rotate-90' : '' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                                <div class="flex-1">
                                    <h4 class="font-semibold text-gray-900 dark:text-white">{{ $persona->title }}</h4>
                                    @if ($expandedPersonaId !== $persona->id && $persona->description)
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1 line-clamp-1">{{ Str::limit($persona->description, 100) }}</p>
                                    @endif
                                </div>
                            </button>
                            <button
                                wire:click="deletePersona({{ $persona->id }})"
                                wire:confirm="Are you sure you want to delete this persona?"
                                class="ml-4 text-gray-400 hover:text-red-600 dark:hover:text-red-400"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>

                        {{-- Persona Content (Expandable) --}}
                        @if ($expandedPersonaId === $persona->id)
                            <div class="p-4 space-y-4">
                                {{-- Title Field --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        {{ __('products.persona_title') }}
                                    </label>
                                    <input
                                        type="text"
                                        wire:model="personaTitles.{{ $persona->id }}"
                                        class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                        placeholder="{{ __('products.persona_title_placeholder') }}"
                                    >
                                </div>

                                {{-- Description Field --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        {{ __('products.persona_description') }}
                                    </label>
                                    <textarea
                                        rows="6"
                                        wire:model="personaDescriptions.{{ $persona->id }}"
                                        class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                        placeholder="{{ __('products.persona_description_placeholder') }}"
                                    ></textarea>
                                </div>

                                {{-- Main Problem Field --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        {{ __('products.persona_main_problem') }}
                                    </label>
                                    <textarea
                                        rows="1"
                                        wire:model="personaMainProblems.{{ $persona->id }}"
                                        class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                        placeholder="{{ __('products.persona_main_problem_placeholder') }}"
                                    ></textarea>
                                </div>

                                {{-- Action Buttons --}}
                                <div class="flex items-center gap-2">
                                    <flux:button
                                        wire:click="updatePersona({{ $persona->id }})"
                                        size="sm"
                                        variant="primary"
                                    >
                                        {{ __('products.save_changes') }}
                                    </flux:button>

                                    <flux:button
                                        wire:click="toggleExpansion({{ $persona->id }})"
                                        size="sm"
                                        variant="ghost"
                                    >
                                        {{ __('cancel') }}
                                    </flux:button>
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
