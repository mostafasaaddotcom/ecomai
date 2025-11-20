<div class="flex h-full w-full flex-1 flex-col gap-4">
    {{-- Tab Navigation --}}
    <x-product-tabs :product="$product" active="funnel-scripts" />

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
                        {{ __('products.analysis_required_for_funnel_scripts') }}
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

    {{-- Personas Check Alert --}}
    @if ($product->personas->isEmpty())
        <div class="rounded-xl border border-yellow-200 bg-yellow-50 p-6 dark:border-yellow-800 dark:bg-yellow-900/20">
            <div class="flex items-start gap-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-600 dark:text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-yellow-900 dark:text-yellow-100">{{ __('products.personas_required') }}</h3>
                    <p class="mt-1 text-sm text-yellow-800 dark:text-yellow-300">
                        {{ __('products.persona_required_for_funnel_scripts') }}
                    </p>
                    <div class="mt-4">
                        <flux:button :href="route('products.personas', $product)" wire:navigate variant="primary" icon="user-group">
                            {{ __('products.generate_personas') }}
                        </flux:button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Generate Funnel Scripts Section --}}
    <div class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-zinc-900">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('products.funnel_scripts') }}</h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ __('products.funnel_scripts_description') }}</p>
            </div>
            <flux:modal.trigger name="generate-funnel-scripts">
                <flux:button
                    variant="primary"
                    icon="sparkles"
                    :disabled="!$product->hasAnalysis() || $product->personas->isEmpty()"
                >
                    {{ __('products.generate_funnel_scripts') }}
                </flux:button>
            </flux:modal.trigger>
        </div>
    </div>

    {{-- Generated Funnel Scripts Section --}}
    <div class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-zinc-900">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">{{ __('products.generated_funnel_scripts') }}</h3>

        @if ($product->funnelScripts->isEmpty())
            <div class="py-12 text-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">{{ __('products.no_funnel_scripts_yet') }}</h3>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    {{ __('products.generate_funnel_scripts_to_see_results') }}
                </p>
            </div>
        @else
            <div class="grid gap-4 lg:grid-cols-2 xl:grid-cols-3">
                @foreach ($product->funnelScripts as $script)
                    <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-zinc-900 overflow-hidden">
                        {{-- Script Header --}}
                        <div class="p-4 bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                            <div class="flex items-start justify-between gap-2 mb-2">
                                <div class="flex flex-wrap gap-2">
                                    <span class="inline-flex items-center rounded-full bg-purple-100 px-2.5 py-0.5 text-xs font-medium text-purple-800 dark:bg-purple-900 dark:text-purple-200">
                                        {{ $script->getStageLabel() }}
                                    </span>
                                    @if ($script->formula)
                                        <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                            {{ $script->formula }}
                                        </span>
                                    @endif
                                </div>
                                <button
                                    wire:click="deleteScript({{ $script->id }})"
                                    wire:confirm="{{ __('products.delete_funnel_script_confirmation') }}"
                                    class="text-gray-400 hover:text-red-600 dark:hover:text-red-400"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>

                            {{-- Metadata --}}
                            <div class="space-y-1 text-xs text-gray-600 dark:text-gray-400">
                                <div class="flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    <span>{{ $script->productPersona->title }}</span>
                                </div>
                                @if ($script->language)
                                    <div class="flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129" />
                                        </svg>
                                        <span>{{ $script->language }}</span>
                                    </div>
                                @endif
                                @if ($script->tone)
                                    <div class="flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                                        </svg>
                                        <span>{{ $script->tone }}</span>
                                    </div>
                                @endif
                                @if ($script->angle)
                                    <div class="flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                        </svg>
                                        <span>{{ $script->angle }}</span>
                                    </div>
                                @endif
                            </div>

                            {{-- Expand/Collapse Button --}}
                            <button
                                wire:click="toggleExpansion({{ $script->id }})"
                                class="mt-3 w-full flex items-center justify-center gap-2 text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300"
                            >
                                @if ($expandedScriptId === $script->id)
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                    </svg>
                                    {{ __('products.hide_content') }}
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                    {{ __('products.show_content') }}
                                @endif
                            </button>
                        </div>

                        {{-- Script Content (Expandable) --}}
                        @if ($expandedScriptId === $script->id)
                            <div class="p-4 space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        {{ __('products.script_content') }}
                                    </label>
                                    <textarea
                                        rows="12"
                                        wire:model.defer="scriptContents.{{ $script->id }}"
                                        class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                        placeholder="{{ __('products.script_content_placeholder') }}"
                                    ></textarea>
                                </div>

                                <div class="flex items-center gap-2">
                                    <flux:button
                                        wire:click="updateScript({{ $script->id }})"
                                        size="sm"
                                        variant="primary"
                                    >
                                        {{ __('products.save_changes') }}
                                    </flux:button>
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Generate Modal --}}
    <flux:modal name="generate-funnel-scripts" class="md:w-2xl">
        <form wire:submit="generateScripts">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">{{ __('products.generate_funnel_scripts') }}</flux:heading>
                    <flux:subheading>{{ __('products.configure_funnel_script_generation') }}</flux:subheading>
                </div>

                {{-- Persona Selection --}}
                <div>
                    <flux:select
                        wire:model="selectedPersonaId"
                        label="{{ __('products.select_persona') }}"
                        placeholder="{{ __('products.choose_persona') }}"
                    >
                        @foreach ($product->personas as $persona)
                            <option value="{{ $persona->id }}">{{ $persona->title }}</option>
                        @endforeach
                    </flux:select>
                </div>

                {{-- Awareness Stages --}}
                <div>
                    <flux:label>{{ __('products.awareness_stages') }}</flux:label>
                    <div class="mt-2 grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Unaware Stage --}}
                        <div class="flex items-center justify-between p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg border border-purple-200 dark:border-purple-800">
                            <div class="flex-1">
                                <h4 class="font-semibold text-purple-900 dark:text-purple-100">{{ __('products.stage_unaware') }}</h4>
                            </div>
                            <div class="flex items-center gap-3">
                                <button
                                    type="button"
                                    wire:click="decrementStageCount('unaware')"
                                    class="flex h-8 w-8 items-center justify-center rounded-full bg-purple-200 text-purple-900 hover:bg-purple-300 dark:bg-purple-800 dark:text-purple-100 dark:hover:bg-purple-700"
                                    :disabled="$isGenerating"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                    </svg>
                                </button>
                                <span class="text-xl font-bold text-purple-900 dark:text-purple-100 min-w-[2rem] text-center">{{ $unawareCount }}</span>
                                <button
                                    type="button"
                                    wire:click="incrementStageCount('unaware')"
                                    class="flex h-8 w-8 items-center justify-center rounded-full bg-purple-200 text-purple-900 hover:bg-purple-300 dark:bg-purple-800 dark:text-purple-100 dark:hover:bg-purple-700"
                                    :disabled="$isGenerating"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        {{-- Problem Aware Stage --}}
                        <div class="flex items-center justify-between p-4 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200 dark:border-red-800">
                            <div class="flex-1">
                                <h4 class="font-semibold text-red-900 dark:text-red-100">{{ __('products.stage_problem_aware') }}</h4>
                            </div>
                            <div class="flex items-center gap-3">
                                <button
                                    type="button"
                                    wire:click="decrementStageCount('problemAware')"
                                    class="flex h-8 w-8 items-center justify-center rounded-full bg-red-200 text-red-900 hover:bg-red-300 dark:bg-red-800 dark:text-red-100 dark:hover:bg-red-700"
                                    :disabled="$isGenerating"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                    </svg>
                                </button>
                                <span class="text-xl font-bold text-red-900 dark:text-red-100 min-w-[2rem] text-center">{{ $problemAwareCount }}</span>
                                <button
                                    type="button"
                                    wire:click="incrementStageCount('problemAware')"
                                    class="flex h-8 w-8 items-center justify-center rounded-full bg-red-200 text-red-900 hover:bg-red-300 dark:bg-red-800 dark:text-red-100 dark:hover:bg-red-700"
                                    :disabled="$isGenerating"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        {{-- Solution Aware Stage --}}
                        <div class="flex items-center justify-between p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg border border-yellow-200 dark:border-yellow-800">
                            <div class="flex-1">
                                <h4 class="font-semibold text-yellow-900 dark:text-yellow-100">{{ __('products.stage_solution_aware') }}</h4>
                            </div>
                            <div class="flex items-center gap-3">
                                <button
                                    type="button"
                                    wire:click="decrementStageCount('solutionAware')"
                                    class="flex h-8 w-8 items-center justify-center rounded-full bg-yellow-200 text-yellow-900 hover:bg-yellow-300 dark:bg-yellow-800 dark:text-yellow-100 dark:hover:bg-yellow-700"
                                    :disabled="$isGenerating"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                    </svg>
                                </button>
                                <span class="text-xl font-bold text-yellow-900 dark:text-yellow-100 min-w-[2rem] text-center">{{ $solutionAwareCount }}</span>
                                <button
                                    type="button"
                                    wire:click="incrementStageCount('solutionAware')"
                                    class="flex h-8 w-8 items-center justify-center rounded-full bg-yellow-200 text-yellow-900 hover:bg-yellow-300 dark:bg-yellow-800 dark:text-yellow-100 dark:hover:bg-yellow-700"
                                    :disabled="$isGenerating"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        {{-- Product Aware Stage --}}
                        <div class="flex items-center justify-between p-4 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                            <div class="flex-1">
                                <h4 class="font-semibold text-green-900 dark:text-green-100">{{ __('products.stage_product_aware') }}</h4>
                            </div>
                            <div class="flex items-center gap-3">
                                <button
                                    type="button"
                                    wire:click="decrementStageCount('productAware')"
                                    class="flex h-8 w-8 items-center justify-center rounded-full bg-green-200 text-green-900 hover:bg-green-300 dark:bg-green-800 dark:text-green-100 dark:hover:bg-green-700"
                                    :disabled="$isGenerating"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                    </svg>
                                </button>
                                <span class="text-xl font-bold text-green-900 dark:text-green-100 min-w-[2rem] text-center">{{ $productAwareCount }}</span>
                                <button
                                    type="button"
                                    wire:click="incrementStageCount('productAware')"
                                    class="flex h-8 w-8 items-center justify-center rounded-full bg-green-200 text-green-900 hover:bg-green-300 dark:bg-green-800 dark:text-green-100 dark:hover:bg-green-700"
                                    :disabled="$isGenerating"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        {{-- Most Aware Stage --}}
                        <div class="flex items-center justify-between p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800 md:col-span-2">
                            <div class="flex-1">
                                <h4 class="font-semibold text-blue-900 dark:text-blue-100">{{ __('products.stage_most_aware') }}</h4>
                            </div>
                            <div class="flex items-center gap-3">
                                <button
                                    type="button"
                                    wire:click="decrementStageCount('mostAware')"
                                    class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-200 text-blue-900 hover:bg-blue-300 dark:bg-blue-800 dark:text-blue-100 dark:hover:bg-blue-700"
                                    :disabled="$isGenerating"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                    </svg>
                                </button>
                                <span class="text-xl font-bold text-blue-900 dark:text-blue-100 min-w-[2rem] text-center">{{ $mostAwareCount }}</span>
                                <button
                                    type="button"
                                    wire:click="incrementStageCount('mostAware')"
                                    class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-200 text-blue-900 hover:bg-blue-300 dark:bg-blue-800 dark:text-blue-100 dark:hover:bg-blue-700"
                                    :disabled="$isGenerating"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Formula Selection --}}
                <div>
                    <flux:label>{{ __('products.select_formulas') }}</flux:label>
                    <div class="mt-2 grid grid-cols-1 md:grid-cols-2 gap-3">
                        @foreach ($formulas as $key => $label)
                            <label class="flex items-start gap-3 p-3 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700">
                                <input
                                    type="checkbox"
                                    wire:model="selectedFormulas"
                                    value="{{ $key }}"
                                    class="mt-0.5 h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700"
                                >
                                <span class="text-sm text-gray-900 dark:text-white">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Language Selection --}}
                <div>
                    <flux:select
                        wire:model="language"
                        label="{{ __('products.language_dialect') }}"
                        placeholder="{{ __('products.select_language') }}"
                    >
                        @foreach ($languages as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </flux:select>
                </div>

                {{-- Total Count Display --}}
                <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-blue-900 dark:text-blue-100">{{ __('products.total_scripts_to_generate') }}:</span>
                        <span class="text-2xl font-bold text-blue-900 dark:text-blue-100">
                            {{ $unawareCount + $problemAwareCount + $solutionAwareCount + $productAwareCount + $mostAwareCount }}
                        </span>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex gap-2">
                    <flux:button type="submit" variant="primary" :disabled="$isGenerating">
                        @if ($isGenerating)
                            <svg class="mr-2 h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            {{ __('products.generating') }}
                        @else
                            {{ __('products.generate') }}
                        @endif
                    </flux:button>
                    <flux:modal.close>
                        <flux:button variant="ghost">{{ __('cancel') }}</flux:button>
                    </flux:modal.close>
                </div>
            </div>
        </form>
    </flux:modal>
</div>
