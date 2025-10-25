<?php

namespace App\Livewire;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class LanguageSwitcher extends Component
{
    public $currentLocale;

    public function mount()
    {
        $this->currentLocale = App::getLocale();
    }

    public function switchLanguage($locale)
    {
        // Validate locale
        if (!in_array($locale, ['ar', 'en'])) {
            return;
        }

        // Update user preference if authenticated
        if (auth()->check()) {
            auth()->user()->update(['locale' => $locale]);
        }

        // Update session
        Session::put('locale', $locale);

        // Refresh the page to apply RTL/LTR changes
        $this->redirect(request()->header('Referer') ?: route('dashboard'), navigate: false);
    }

    public function render()
    {
        return view('livewire.language-switcher');
    }
}
