<div class="relative">
    <flux:dropdown position="bottom" align="end">
        <flux:button variant="ghost" size="sm" icon-trailing="chevron-down">
            @if($currentLocale === 'ar')
                العربية
            @else
                English
            @endif
        </flux:button>

        <flux:menu class="w-48">
            <flux:menu.item wire:click="switchLanguage('ar')" :class="$currentLocale === 'ar' ? 'bg-zinc-100 dark:bg-zinc-800' : ''">
                <div class="flex items-center">
                    <span>العربية</span>
                    @if($currentLocale === 'ar')
                        <svg class="w-5 h-5 ms-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                    @endif
                </div>
            </flux:menu.item>

            <flux:menu.item wire:click="switchLanguage('en')" :class="$currentLocale === 'en' ? 'bg-zinc-100 dark:bg-zinc-800' : ''">
                <div class="flex items-center">
                    <span>English</span>
                    @if($currentLocale === 'en')
                        <svg class="w-5 h-5 ms-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                    @endif
                </div>
            </flux:menu.item>
        </flux:menu>
    </flux:dropdown>
</div>
