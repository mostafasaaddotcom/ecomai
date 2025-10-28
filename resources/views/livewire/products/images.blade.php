<div class="flex h-full w-full flex-1 flex-col gap-4">
    {{-- Tab Navigation --}}
    <x-product-tabs :product="$product" active="images" />

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
                        {{ __('Please generate a product analysis first before creating AI images. The analysis provides essential information for generating high-quality product images.') }}
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

    {{-- Generate/Upload Images Button --}}
    <div class="flex justify-between items-center">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Product Images') }}</h2>
        <flux:modal.trigger name="images-modal">
            <flux:button
                variant="primary"
                icon="photo"
                :disabled="!$product->hasAnalysis()"
                x-data=""
                x-on:click.prevent="$dispatch('open-modal', 'images-modal')"
            >
                {{ __('Generate / Upload Images') }}
            </flux:button>
        </flux:modal.trigger>
    </div>

    {{-- Generate/Upload Images Modal --}}
    <flux:modal name="images-modal" class="max-w-4xl">
        <div x-data="{ activeTab: 'generate' }" class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('Product Images') }}</flux:heading>
                <flux:subheading>{{ __('Generate AI images or upload your own product images') }}</flux:subheading>
            </div>

            {{-- Tab Buttons --}}
            <div class="flex gap-2 border-b border-gray-200 dark:border-gray-700">
                <button
                    type="button"
                    x-on:click="activeTab = 'generate'"
                    :class="activeTab === 'generate' ? 'border-blue-500 text-blue-600 dark:border-blue-400 dark:text-blue-400' : 'border-transparent text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200'"
                    class="border-b-2 px-4 py-2 text-sm font-medium transition-colors"
                >
                    {{ __('Generate Images') }}
                </button>
                <button
                    type="button"
                    x-on:click="activeTab = 'upload'"
                    :class="activeTab === 'upload' ? 'border-blue-500 text-blue-600 dark:border-blue-400 dark:text-blue-400' : 'border-transparent text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200'"
                    class="border-b-2 px-4 py-2 text-sm font-medium transition-colors"
                >
                    {{ __('Upload Images') }}
                </button>
            </div>

            {{-- Generate Images Form --}}
            <form x-show="activeTab === 'generate'" wire:submit="generateImages" class="space-y-6">
                {{-- Ad Country Selection --}}
                <div>
                    <flux:label>{{ __('Ad Country') }} <span class="text-red-500">*</span></flux:label>
                    <select
                        wire:model="ad_country"
                        class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                        required
                    >
                        <option value="">{{ __('Select ad country...') }}</option>
                        @foreach ($countries as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Prompt Language Selection --}}
                <div>
                    <flux:label>{{ __('Prompt Language') }}</flux:label>
                    <div class="mt-2 flex gap-4">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input
                                type="radio"
                                wire:model="promptLanguage"
                                value="english"
                                class="rounded-full border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700"
                            >
                            <span class="text-sm text-gray-900 dark:text-white">{{ __('English') }}</span>
                            <span class="rounded-full bg-blue-100 px-2 py-0.5 text-xs font-medium text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">{{ __('Recommended') }}</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input
                                type="radio"
                                wire:model="promptLanguage"
                                value="arabic"
                                class="rounded-full border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700"
                            >
                            <span class="text-sm text-gray-900 dark:text-white">{{ __('Arabic') }}</span>
                        </label>
                    </div>
                </div>

                {{-- Aspect Ratio Selection --}}
                <div>
                    <flux:label>{{ __('Aspect Ratio') }} <span class="text-red-500">*</span></flux:label>
                    <div class="mt-2 flex gap-4">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input
                                type="radio"
                                wire:model="aspect_ratio"
                                value="9:16"
                                class="rounded-full border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700"
                            >
                            <span class="text-sm text-gray-900 dark:text-white">{{ __('9:16') }}</span>
                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ __('(Vertical)') }}</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input
                                type="radio"
                                wire:model="aspect_ratio"
                                value="16:9"
                                class="rounded-full border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700"
                            >
                            <span class="text-sm text-gray-900 dark:text-white">{{ __('16:9') }}</span>
                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ __('(Horizontal)') }}</span>
                        </label>
                    </div>
                </div>

                {{-- Image Type Cards --}}
                <div>
                    <flux:label>{{ __('Image Types') }}</flux:label>
                    <p class="mt-1 mb-3 text-xs text-gray-500 dark:text-gray-400">{{ __('Select how many images of each type to generate') }}</p>

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        {{-- Product Only Card --}}
                        <div class="rounded-lg border-2 border-blue-200 bg-blue-50 p-4 dark:border-blue-800 dark:bg-blue-900/20">
                            <div class="mb-3 flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                                <h3 class="font-medium text-blue-900 dark:text-blue-100">{{ __('Product Only') }}</h3>
                            </div>
                            <div class="flex items-center justify-center gap-3">
                                <button
                                    type="button"
                                    wire:click="decrementCount('product_only')"
                                    class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-200 text-blue-900 hover:bg-blue-300 dark:bg-blue-800 dark:text-blue-100 dark:hover:bg-blue-700"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                    </svg>
                                </button>
                                <span class="text-2xl font-bold text-blue-900 dark:text-blue-100">{{ $productOnlyCount }}</span>
                                <button
                                    type="button"
                                    wire:click="incrementCount('product_only')"
                                    class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-200 text-blue-900 hover:bg-blue-300 dark:bg-blue-800 dark:text-blue-100 dark:hover:bg-blue-700"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        {{-- Lifestyle Card --}}
                        <div class="rounded-lg border-2 border-green-200 bg-green-50 p-4 dark:border-green-800 dark:bg-green-900/20">
                            <div class="mb-3 flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                </svg>
                                <h3 class="font-medium text-green-900 dark:text-green-100">{{ __('Lifestyle') }}</h3>
                            </div>
                            <div class="flex items-center justify-center gap-3">
                                <button
                                    type="button"
                                    wire:click="decrementCount('lifestyle')"
                                    class="flex h-8 w-8 items-center justify-center rounded-full bg-green-200 text-green-900 hover:bg-green-300 dark:bg-green-800 dark:text-green-100 dark:hover:bg-green-700"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                    </svg>
                                </button>
                                <span class="text-2xl font-bold text-green-900 dark:text-green-100">{{ $lifestyleCount }}</span>
                                <button
                                    type="button"
                                    wire:click="incrementCount('lifestyle')"
                                    class="flex h-8 w-8 items-center justify-center rounded-full bg-green-200 text-green-900 hover:bg-green-300 dark:bg-green-800 dark:text-green-100 dark:hover:bg-green-700"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        {{-- UGC Scene Card --}}
                        <div class="rounded-lg border-2 border-purple-200 bg-purple-50 p-4 dark:border-purple-800 dark:bg-purple-900/20">
                            <div class="mb-3 flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                                <h3 class="font-medium text-purple-900 dark:text-purple-100">{{ __('UGC Scene') }}</h3>
                            </div>
                            <div class="flex items-center justify-center gap-3">
                                <button
                                    type="button"
                                    wire:click="decrementCount('ugc_scene')"
                                    class="flex h-8 w-8 items-center justify-center rounded-full bg-purple-200 text-purple-900 hover:bg-purple-300 dark:bg-purple-800 dark:text-purple-100 dark:hover:bg-purple-700"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                    </svg>
                                </button>
                                <span class="text-2xl font-bold text-purple-900 dark:text-purple-100">{{ $ugcSceneCount }}</span>
                                <button
                                    type="button"
                                    wire:click="incrementCount('ugc_scene')"
                                    class="flex h-8 w-8 items-center justify-center rounded-full bg-purple-200 text-purple-900 hover:bg-purple-300 dark:bg-purple-800 dark:text-purple-100 dark:hover:bg-purple-700"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        {{-- Expert Card --}}
                        <div class="rounded-lg border-2 border-orange-200 bg-orange-50 p-4 dark:border-orange-800 dark:bg-orange-900/20">
                            <div class="mb-3 flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-orange-600 dark:text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path d="M12 14l9-5-9-5-9 5 9 5z" />
                                    <path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222" />
                                </svg>
                                <h3 class="font-medium text-orange-900 dark:text-orange-100">{{ __('Expert') }}</h3>
                            </div>
                            <div class="flex items-center justify-center gap-3">
                                <button
                                    type="button"
                                    wire:click="decrementCount('expert')"
                                    class="flex h-8 w-8 items-center justify-center rounded-full bg-orange-200 text-orange-900 hover:bg-orange-300 dark:bg-orange-800 dark:text-orange-100 dark:hover:bg-orange-700"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                    </svg>
                                </button>
                                <span class="text-2xl font-bold text-orange-900 dark:text-orange-100">{{ $expertCount }}</span>
                                <button
                                    type="button"
                                    wire:click="incrementCount('expert')"
                                    class="flex h-8 w-8 items-center justify-center rounded-full bg-orange-200 text-orange-900 hover:bg-orange-300 dark:bg-orange-800 dark:text-orange-100 dark:hover:bg-orange-700"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Total Images Info --}}
                @if ($this->totalImages > 0)
                    <div class="rounded-lg bg-blue-50 p-4 dark:bg-blue-900/20">
                        <p class="text-sm text-blue-900 dark:text-blue-100">
                            <strong>{{ __('Total images to generate:') }}</strong> {{ $this->totalImages }}
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
                            {{ __('Generate Images') }}
                        @endif
                    </flux:button>
                </div>
            </form>

            {{-- Upload Images Form --}}
            <div x-show="activeTab === 'upload'" x-data="imageUploadManager()" class="space-y-6">
                {{-- File Upload --}}
                <div>
                    <flux:label>{{ __('Select Images') }} <span class="text-red-500">*</span></flux:label>
                    <input
                        type="file"
                        @change="handleFileSelect($event)"
                        multiple
                        accept="image/*"
                        class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                        :disabled="uploading"
                    >
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('Max file size: 10MB per image. Multiple images allowed.') }}</p>
                </div>

                {{-- Selected Files Preview --}}
                <div x-show="selectedFiles.length > 0" class="space-y-2">
                    <flux:label>{{ __('Selected Files') }}</flux:label>
                    <div class="space-y-1">
                        <template x-for="(file, index) in selectedFiles" :key="index">
                            <div class="flex items-center justify-between rounded-lg bg-gray-50 p-2 text-sm dark:bg-gray-800">
                                <span class="text-gray-900 dark:text-white" x-text="file.name"></span>
                                <span class="text-gray-500 dark:text-gray-400" x-text="formatFileSize(file.size)"></span>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Upload Progress --}}
                <div x-show="uploading" class="space-y-2">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-700 dark:text-gray-300">{{ __('Uploading...') }}</span>
                        <span class="text-gray-600 dark:text-gray-400" x-text="`${uploadProgress}%`"></span>
                    </div>
                    <div class="h-2 overflow-hidden rounded-full bg-gray-200 dark:bg-gray-700">
                        <div class="h-full bg-blue-500 transition-all duration-300" :style="`width: ${uploadProgress}%`"></div>
                    </div>
                </div>

                {{-- Success Message --}}
                <div x-show="successMessage" class="rounded-lg border border-green-200 bg-green-50 p-3 text-sm text-green-800 dark:border-green-800 dark:bg-green-900/20 dark:text-green-400">
                    <span x-text="successMessage"></span>
                </div>

                {{-- Error Message --}}
                <div x-show="errorMessage" class="rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-800 dark:border-red-800 dark:bg-red-900/20 dark:text-red-400">
                    <span x-text="errorMessage"></span>
                </div>

                {{-- Type Selection --}}
                <div>
                    <flux:label>{{ __('Image Type') }} <span class="text-red-500">*</span></flux:label>
                    <select
                        x-model="uploadType"
                        class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                        :disabled="uploading"
                    >
                        @foreach ($imageTypes as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Modal Footer --}}
                <div class="flex justify-end gap-2">
                    <flux:modal.close>
                        <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
                    </flux:modal.close>

                    <flux:button
                        type="button"
                        @click.prevent="uploadImages()"
                        variant="primary"
                    >
                        {{ __('Upload Images') }}
                    </flux:button>
                </div>
            </div>
        </div>
    </flux:modal>

    {{-- Generated/Uploaded Images Section --}}
    <div class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-zinc-900">
        @if ($product->images->isEmpty())
            <div class="py-12 text-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">{{ __('No Images Yet') }}</h3>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    {{ __('Use the button above to generate AI images or upload your own.') }}
                </p>
            </div>
        @else
            <div class="grid gap-4" style="grid-template-columns: repeat(3, minmax(0, 1fr));">
                @foreach ($product->images as $image)
                    <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-zinc-900 overflow-hidden">
                        {{-- Image Thumbnail --}}
                        @if ($image->image_url)
                            <div class="aspect-square bg-gray-100 dark:bg-gray-800">
                                <img src="{{ $image->image_url }}" alt="{{ $image->type }}" class="h-full w-full object-cover">
                            </div>
                        @else
                            <div class="aspect-square bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                        @endif

                        {{-- Card Content --}}
                        <div class="p-4 space-y-3">
                            {{-- Badges --}}
                            <div class="flex flex-wrap items-center gap-2">
                                <flux:badge
                                    :color="$image->type === 'product_only' ? 'blue' : ($image->type === 'lifestyle' ? 'green' : ($image->type === 'ugc_scene' ? 'purple' : ($image->type === 'expert' ? 'orange' : 'gray')))"
                                    size="sm"
                                >
                                    {{ ucfirst(str_replace('_', ' ', $image->type)) }}
                                </flux:badge>
                                <flux:badge
                                    :color="$image->status === 'completed' ? 'green' : ($image->status === 'failed' ? 'red' : ($image->status === 'image_generating' ? 'yellow' : 'gray'))"
                                    size="sm"
                                >
                                    {{ ucfirst(str_replace('_', ' ', $image->status)) }}
                                </flux:badge>
                                @if ($image->is_ai_generated)
                                    <flux:badge color="indigo" size="sm">AI</flux:badge>
                                @endif
                            </div>

                            {{-- Metadata --}}
                            <div class="text-xs text-gray-500 dark:text-gray-400 space-y-1">
                                @if ($image->aspect_ratio)
                                    <p>{{ __('Aspect Ratio:') }} {{ $image->aspect_ratio }}</p>
                                @endif
                                <p>{{ $image->updated_at->diffForHumans() }}</p>
                            </div>

                            {{-- Prompt (editable for AI images) --}}
                            @if ($image->is_ai_generated)
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Prompt') }}</label>
                                    <textarea
                                        rows="3"
                                        class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                        wire:model.defer="imagePrompts.{{ $image->id }}"
                                    >{{ $image->prompt }}</textarea>
                                </div>
                            @elseif ($image->prompt)
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Prompt') }}</label>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $image->prompt }}</p>
                                </div>
                            @endif

                            {{-- Actions --}}
                            <div class="flex items-center gap-2 flex-wrap">
                                @if ($image->is_ai_generated)
                                    <flux:button
                                        wire:click="regenerateImage({{ $image->id }})"
                                        size="sm"
                                        variant="primary"
                                    >
                                        {{ __('Regenerate') }}
                                    </flux:button>
                                    <flux:button
                                        wire:click="updatePrompt({{ $image->id }})"
                                        size="sm"
                                        variant="ghost"
                                    >
                                        {{ __('Update Prompt') }}
                                    </flux:button>
                                @endif

                                @if ($image->image_url && $image->status === 'completed')
                                    <a href="{{ $image->image_url }}" download class="text-xs">
                                        <flux:button size="sm" variant="ghost">
                                            {{ __('Download') }}
                                        </flux:button>
                                    </a>
                                @endif

                                <button
                                    wire:click="deleteImage({{ $image->id }})"
                                    wire:confirm="Are you sure you want to delete this image?"
                                    class="ml-auto text-gray-400 hover:text-red-600 dark:hover:text-red-400"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

