<?php

namespace App\Livewire\Products;

use App\Models\Product;
use App\Models\ProductPersona;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Personas extends Component
{
    public Product $product;

    public bool $isGenerating = false;

    public int $personasCount = 3;

    // Expanded persona tracking
    public ?int $expandedPersonaId = null;

    // Persona content for editing
    public array $personaTitles = [];
    public array $personaDescriptions = [];
    public array $personaMainProblems = [];

    /**
     * Mount the component.
     */
    public function mount(Product $product): void
    {
        // Ensure the product belongs to the authenticated user
        if ($product->user_id !== Auth::id()) {
            abort(403);
        }

        $this->product = $product->load(['personas', 'analysis']);

        // Initialize persona content for editing
        foreach ($this->product->personas as $persona) {
            $this->personaTitles[$persona->id] = $persona->title;
            $this->personaDescriptions[$persona->id] = $persona->description ?? '';
            $this->personaMainProblems[$persona->id] = $persona->main_problem ?? '';
        }
    }

    /**
     * Increment personas count.
     */
    public function incrementCount(): void
    {
        $this->personasCount = min($this->personasCount + 1, 5);
    }

    /**
     * Decrement personas count.
     */
    public function decrementCount(): void
    {
        $this->personasCount = max($this->personasCount - 1, 1);
    }

    /**
     * Generate personas via N8N webhook.
     */
    public function generatePersonas(): void
    {
        if (! $this->product->hasAnalysis()) {
            session()->flash('error', __('products.analysis_required_for_copies'));
            return;
        }

        $this->isGenerating = true;

        try {
            // Prepare webhook payload
            $webhookPayload = [
                'product_id' => $this->product->id,
                'product_analysis_id' => $this->product->analysis->id,
                'user_id' => Auth::id(),
                'count' => $this->personasCount,
                'product_data' => [
                    'name' => $this->product->name,
                    'description_user' => $this->product->description_user,
                    'description_ai' => $this->product->description_ai,
                    'type' => $this->product->type,
                    'main_image_url' => $this->product->main_image_url,
                ],
                'analysis_data' => [
                    'core_function_and_use' => $this->product->analysis->core_function_and_use,
                    'features' => $this->product->analysis->features,
                    'benefits' => $this->product->analysis->benefits,
                    'problems' => $this->product->analysis->problems,
                    'goals' => $this->product->analysis->goals,
                    'emotions' => $this->product->analysis->emotions,
                    'objections' => $this->product->analysis->objections,
                    'faqs' => $this->product->analysis->faqs,
                ],
                'app_url' => config('app.url')
            ];

            // Log the webhook payload for debugging
            Log::info('Webhook payload for product personas generation', [
                'product_id' => $this->product->id,
                'payload' => $webhookPayload,
            ]);

            // Send webhook to n8n
            $response = Http::timeout(30)->post(config('app.n8n_base_url') . 'generate-product-personas', $webhookPayload);

            if ($response->successful()) {
                session()->flash('message', __('products.personas_generation_started'));
                // Refresh personas
                $this->product->load('personas');
            } else {
                Log::error('Webhook failed for product personas generation', [
                    'product_id' => $this->product->id,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                session()->flash('error', __('products.personas_generation_failed'));
            }
        } catch (\Exception $e) {
            Log::error('Error generating product personas', [
                'product_id' => $this->product->id,
                'error' => $e->getMessage(),
            ]);
            session()->flash('error', __('products.personas_generation_failed'));
        } finally {
            $this->isGenerating = false;
        }
    }

    /**
     * Toggle persona expansion.
     */
    public function toggleExpansion(int $personaId): void
    {
        $this->expandedPersonaId = $this->expandedPersonaId === $personaId ? null : $personaId;
    }

    /**
     * Update persona.
     */
    public function updatePersona(int $personaId): void
    {
        $persona = ProductPersona::findOrFail($personaId);

        // Ensure the persona belongs to the authenticated user
        if ($persona->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $this->validate([
            "personaTitles.{$personaId}" => ['required', 'string', 'max:255'],
            "personaDescriptions.{$personaId}" => ['nullable', 'string'],
            "personaMainProblems.{$personaId}" => ['nullable', 'string'],
        ]);

        $persona->update([
            'title' => $this->personaTitles[$personaId],
            'description' => $this->personaDescriptions[$personaId] ?? '',
            'main_problem' => $this->personaMainProblems[$personaId] ?? '',
        ]);

        session()->flash('message', __('products.persona_updated'));

        // Reload personas to reflect changes
        $this->product->load('personas');
    }

    /**
     * Delete a persona.
     */
    public function deletePersona(int $personaId): void
    {
        $persona = ProductPersona::findOrFail($personaId);

        // Ensure the persona belongs to the authenticated user
        if ($persona->user_id !== Auth::id()) {
            abort(403);
        }

        $persona->delete();

        // Remove from arrays
        unset($this->personaTitles[$personaId]);
        unset($this->personaDescriptions[$personaId]);
        unset($this->personaMainProblems[$personaId]);

        // Reload personas
        $this->product->load('personas');

        session()->flash('message', __('products.persona_deleted'));
    }

    public function render()
    {
        return view('livewire.products.personas');
    }
}
