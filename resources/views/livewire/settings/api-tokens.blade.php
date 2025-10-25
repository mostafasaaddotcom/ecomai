<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('API Tokens')" :subheading="__('Manage your API tokens for accessing the API')">

        {{-- New Token Alert --}}
        @if ($newToken)
            <div class="mb-6 rounded-lg border border-yellow-200 bg-yellow-50 p-4 dark:border-yellow-800 dark:bg-yellow-900/20">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3 flex-1">
                        <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-400">
                            {{ __('Make sure to copy your token now. You won\'t be able to see it again!') }}
                        </h3>
                        <div class="mt-3 rounded-lg bg-white p-3 dark:bg-gray-800">
                            <code class="break-all text-sm text-gray-900 dark:text-gray-100">{{ $newToken }}</code>
                        </div>
                        <div class="mt-3 flex gap-2">
                            <button
                                type="button"
                                onclick="navigator.clipboard.writeText('{{ $newToken }}')"
                                class="rounded-lg bg-yellow-100 px-3 py-2 text-sm font-medium text-yellow-800 hover:bg-yellow-200 dark:bg-yellow-900/30 dark:text-yellow-400 dark:hover:bg-yellow-900/50"
                            >
                                {{ __('Copy to Clipboard') }}
                            </button>
                            <button
                                type="button"
                                wire:click="closeNewToken"
                                class="rounded-lg bg-gray-100 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600"
                            >
                                {{ __('I\'ve copied it') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Success Message --}}
        @if (session('message'))
            <div class="mb-6 rounded-lg border border-green-200 bg-green-50 p-4 text-sm text-green-800 dark:border-green-800 dark:bg-green-900/20 dark:text-green-400">
                {{ session('message') }}
            </div>
        @endif

        {{-- Create Token Form --}}
        <div class="mb-6 rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-zinc-900">
            <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">{{ __('Create New Token') }}</h3>
            <form wire:submit="createToken" class="flex gap-3">
                <div class="flex-1">
                    <flux:input
                        wire:model="tokenName"
                        :placeholder="__('Token name (e.g., Mobile App, Production Server)')"
                        type="text"
                        required
                    />
                </div>
                <flux:button type="submit" variant="primary">
                    {{ __('Generate Token') }}
                </flux:button>
            </form>
        </div>

        {{-- Tokens List --}}
        <div class="rounded-xl border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-zinc-900">
            <div class="border-b border-neutral-200 p-6 dark:border-neutral-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Your API Tokens') }}</h3>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ __('These tokens allow you to authenticate API requests') }}</p>
            </div>

            @if ($tokens->isEmpty())
                <div class="p-6 text-center text-gray-500 dark:text-gray-400">
                    {{ __('No API tokens created yet.') }}
                </div>
            @else
                <div class="divide-y divide-neutral-200 dark:divide-neutral-700">
                    @foreach ($tokens as $token)
                        <div class="flex items-center justify-between p-6">
                            <div class="flex-1">
                                <h4 class="font-medium text-gray-900 dark:text-white">{{ $token->name }}</h4>
                                <div class="mt-1 flex items-center gap-4 text-sm text-gray-600 dark:text-gray-400">
                                    <span>{{ __('Created') }}: {{ $token->created_at->format('M d, Y') }}</span>
                                    @if ($token->last_used_at)
                                        <span>{{ __('Last used') }}: {{ $token->last_used_at->diffForHumans() }}</span>
                                    @else
                                        <span>{{ __('Never used') }}</span>
                                    @endif
                                </div>
                            </div>
                            <flux:button
                                wire:click="deleteToken({{ $token->id }})"
                                wire:confirm="Are you sure you want to delete this token? This action cannot be undone."
                                variant="danger"
                                size="sm"
                            >
                                {{ __('Delete') }}
                            </flux:button>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- API Usage Instructions --}}
        <div class="mt-6 rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-zinc-900">
            <h3 class="mb-3 text-lg font-semibold text-gray-900 dark:text-white">{{ __('How to use your API token') }}</h3>
            <p class="mb-3 text-sm text-gray-600 dark:text-gray-400">{{ __('Include your token in the Authorization header:') }}</p>
            <pre class="overflow-x-auto rounded-lg bg-gray-100 p-4 text-sm dark:bg-gray-800"><code>curl -X GET {{ url('/api/v1/products/{id}') }} \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Accept: application/json"</code></pre>
        </div>

    </x-settings.layout>
</section>
