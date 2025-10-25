<div class="flex h-full w-full flex-1 flex-col gap-4">
    {{-- Tab Navigation --}}
    <x-product-tabs :product="$product" active="analysis" />

    {{-- Generate/Regenerate Analysis Button --}}
    <div class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-zinc-900">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                    {{ $analysis ? __('Analysis Generated') : __('Generate Analysis') }}
                </h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    {{ $analysis ? __('You can regenerate the analysis or edit the fields below') : __('Click the button to generate AI-powered marketing analysis') }}
                </p>
            </div>

            <flux:button
                wire:click="generateAnalysis"
                variant="primary"
                :disabled="$isGenerating"
            >
                @if ($isGenerating)
                    <svg class="mr-2 h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    {{ __('Generating...') }}
                @else
                    {{ $analysis ? __('Regenerate Analysis') : __('Generate Analysis') }}
                @endif
            </flux:button>
        </div>
    </div>

    {{-- Analysis Fields --}}
    @if ($analysis || $isGenerating)
        <form wire:submit="save">
            <div class="space-y-6">
                {{-- Core Function and Use --}}
                <div class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-zinc-900">
                    <flux:label>{{ __('Core Function and Use') }}</flux:label>
                    <textarea
                        wire:model="core_function_and_use"
                        rows="6"
                        class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400"
                        placeholder="{{ __('Describe the core function and use of the product...') }}"
                    ></textarea>
                </div>

                {{-- Features --}}
                <div class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-zinc-900">
                    <div class="flex items-center justify-between mb-4">
                        <flux:label>{{ __('Features') }}</flux:label>
                        <flux:button wire:click="addFeature" size="sm" variant="primary" icon="plus">
                            {{ __('Add Feature') }}
                        </flux:button>
                    </div>

                    <div class="space-y-2">
                        @forelse ($features as $index => $feature)
                            <div class="flex items-center gap-2 group">
                                @if ($editingFeatureIndex === $index)
                                    <input
                                        type="text"
                                        wire:model="features.{{ $index }}"
                                        wire:blur="stopEditingFeature"
                                        wire:keydown.enter="stopEditingFeature"
                                        wire:keydown.escape="stopEditingFeature"
                                        autofocus
                                        class="flex-1 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                        placeholder="{{ __('Enter feature...') }}"
                                    >
                                @else
                                    <div
                                        wire:click="startEditingFeature({{ $index }})"
                                        class="flex-1 cursor-pointer rounded-lg border border-transparent px-3 py-2 text-sm text-gray-900 hover:border-gray-300 hover:bg-gray-50 dark:text-white dark:hover:border-gray-600 dark:hover:bg-gray-800"
                                    >
                                        {{ $feature ?: __('Click to edit...') }}
                                    </div>
                                @endif

                                <button
                                    wire:click="removeFeature({{ $index }})"
                                    type="button"
                                    class="flex-shrink-0 text-gray-400 hover:text-red-600 dark:text-gray-600 dark:hover:text-red-400 transition-colors"
                                    title="{{ __('Delete') }}"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 dark:text-gray-400 italic">{{ __('No features added yet. Click "Add Feature" to get started.') }}</p>
                        @endforelse
                    </div>
                </div>

                {{-- Benefits --}}
                <div class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-zinc-900">
                    <div class="flex items-center justify-between mb-4">
                        <flux:label>{{ __('Benefits') }}</flux:label>
                        <flux:button wire:click="addBenefit" size="sm" variant="primary" icon="plus">
                            {{ __('Add Benefit') }}
                        </flux:button>
                    </div>

                    <div class="space-y-2">
                        @forelse ($benefits as $index => $benefit)
                            <div class="flex items-center gap-2 group">
                                @if ($editingBenefitIndex === $index)
                                    <input
                                        type="text"
                                        wire:model="benefits.{{ $index }}"
                                        wire:blur="stopEditingBenefit"
                                        wire:keydown.enter="stopEditingBenefit"
                                        wire:keydown.escape="stopEditingBenefit"
                                        autofocus
                                        class="flex-1 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                        placeholder="{{ __('Enter benefit...') }}"
                                    >
                                @else
                                    <div
                                        wire:click="startEditingBenefit({{ $index }})"
                                        class="flex-1 cursor-pointer rounded-lg border border-transparent px-3 py-2 text-sm text-gray-900 hover:border-gray-300 hover:bg-gray-50 dark:text-white dark:hover:border-gray-600 dark:hover:bg-gray-800"
                                    >
                                        {{ $benefit ?: __('Click to edit...') }}
                                    </div>
                                @endif

                                <button
                                    wire:click="removeBenefit({{ $index }})"
                                    type="button"
                                    class="flex-shrink-0 text-gray-400 hover:text-red-600 dark:text-gray-600 dark:hover:text-red-400 transition-colors"
                                    title="{{ __('Delete') }}"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 dark:text-gray-400 italic">{{ __('No benefits added yet. Click "Add Benefit" to get started.') }}</p>
                        @endforelse
                    </div>
                </div>

                {{-- Problems --}}
                <div class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-zinc-900">
                    <div class="flex items-center justify-between mb-4">
                        <flux:label>{{ __('Problems Solved') }}</flux:label>
                        <flux:button wire:click="addProblem" size="sm" variant="primary" icon="plus">
                            {{ __('Add Problem') }}
                        </flux:button>
                    </div>

                    <div class="space-y-2">
                        @forelse ($problems as $index => $problem)
                            <div class="flex items-center gap-2 group">
                                @if ($editingProblemIndex === $index)
                                    <input
                                        type="text"
                                        wire:model="problems.{{ $index }}"
                                        wire:blur="stopEditingProblem"
                                        wire:keydown.enter="stopEditingProblem"
                                        wire:keydown.escape="stopEditingProblem"
                                        autofocus
                                        class="flex-1 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                        placeholder="{{ __('Enter problem...') }}"
                                    >
                                @else
                                    <div
                                        wire:click="startEditingProblem({{ $index }})"
                                        class="flex-1 cursor-pointer rounded-lg border border-transparent px-3 py-2 text-sm text-gray-900 hover:border-gray-300 hover:bg-gray-50 dark:text-white dark:hover:border-gray-600 dark:hover:bg-gray-800"
                                    >
                                        {{ $problem ?: __('Click to edit...') }}
                                    </div>
                                @endif

                                <button
                                    wire:click="removeProblem({{ $index }})"
                                    type="button"
                                    class="flex-shrink-0 text-gray-400 hover:text-red-600 dark:text-gray-600 dark:hover:text-red-400 transition-colors"
                                    title="{{ __('Delete') }}"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 dark:text-gray-400 italic">{{ __('No problems added yet. Click "Add Problem" to get started.') }}</p>
                        @endforelse
                    </div>
                </div>

                {{-- Goals --}}
                <div class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-zinc-900">
                    <div class="flex items-center justify-between mb-4">
                        <flux:label>{{ __('Customer Goals') }}</flux:label>
                        <flux:button wire:click="addGoal" size="sm" variant="primary" icon="plus">
                            {{ __('Add Goal') }}
                        </flux:button>
                    </div>

                    <div class="space-y-2">
                        @forelse ($goals as $index => $goal)
                            <div class="flex items-center gap-2 group">
                                @if ($editingGoalIndex === $index)
                                    <input
                                        type="text"
                                        wire:model="goals.{{ $index }}"
                                        wire:blur="stopEditingGoal"
                                        wire:keydown.enter="stopEditingGoal"
                                        wire:keydown.escape="stopEditingGoal"
                                        autofocus
                                        class="flex-1 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                        placeholder="{{ __('Enter goal...') }}"
                                    >
                                @else
                                    <div
                                        wire:click="startEditingGoal({{ $index }})"
                                        class="flex-1 cursor-pointer rounded-lg border border-transparent px-3 py-2 text-sm text-gray-900 hover:border-gray-300 hover:bg-gray-50 dark:text-white dark:hover:border-gray-600 dark:hover:bg-gray-800"
                                    >
                                        {{ $goal ?: __('Click to edit...') }}
                                    </div>
                                @endif

                                <button
                                    wire:click="removeGoal({{ $index }})"
                                    type="button"
                                    class="flex-shrink-0 text-gray-400 hover:text-red-600 dark:text-gray-600 dark:hover:text-red-400 transition-colors"
                                    title="{{ __('Delete') }}"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 dark:text-gray-400 italic">{{ __('No goals added yet. Click "Add Goal" to get started.') }}</p>
                        @endforelse
                    </div>
                </div>

                {{-- Emotions --}}
                <div class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-zinc-900">
                    <div class="flex items-center justify-between mb-4">
                        <flux:label>{{ __('Emotional Triggers') }}</flux:label>
                        <flux:button wire:click="addEmotion" size="sm" variant="primary" icon="plus">
                            {{ __('Add Emotion') }}
                        </flux:button>
                    </div>

                    <div class="space-y-2">
                        @forelse ($emotions as $index => $emotion)
                            <div class="flex items-center gap-2 group">
                                @if ($editingEmotionIndex === $index)
                                    <input
                                        type="text"
                                        wire:model="emotions.{{ $index }}"
                                        wire:blur="stopEditingEmotion"
                                        wire:keydown.enter="stopEditingEmotion"
                                        wire:keydown.escape="stopEditingEmotion"
                                        autofocus
                                        class="flex-1 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                        placeholder="{{ __('Enter emotion...') }}"
                                    >
                                @else
                                    <div
                                        wire:click="startEditingEmotion({{ $index }})"
                                        class="flex-1 cursor-pointer rounded-lg border border-transparent px-3 py-2 text-sm text-gray-900 hover:border-gray-300 hover:bg-gray-50 dark:text-white dark:hover:border-gray-600 dark:hover:bg-gray-800"
                                    >
                                        {{ $emotion ?: __('Click to edit...') }}
                                    </div>
                                @endif

                                <button
                                    wire:click="removeEmotion({{ $index }})"
                                    type="button"
                                    class="flex-shrink-0 text-gray-400 hover:text-red-600 dark:text-gray-600 dark:hover:text-red-400 transition-colors"
                                    title="{{ __('Delete') }}"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 dark:text-gray-400 italic">{{ __('No emotions added yet. Click "Add Emotion" to get started.') }}</p>
                        @endforelse
                    </div>
                </div>

                {{-- Objections --}}
                <div class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-zinc-900">
                    <div class="flex items-center justify-between mb-4">
                        <flux:label>{{ __('Customer Objections') }}</flux:label>
                        <flux:button wire:click="addObjection" size="sm" variant="primary" icon="plus">
                            {{ __('Add Objection') }}
                        </flux:button>
                    </div>

                    <div class="space-y-2">
                        @forelse ($objections as $index => $objection)
                            <div class="flex items-center gap-2 group">
                                @if ($editingObjectionIndex === $index)
                                    <input
                                        type="text"
                                        wire:model="objections.{{ $index }}"
                                        wire:blur="stopEditingObjection"
                                        wire:keydown.enter="stopEditingObjection"
                                        wire:keydown.escape="stopEditingObjection"
                                        autofocus
                                        class="flex-1 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                        placeholder="{{ __('Enter objection...') }}"
                                    >
                                @else
                                    <div
                                        wire:click="startEditingObjection({{ $index }})"
                                        class="flex-1 cursor-pointer rounded-lg border border-transparent px-3 py-2 text-sm text-gray-900 hover:border-gray-300 hover:bg-gray-50 dark:text-white dark:hover:border-gray-600 dark:hover:bg-gray-800"
                                    >
                                        {{ $objection ?: __('Click to edit...') }}
                                    </div>
                                @endif

                                <button
                                    wire:click="removeObjection({{ $index }})"
                                    type="button"
                                    class="flex-shrink-0 text-gray-400 hover:text-red-600 dark:text-gray-600 dark:hover:text-red-400 transition-colors"
                                    title="{{ __('Delete') }}"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 dark:text-gray-400 italic">{{ __('No objections added yet. Click "Add Objection" to get started.') }}</p>
                        @endforelse
                    </div>
                </div>

                {{-- FAQs --}}
                <div class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-zinc-900">
                    <div class="flex items-center justify-between mb-4">
                        <flux:label>{{ __('Frequently Asked Questions') }}</flux:label>
                        <flux:button wire:click="addFaq" size="sm" variant="primary" icon="plus">
                            {{ __('Add FAQ') }}
                        </flux:button>
                    </div>

                    <div class="space-y-4">
                        @forelse ($faqs as $index => $faq)
                            <div class="rounded-lg border border-gray-200 p-4 dark:border-gray-700 group hover:border-gray-300 dark:hover:border-gray-600 transition-colors">
                                <div class="flex items-start justify-between gap-2 mb-3">
                                    <div class="flex-1">
                                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Question') }}</label>
                                        @if ($editingFaqIndex === $index)
                                            <input
                                                type="text"
                                                wire:model="faqs.{{ $index }}.question"
                                                class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                                placeholder="{{ __('Enter question...') }}"
                                            >
                                        @else
                                            <div
                                                wire:click="startEditingFaq({{ $index }})"
                                                class="cursor-pointer rounded-lg border border-transparent px-3 py-2 text-sm text-gray-900 hover:border-gray-300 hover:bg-gray-50 dark:text-white dark:hover:border-gray-600 dark:hover:bg-gray-800"
                                            >
                                                {{ $faq['question'] ?? __('Click to add question...') }}
                                            </div>
                                        @endif
                                    </div>

                                    <button
                                        wire:click="removeFaq({{ $index }})"
                                        type="button"
                                        class="flex-shrink-0 text-gray-400 hover:text-red-600 dark:text-gray-600 dark:hover:text-red-400 transition-colors"
                                        title="{{ __('Delete') }}"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>

                                <div>
                                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Answer') }}</label>
                                    @if ($editingFaqIndex === $index)
                                        <textarea
                                            wire:model="faqs.{{ $index }}.answer"
                                            rows="3"
                                            class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                            placeholder="{{ __('Enter answer...') }}"
                                        ></textarea>
                                    @else
                                        <div
                                            wire:click="startEditingFaq({{ $index }})"
                                            class="cursor-pointer rounded-lg border border-transparent px-3 py-2 text-sm text-gray-900 hover:border-gray-300 hover:bg-gray-50 dark:text-white dark:hover:border-gray-600 dark:hover:bg-gray-800 min-h-[60px]"
                                        >
                                            {{ $faq['answer'] ?? __('Click to add answer...') }}
                                        </div>
                                    @endif
                                </div>

                                @if ($editingFaqIndex === $index)
                                    <div class="mt-3 flex gap-2">
                                        <flux:button wire:click="stopEditingFaq" size="sm" variant="ghost">
                                            {{ __('Done') }}
                                        </flux:button>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 dark:text-gray-400 italic">{{ __('No FAQs added yet. Click "Add FAQ" to get started.') }}</p>
                        @endforelse
                    </div>
                </div>

                {{-- Save Button --}}
                <div class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-zinc-900">
                    <div class="flex items-center gap-4">
                        <flux:button type="submit" variant="primary">
                            {{ __('Save Analysis') }}
                        </flux:button>
                        <flux:button :href="route('products.index')" wire:navigate variant="ghost">
                            {{ __('Cancel') }}
                        </flux:button>
                    </div>
                </div>
            </div>
        </form>
    @endif
</div>
