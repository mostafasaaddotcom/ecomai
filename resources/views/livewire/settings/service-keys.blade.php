<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('API Service Keys')" :subheading="__('Configure API keys for external services')">
        <form wire:submit="save" class="my-6 w-full space-y-6">
            <div>
                <flux:input
                    wire:model="openrouter_key"
                    :label="__('OpenRouter API Key')"
                    type="password"
                    autocomplete="off"
                    :placeholder="__('Enter your OpenRouter API key')" />
                <flux:text class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    {{ __('API key for OpenRouter service integration') }}
                </flux:text>
            </div>

            <div>
                <flux:input
                    wire:model="kie_key"
                    :label="__('KIE API Key')"
                    type="password"
                    autocomplete="off"
                    :placeholder="__('Enter your KIE API key')" />
                <flux:text class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    {{ __('API key for KIE service integration') }}
                </flux:text>
            </div>

            <div>
                <flux:input
                    wire:model="lahajati_key"
                    :label="__('Lahajati API Key')"
                    type="password"
                    autocomplete="off"
                    :placeholder="__('Enter your Lahajati API key')" />
                <flux:text class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    {{ __('API key for Lahajati service integration') }}
                </flux:text>
            </div>

            <div>
                <flux:input
                    wire:model="supabase_project_url"
                    :label="__('Supabase Project URL')"
                    type="text"
                    autocomplete="off"
                    :placeholder="__('https://your-project.supabase.co')" />
                <flux:text class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    {{ __('Your Supabase project URL') }}
                </flux:text>
            </div>

            <div>
                <flux:input
                    wire:model="supabase_service_role_key"
                    :label="__('Supabase Service Role Key')"
                    type="password"
                    autocomplete="off"
                    :placeholder="__('Enter your Supabase service role key')" />
                <flux:text class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    {{ __('Service role key for Supabase (keep this secure!)') }}
                </flux:text>
            </div>

            <div class="flex items-center gap-4">
                <div class="flex items-center justify-end">
                    <flux:button variant="primary" type="submit" class="w-full">{{ __('Save Keys') }}</flux:button>
                </div>

                <x-action-message class="me-3" on="service-keys-updated">
                    {{ __('Saved.') }}
                </x-action-message>
            </div>
        </form>
    </x-settings.layout>
</section>