<script>
    function imageUploadManager() {
        return {
            selectedFiles: [],
            uploading: false,
            uploadProgress: 0,
            successMessage: '',
            errorMessage: '',
            uploadType: 'other',

            handleFileSelect(event) {
                this.selectedFiles = Array.from(event.target.files);
                this.successMessage = '';
                this.errorMessage = '';

                // Validate files
                const maxSize = 10 * 1024 * 1024; // 10MB
                const invalidFiles = this.selectedFiles.filter(file => {
                    return !file.type.startsWith('image/') || file.size > maxSize;
                });

                if (invalidFiles.length > 0) {
                    this.errorMessage = 'Some files are invalid. Please ensure all files are images under 10MB.';
                    this.selectedFiles = [];
                    event.target.value = '';
                }
            },

            formatFileSize(bytes) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
            },

            async uploadImages() {
                if (this.selectedFiles.length === 0) {
                    this.errorMessage = 'Please select at least one image';
                    return;
                }

                // Check if CSRF token exists
                const csrfToken = document.querySelector('meta[name="csrf-token"]');
                if (!csrfToken) {
                    this.errorMessage = 'CSRF token not found. Please refresh the page.';
                    console.error('CSRF token meta tag is missing from the page');
                    return;
                }

                this.uploading = true;
                this.uploadProgress = 0;
                this.successMessage = '';
                this.errorMessage = '';

                const formData = new FormData();
                this.selectedFiles.forEach((file, index) => {
                    formData.append('images[]', file);
                });
                formData.append('upload_type', this.uploadType);

                try {
                    const xhr = new XMLHttpRequest();

                    // Track upload progress
                    xhr.upload.addEventListener('progress', (e) => {
                        if (e.lengthComputable) {
                            this.uploadProgress = Math.round((e.loaded / e.total) * 100);
                        }
                    });

                    // Handle response
                    xhr.addEventListener('load', () => {
                        this.uploading = false;

                        if (xhr.status === 200) {
                            try {
                                const response = JSON.parse(xhr.responseText);
                                if (response.success) {
                                    this.successMessage = `Successfully uploaded ${response.count} image(s)`;
                                    this.selectedFiles = [];
                                    this.uploadProgress = 0;

                                    // Refresh Livewire images
                                    @this.call('refreshImages');

                                    // Reset file input
                                    const fileInput = document.querySelector('#upload-tab-content input[type="file"]');
                                    if (fileInput) fileInput.value = '';

                                    // Clear success message after 5 seconds
                                    setTimeout(() => {
                                        this.successMessage = '';
                                    }, 5000);
                                } else {
                                    this.errorMessage = response.message || 'Upload failed';
                                }
                            } catch (e) {
                                this.errorMessage = 'Failed to parse server response';
                                console.error('Parse error:', e, xhr.responseText);
                            }
                        } else {
                            try {
                                const response = JSON.parse(xhr.responseText);
                                // Handle validation errors
                                if (response.errors) {
                                    const errorMessages = Object.values(response.errors).flat();
                                    this.errorMessage = errorMessages.join(', ');
                                } else {
                                    this.errorMessage = response.message || `Upload failed (${xhr.status})`;
                                }
                            } catch (e) {
                                this.errorMessage = `Upload failed (${xhr.status})`;
                                console.error('Error response:', xhr.responseText);
                            }
                        }
                    });

                    // Handle errors
                    xhr.addEventListener('error', () => {
                        this.uploading = false;
                        this.errorMessage = 'Network error occurred. Please check your connection.';
                    });

                    // Send request
                    xhr.open('POST', '{{ asset('products/' . $product->id . '/upload-images') }}');
                    xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken.content);
                    xhr.send(formData);

                } catch (error) {
                    this.uploading = false;
                    this.errorMessage = 'An error occurred: ' + error.message;
                    console.error('Upload error:', error);
                }
            }
        };
    }
</script>
