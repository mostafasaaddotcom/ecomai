<?php

namespace App\Livewire\Products;

use App\Models\Product;
use App\Models\ProductCopy;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Copywriting extends Component
{
    public Product $product;

    public bool $isGenerating = false;

    // Voice generation tracking
    public ?int $generatingVoiceForCopyId = null;

    // Voice selection UI
    public ?int $expandedVoiceSelectorCopyId = null;
    public $userVoices = [];
    public $userPerformances = [];
    public $userDialects = [];

    // Selected voice options per copy (array keyed by copy ID)
    public array $selectedVoiceIds = [];
    public array $selectedPerformanceIds = [];
    public array $selectedDialectIds = [];

    // Voice mode tracking (generate or upload) per copy
    public array $voiceMode = []; // 'generate' or 'upload'

    // Form fields
    public string $language = '';

    public int $ugcCount = 0;

    public int $expertCount = 0;

    public int $backgroundVoiceCount = 0;

    public array $selectedFormulas = [];

    // Expanded copy tracking
    public ?int $expandedCopyId = null;

    // Copy content for editing
    public array $copyContent = [];

    // Available options
    public array $languages = [
        'egyptian' => 'اللهجة المصرية',
        'saudi' => 'اللهجة السعودية',
        'gulf' => 'اللهجة الخليجية',
        'levantine' => 'اللهجة الشامية',
        'moroccan' => 'اللهجة المغربية',
        'msa' => 'العربية الفصحى',
    ];

    public array $formulas = [
        'AIDA' => 'AIDA (Attention, Interest, Desire, Action)',
        'BAB' => 'BAB (Before, After, Bridge)',
        'PAS' => 'PAS (Problem, Agitate, Solve)',
        'FAB' => 'FAB (Features, Advantages, Benefits)',
        '4Ps' => '4Ps (Picture, Promise, Prove, Push)',
        'QUEST' => 'QUEST (Qualify, Understand, Educate, Stimulate, Transition)',
    ];

    /**
     * Mount the component.
     */
    public function mount(Product $product): void
    {
        // Ensure the product belongs to the authenticated user
        if ($product->user_id !== Auth::id()) {
            abort(403);
        }

        $this->product = $product->load(['copies', 'analysis']);

        // Load user's Lahajati preferences
        $this->userVoices = Auth::user()->lahajatiVoices()->with('lahajatiVoice')->get();
        $this->userPerformances = Auth::user()->lahajatiPerformances()->with('lahajatiPerformance')->get();
        $this->userDialects = Auth::user()->lahajatiDialects()->with('lahajatiDialect')->get();

        // Initialize copy content and default voice selections
        foreach ($this->product->copies as $copy) {
            $this->copyContent[$copy->id] = $copy->content;

            // Set default selections (user's default preferences)
            $defaultVoice = $this->userVoices->firstWhere('is_default', true);
            $defaultPerformance = $this->userPerformances->firstWhere('is_default', true);
            $defaultDialect = $this->userDialects->firstWhere('is_default', true);

            $this->selectedVoiceIds[$copy->id] = $defaultVoice?->id;
            $this->selectedPerformanceIds[$copy->id] = $defaultPerformance?->id;
            $this->selectedDialectIds[$copy->id] = $defaultDialect?->id;

            // Initialize voice mode to 'generate' by default
            $this->voiceMode[$copy->id] = 'generate';
        }
    }

    /**
     * Increment copy type count.
     */
    public function incrementCount(string $type): void
    {
        match ($type) {
            'ugc' => $this->ugcCount = min($this->ugcCount + 1, 10),
            'expert' => $this->expertCount = min($this->expertCount + 1, 10),
            'background_voice' => $this->backgroundVoiceCount = min($this->backgroundVoiceCount + 1, 10),
            default => null,
        };
    }

    /**
     * Decrement copy type count.
     */
    public function decrementCount(string $type): void
    {
        match ($type) {
            'ugc' => $this->ugcCount = max($this->ugcCount - 1, 0),
            'expert' => $this->expertCount = max($this->expertCount - 1, 0),
            'background_voice' => $this->backgroundVoiceCount = max($this->backgroundVoiceCount - 1, 0),
            default => null,
        };
    }

    /**
     * Generate copies.
     */
    public function generateCopies(): void
    {
        // Validate
        $this->validate([
            'language' => ['required', 'string'],
            'selectedFormulas' => ['required', 'array', 'min:1'],
        ]);

        if ($this->ugcCount + $this->expertCount + $this->backgroundVoiceCount === 0) {
            session()->flash('error', 'Please select at least one copy type with quantity greater than 0.');

            return;
        }

        if (! $this->product->hasAnalysis()) {
            session()->flash('error', 'Please generate product analysis first.');

            return;
        }

        $this->isGenerating = true;

        try {
            // Prepare types array
            $types = [];
            if ($this->ugcCount > 0) {
                $types[] = ['type' => 'ugc', 'count' => $this->ugcCount];
            }
            if ($this->expertCount > 0) {
                $types[] = ['type' => 'expert', 'count' => $this->expertCount];
            }
            if ($this->backgroundVoiceCount > 0) {
                $types[] = ['type' => 'background_voice', 'count' => $this->backgroundVoiceCount];
            }

            // Prepare webhook payload
            $webhookPayload = [
                'product_id' => $this->product->id,
                'product_analysis_id' => $this->product->analysis->id,
                'user_id' => Auth::id(),
                'language' => $this->language,
                'types' => $types,
                'formulas' => $this->selectedFormulas,
                'product_data' => [
                    'name' => $this->product->name,
                    'description_user' => $this->product->description_user,
                    'description_ai' => $this->product->description_ai,
                    'type' => $this->product->type,
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
            Log::info('Webhook payload for product copy generation', [
                'product_id' => $this->product->id,
                'payload' => $webhookPayload,
            ]);

            // Send webhook to n8n
            $response = Http::timeout(30)->post(config('app.n8n_base_url') . 'generate-product-copies', $webhookPayload);

            if ($response->successful()) {
                session()->flash('message', 'Copy generation started successfully. Please refresh the page in a moment to see the results.');
                // Refresh copies
                $this->product->load('copies');
            } else {
                Log::error('Webhook failed for product copy generation', [
                    'product_id' => $this->product->id,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                session()->flash('error', 'Failed to generate copies. Please try again.');
            }
        } catch (\Exception $e) {
            Log::error('Error generating product copies', [
                'product_id' => $this->product->id,
                'error' => $e->getMessage(),
            ]);
            session()->flash('error', 'An error occurred while generating copies. Please try again.');
        } finally {
            $this->isGenerating = false;
        }
    }

    /**
     * Toggle copy expansion.
     */
    public function toggleExpansion(int $copyId): void
    {
        $this->expandedCopyId = $this->expandedCopyId === $copyId ? null : $copyId;
    }

    /**
     * Toggle voice selector expansion.
     */
    public function toggleVoiceSelector(int $copyId): void
    {
        $this->expandedVoiceSelectorCopyId = $this->expandedVoiceSelectorCopyId === $copyId ? null : $copyId;
    }

    /**
     * Update copy content.
     */
    public function updateCopy(int $copyId): void
    {
        $copy = ProductCopy::findOrFail($copyId);

        // Ensure the copy belongs to the authenticated user
        if ($copy->user_id !== Auth::id()) {
            abort(403);
        }

        $copy->update([
            'content' => $this->copyContent[$copyId] ?? '',
        ]);

        session()->flash('message', 'Copy updated successfully.');

        // Reload copies to reflect changes
        $this->product->load('copies');
    }

    /**
     * Delete a copy.
     */
    public function deleteCopy(int $copyId): void
    {
        $copy = ProductCopy::findOrFail($copyId);

        // Ensure the copy belongs to the authenticated user
        if ($copy->user_id !== Auth::id()) {
            abort(403);
        }

        $copy->delete();

        // Remove from copyContent array
        unset($this->copyContent[$copyId]);

        // Reload copies
        $this->product->load('copies');

        session()->flash('message', 'Copy deleted successfully.');
    }

    /**
     * Generate voice for a copy using Lahajati.
     */
    public function generateVoice(int $copyId): void
    {
        $copy = ProductCopy::findOrFail($copyId);

        // Ensure the copy belongs to the authenticated user
        if ($copy->user_id !== Auth::id()) {
            abort(403);
        }

        // Check if user has Lahajati API key
        $apiKey = Auth::user()->apiServiceKeys?->lahajati_key;
        if (!$apiKey) {
            session()->flash('error', 'Please add your Lahajati API key in Service Keys settings first.');
            return;
        }

        // Get selected voice, performance, and dialect for this copy
        $selectedVoiceId = $this->selectedVoiceIds[$copyId] ?? null;
        $selectedPerformanceId = $this->selectedPerformanceIds[$copyId] ?? null;
        $selectedDialectId = $this->selectedDialectIds[$copyId] ?? null;

        if (!$selectedVoiceId || !$selectedPerformanceId || !$selectedDialectId) {
            session()->flash('error', 'Please select voice, performance, and dialect options.');
            return;
        }

        // Load the actual models
        $userVoice = Auth::user()->lahajatiVoices()->with('lahajatiVoice')->find($selectedVoiceId);
        $userPerformance = Auth::user()->lahajatiPerformances()->with('lahajatiPerformance')->find($selectedPerformanceId);
        $userDialect = Auth::user()->lahajatiDialects()->with('lahajatiDialect')->find($selectedDialectId);

        if (!$userVoice || !$userPerformance || !$userDialect) {
            session()->flash('error', 'Selected voice options are invalid. Please select again.');
            return;
        }

        $this->generatingVoiceForCopyId = $copyId;

        try {
            // Prepare webhook payload
            $webhookPayload = [
                'copy_id' => $copyId,
                'text' => $copy->content,
                'user_id' => Auth::id(),
                'lahajati_key' => $apiKey,
                'id_voice' => $userVoice->lahajatiVoice->lahajati_id,
                'performance_id' => $userPerformance->lahajatiPerformance->lahajati_id,
                'dialect_id' => $userDialect->lahajatiDialect->lahajati_id,
                'input_mode' => '0',
                'app_url' => config('app.url'),
            ];

            // Log the webhook payload for debugging
            Log::info('Webhook payload for voice generation', [
                'copy_id' => $copyId,
                'payload' => $webhookPayload,
            ]);

            // Send webhook to n8n
            $response = Http::timeout(30)->post(config('app.n8n_base_url') . 'generate-voice-copy', $webhookPayload);

            if ($response->successful()) {
                session()->flash('message', 'Voice generation started successfully. The page will refresh automatically when ready.');
                // Refresh copies after a short delay
                $this->product->load('copies');
            } else {
                Log::error('Webhook failed for voice generation', [
                    'copy_id' => $copyId,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                session()->flash('error', 'Failed to generate voice. Please try again.');
            }
        } catch (\Exception $e) {
            Log::error('Error generating voice', [
                'copy_id' => $copyId,
                'error' => $e->getMessage(),
            ]);
            session()->flash('error', 'An error occurred while generating voice. Please try again.');
        } finally {
            $this->generatingVoiceForCopyId = null;
        }
    }

    /**
     * Refresh a specific copy from the database.
     */
    public function refreshCopy(int $copyId): void
    {
        $copy = ProductCopy::findOrFail($copyId);

        // Ensure the copy belongs to the authenticated user
        if ($copy->user_id !== Auth::id()) {
            abort(403);
        }

        // Reload the product copies
        $this->product->load('copies');

        // Update the copy content array
        $this->copyContent[$copyId] = $copy->content;

        session()->flash('message', 'Copy refreshed successfully.');
    }

    /**
     * Handle successful voice upload from AJAX.
     * This is called from the frontend after the upload completes.
     */
    #[On('voiceUploaded')]
    public function voiceUploadSuccess(int $copyId): void
    {
        // Reload the product copies to get the updated voice_url_link
        $this->product->load('copies');

        session()->flash('message', 'Voice uploaded successfully.');
    }

    /**
     * Get total copies to be generated.
     */
    public function getTotalCopiesProperty(): int
    {
        $totalTypes = $this->ugcCount + $this->expertCount + $this->backgroundVoiceCount;

        return $totalTypes * count($this->selectedFormulas);
    }

    public function render()
    {
        return view('livewire.products.copywriting');
    }
}
