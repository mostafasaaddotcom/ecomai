<?php

namespace App\Livewire\Settings;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class ApiTokens extends Component
{
    public string $tokenName = '';

    public ?string $newToken = null;

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        // Check if there's a new token from registration
        if (session()->has('new_token')) {
            $this->newToken = session('new_token');
            session()->forget('new_token');
        }
    }

    /**
     * Create a new API token.
     */
    public function createToken(): void
    {
        $this->validate([
            'tokenName' => ['required', 'string', 'max:255'],
        ]);

        // Create token with user-level abilities (not admin)
        // Using specific abilities instead of '*' to prevent admin access
        $token = Auth::user()->createToken($this->tokenName, ['user:access']);

        $this->newToken = $token->plainTextToken;
        $this->tokenName = '';

        $this->dispatch('token-created');
    }

    /**
     * Delete an API token.
     */
    public function deleteToken(int $tokenId): void
    {
        $token = Auth::user()->tokens()->find($tokenId);

        if ($token) {
            $token->delete();
            session()->flash('message', 'Token deleted successfully.');
        }
    }

    /**
     * Close the new token display.
     */
    public function closeNewToken(): void
    {
        $this->newToken = null;
    }

    /**
     * Get token preview (first 8 + ... + last 4 characters).
     */
    private function getTokenPreview(string $tokenName): string
    {
        // For display purposes, we can only show a preview
        // since the actual token is hashed in the database
        return $tokenName;
    }

    public function render()
    {
        return view('livewire.settings.api-tokens', [
            'tokens' => Auth::user()->tokens()->orderBy('created_at', 'desc')->get(),
        ]);
    }
}
