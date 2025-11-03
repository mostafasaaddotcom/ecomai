<div class="flex h-full w-full flex-1 flex-col gap-4">
    {{-- Tab Navigation --}}
    <x-product-tabs :product="$product" active="ad-creatives" />

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

    {{-- Create New Ad Creative Button --}}
    <div class="flex justify-between items-center">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Ad Creatives') }}</h2>
        <flux:modal.trigger name="ad-creatives-modal">
            <flux:button
                variant="primary"
                icon="sparkles"
                x-data=""
                x-on:click.prevent="$dispatch('open-modal', 'ad-creatives-modal')"
            >
                {{ __('Create New Ad Creative') }}
            </flux:button>
        </flux:modal.trigger>
    </div>

    {{-- Create Ad Creative Modal --}}
    <flux:modal name="ad-creatives-modal" class="max-w-4xl">
        <div x-data="{ activeTab: 'mix' }" class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('Create Ad Creative') }}</flux:heading>
                <flux:subheading>{{ __('Mix video with audio or upload ad creative file') }}</flux:subheading>
            </div>

            {{-- Tab Buttons --}}
            <div class="flex gap-2 border-b border-gray-200 dark:border-gray-700">
                <button
                    type="button"
                    x-on:click="activeTab = 'mix'"
                    :class="activeTab === 'mix' ? 'border-blue-500 text-blue-600 dark:border-blue-400 dark:text-blue-400' : 'border-transparent text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200'"
                    class="border-b-2 px-4 py-2 text-sm font-medium transition-colors"
                >
                    {{ __('Mix Video and Audios') }}
                </button>
                <button
                    type="button"
                    x-on:click="activeTab = 'upload'"
                    :class="activeTab === 'upload' ? 'border-blue-500 text-blue-600 dark:border-blue-400 dark:text-blue-400' : 'border-transparent text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200'"
                    class="border-b-2 px-4 py-2 text-sm font-medium transition-colors"
                >
                    {{ __('Upload') }}
                </button>
            </div>

            {{-- Mix Video and Audio Form --}}
            <form x-show="activeTab === 'mix'" wire:submit="createAndSend" class="space-y-6">
                {{-- Video URL --}}
                <div>
                    <flux:label>{{ __('Video URL') }} <span class="text-red-500">*</span></flux:label>
                    <input
                        type="url"
                        wire:model="videoUrl"
                        placeholder="https://example.com/video.mp4"
                        class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                    >
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('Enter the URL of your video (e.g., from Supabase storage)') }}</p>
                    @error('videoUrl')
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Voice Selection --}}
                <div wire:ignore.self>
                    <flux:label>{{ __('Select Voices') }} <span class="text-red-500">*</span></flux:label>
                    <p class="mt-1 mb-3 text-xs text-gray-500 dark:text-gray-400">{{ __('Choose voices from your product copies (multiple selection allowed)') }}</p>

                    @if ($product->copies->where('voice_url_link', '!=', null)->isEmpty())
                        <div class="rounded-lg border border-yellow-200 bg-yellow-50 p-4 text-sm text-yellow-800 dark:border-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400">
                            {{ __('No voices available. Please generate copywriting with voices first.') }}
                        </div>
                    @else
                        <div class="space-y-2 max-h-64 overflow-y-auto">
                            @foreach ($product->copies->where('voice_url_link', '!=', null) as $copy)
                                <label class="flex items-start gap-3 rounded-lg border-2 p-3 cursor-pointer transition-colors {{ in_array($copy->id, $selectedVoiceIds) ? 'border-blue-500 bg-blue-50 dark:border-blue-400 dark:bg-blue-900/20' : 'border-gray-200 hover:border-gray-300 dark:border-gray-700 dark:hover:border-gray-600' }}">
                                    <input
                                        type="checkbox"
                                        wire:model="selectedVoiceIds"
                                        value="{{ $copy->id }}"
                                        class="mt-1 rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700"
                                    >
                                    <div class="flex-1 space-y-2">
                                        <div class="flex items-center gap-2">
                                            <flux:badge
                                                :color="$copy->type === 'ugc' ? 'blue' : ($copy->type === 'expert' ? 'green' : 'purple')"
                                                size="sm"
                                            >
                                                {{ strtoupper($copy->type) }}
                                            </flux:badge>
                                            @if ($copy->angle)
                                                <span class="text-xs text-gray-600 dark:text-gray-400">{{ $copy->angle }}</span>
                                            @endif
                                        </div>
                                        @if ($copy->content)
                                            <p class="text-sm text-gray-700 dark:text-gray-300 line-clamp-2">{{ $copy->content }}</p>
                                        @endif
                                        <audio controls class="w-full h-8" style="max-height: 32px;">
                                            <source src="{{ $copy->voice_url_link }}" type="audio/mpeg">
                                            {{ __('Your browser does not support the audio element.') }}
                                        </audio>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Optional Metadata Fields --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <flux:label>{{ __('Title (Optional)') }}</flux:label>
                        <input
                            type="text"
                            wire:model="title"
                            class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                        >
                    </div>

                    <div>
                        <flux:label>{{ __('Call to Action') }}</flux:label>
                        <select
                            wire:model="callToActionType"
                            class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                        >
                            <option value="">{{ __('Select CTA...') }}</option>
                            @foreach ($ctaTypes as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <flux:label>{{ __('Message (Optional)') }}</flux:label>
                    <textarea
                        wire:model="message"
                        rows="3"
                        class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                    ></textarea>
                </div>

                <div>
                    <flux:label>{{ __('CTA Link (Optional)') }}</flux:label>
                    <input
                        type="url"
                        wire:model="callToActionLink"
                        class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                        placeholder="https://..."
                    >
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <flux:label>{{ __('Facebook Page ID (Optional)') }}</flux:label>
                        <input
                            type="text"
                            wire:model="pageId"
                            class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                        >
                    </div>

                    <div>
                        <flux:label>{{ __('Instagram User ID (Optional)') }}</flux:label>
                        <input
                            type="text"
                            wire:model="instagramUserId"
                            class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                        >
                    </div>
                </div>

                {{-- Modal Footer --}}
                <div class="flex justify-end gap-2">
                    <flux:modal.close>
                        <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
                    </flux:modal.close>

                    <flux:button
                        type="submit"
                        variant="primary"
                        wire:loading.attr="disabled"
                        icon="sparkles"
                    >
                        @if ($isProcessing)
                            <svg class="mr-2 h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            {{ __('Processing...') }}
                        @else
                            {{ __('Create & Send to N8N') }}
                        @endif
                    </flux:button>
                </div>
            </form>

            {{-- Upload Form --}}
            <form x-show="activeTab === 'upload'" wire:submit="uploadAdCreative" class="space-y-6">
                {{-- File URL --}}
                <div>
                    <flux:label>{{ __('File URL (Video or Image)') }} <span class="text-red-500">*</span></flux:label>
                    <input
                        type="url"
                        wire:model="fileUrl"
                        placeholder="https://example.com/media.mp4"
                        class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                    >
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('Enter the URL of your video or image file') }}</p>
                    @error('fileUrl')
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Optional Metadata Fields --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <flux:label>{{ __('Title (Optional)') }}</flux:label>
                        <input
                            type="text"
                            wire:model="title"
                            class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                        >
                    </div>

                    <div>
                        <flux:label>{{ __('Call to Action') }}</flux:label>
                        <select
                            wire:model="callToActionType"
                            class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                        >
                            <option value="">{{ __('Select CTA...') }}</option>
                            @foreach ($ctaTypes as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <flux:label>{{ __('Message (Optional)') }}</flux:label>
                    <textarea
                        wire:model="message"
                        rows="3"
                        class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                    ></textarea>
                </div>

                <div>
                    <flux:label>{{ __('CTA Link (Optional)') }}</flux:label>
                    <input
                        type="url"
                        wire:model="callToActionLink"
                        class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                        placeholder="https://..."
                    >
                </div>

                {{-- Modal Footer --}}
                <div class="flex justify-end gap-2">
                    <flux:modal.close>
                        <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
                    </flux:modal.close>

                    <flux:button
                        type="submit"
                        variant="primary"
                        wire:loading.attr="disabled"
                        icon="cloud-arrow-up"
                    >
                        @if ($isProcessing)
                            <svg class="mr-2 h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            {{ __('Processing...') }}
                        @else
                            {{ __('Create') }}
                        @endif
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:modal>

    {{-- Ad Creatives Grid --}}
    <div class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-zinc-900">
        @if ($product->adCreatives->isEmpty())
            <div class="py-12 text-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-16 w-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z" />
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">{{ __('No Ad Creatives Yet') }}</h3>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    {{ __('Create your first ad creative using the button above.') }}
                </p>
            </div>
        @else
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($product->adCreatives as $creative)
                    <div class="rounded-lg border border-gray-300 bg-white shadow-sm overflow-hidden dark:border-gray-700 dark:bg-zinc-800">
                        {{-- Facebook-Style Ad Preview --}}
                        <div class="p-3 bg-gray-50 border-b border-gray-200 dark:bg-gray-800 dark:border-gray-700">
                            <div class="flex items-center gap-2">
                                <div class="h-10 w-10 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $product->name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Sponsored</p>
                                </div>
                                <button
                                    wire:click="deleteAdCreative({{ $creative->id }})"
                                    wire:confirm="Are you sure you want to delete this ad creative?"
                                    class="text-gray-400 hover:text-red-600 dark:hover:text-red-400"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        {{-- Ad Content --}}
                        <div class="p-3 space-y-2">
                            @if ($creative->title || $creative->message)
                                <div class="space-y-1">
                                    @if ($creative->title)
                                        <h3 class="font-semibold text-gray-900 dark:text-white text-sm">{{ $creative->title }}</h3>
                                    @endif
                                    @if ($creative->message)
                                        <p class="text-sm text-gray-700 dark:text-gray-300 line-clamp-3">{{ $creative->message }}</p>
                                    @endif
                                </div>
                            @endif

                            {{-- Media Placeholder --}}
                            <div class="aspect-video bg-gray-200 dark:bg-gray-700 rounded flex items-center justify-center">
                                @if ($creative->type === 'video')
                                    @if ($creative->processed_video_url)
                                        {{-- Show N8N-processed video (priority) --}}
                                        <video controls class="w-full h-full object-cover rounded">
                                            <source src="{{ $creative->processed_video_url }}" type="video/mp4">
                                        </video>
                                    @elseif ($creative->original_video_url)
                                        {{-- Show original video with processing badge --}}
                                        <div class="relative w-full h-full">
                                            <video controls class="w-full h-full object-cover rounded">
                                                <source src="{{ $creative->original_video_url }}" type="video/mp4">
                                            </video>
                                            <div class="absolute bottom-2 right-2 bg-yellow-500 text-white px-2 py-1 rounded text-xs font-semibold shadow-lg">
                                                Processing...
                                            </div>
                                        </div>
                                    @else
                                        {{-- No video available --}}
                                        <div class="text-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Processing...</p>
                                        </div>
                                    @endif
                                @else
                                    @if ($creative->thumbnail_url)
                                        <img src="{{ $creative->thumbnail_url }}" alt="Ad Creative" class="w-full h-full object-cover rounded">
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    @endif
                                @endif
                            </div>

                            {{-- CTA Button --}}
                            @if ($creative->call_to_action_type)
                                <div class="pt-2">
                                    <div class="w-full bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white text-center py-2 px-4 rounded font-semibold text-sm">
                                        {{ $ctaTypes[$creative->call_to_action_type] ?? $creative->call_to_action_type }}
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- Metadata Footer --}}
                        <div class="p-3 bg-gray-50 border-t border-gray-200 dark:bg-gray-800 dark:border-gray-700">
                            <div class="flex flex-wrap gap-2">
                                <flux:badge :color="$creative->type === 'video' ? 'blue' : 'green'" size="sm">
                                    {{ ucfirst($creative->type) }}
                                </flux:badge>
                                @if ($creative->creative_id)
                                    <flux:badge color="purple" size="sm">
                                        ID: {{ $creative->creative_id }}
                                    </flux:badge>
                                @endif
                                @if ($creative->page_id)
                                    <span class="text-xs text-gray-600 dark:text-gray-400">Page: {{ $creative->page_id }}</span>
                                @endif
                                @if ($creative->instagram_user_id)
                                    <span class="text-xs text-gray-600 dark:text-gray-400">IG: {{ $creative->instagram_user_id }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
