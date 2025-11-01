<?php

namespace App\Livewire\Settings;

use App\Models\ApiServiceKey;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ServiceKeys extends Component
{
    public string $openrouter_key = '';

    public string $kie_key = '';

    public string $lahajati_key = '';

    public string $supabase_project_url = '';

    public string $supabase_service_role_key = '';

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $serviceKeys = Auth::user()->apiServiceKeys;

        if ($serviceKeys) {
            $this->openrouter_key = $serviceKeys->openrouter_key ?? '';
            $this->kie_key = $serviceKeys->kie_key ?? '';
            $this->lahajati_key = $serviceKeys->lahajati_key ?? '';
            $this->supabase_project_url = $serviceKeys->supabase_project_url ?? '';
            $this->supabase_service_role_key = $serviceKeys->supabase_service_role_key ?? '';
        }
    }

    /**
     * Update the API service keys for the currently authenticated user.
     */
    public function save(): void
    {
        $validated = $this->validate([
            'openrouter_key' => ['nullable', 'string', 'max:255'],
            'kie_key' => ['nullable', 'string', 'max:255'],
            'lahajati_key' => ['nullable', 'string', 'max:1000'],
            'supabase_project_url' => ['nullable', 'string', 'max:255'],
            'supabase_service_role_key' => ['nullable', 'string', 'max:255'],
        ]);

        Auth::user()->apiServiceKeys()->updateOrCreate(
            ['user_id' => Auth::id()],
            $validated
        );

        $this->dispatch('service-keys-updated');
    }
}
