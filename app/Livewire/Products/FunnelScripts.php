<?php

namespace App\Livewire\Products;

use App\Models\Product;
use App\Models\ProductPersonaFunnelScript;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class FunnelScripts extends Component
{
    public Product $product;
    public bool $isGenerating = false;
    public ?int $selectedPersonaId = null;
    public string $language = '';
    public array $selectedFormulas = [];

    // Stage counters (0-10 for each awareness stage)
    public int $unawareCount = 0;
    public int $problemAwareCount = 0;
    public int $solutionAwareCount = 0;
    public int $productAwareCount = 0;
    public int $mostAwareCount = 0;

    // For displaying and editing scripts
    public ?int $expandedScriptId = null;
    public array $scriptContents = [];

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
        'PAS' => 'PAS (Problem, Agitate, Solution)',
        'FAB' => 'FAB (Features, Advantages, Benefits)',
        'BAB' => 'Before-After-Bridge',
        '4Ps' => '4Ps (Picture, Promise, Proof, Push)',
        'QUEST' => 'QUEST (Qualify, Understand, Educate, Stimulate, Transition)',
    ];

    public function mount(Product $product)
    {
        // Check authorization
        if ($product->user_id !== Auth::id()) {
            abort(403);
        }

        // Load relationships
        $this->product = $product->load(['funnelScripts.productPersona', 'personas', 'analysis']);

        // Initialize script contents for existing scripts
        foreach ($this->product->funnelScripts as $script) {
            $this->scriptContents[$script->id] = $script->content ?? '';
        }
    }

    public function generateScripts()
    {
        // Validate inputs
        $this->validate([
            'selectedPersonaId' => ['required', 'exists:product_personas,id'],
            'language' => ['required', 'string'],
            'selectedFormulas' => ['required', 'array', 'min:1'],
        ]);

        // Check if at least one stage has a count > 0
        $totalCount = $this->unawareCount + $this->problemAwareCount +
                      $this->solutionAwareCount + $this->productAwareCount +
                      $this->mostAwareCount;

        if ($totalCount === 0) {
            session()->flash('error', __('products.at_least_one_stage_required'));
            return;
        }

        // Check if product has analysis
        if (!$this->product->hasAnalysis()) {
            session()->flash('error', __('products.analysis_required_for_funnel_scripts'));
            return;
        }

        // Check if product has personas
        if ($this->product->personas->isEmpty()) {
            session()->flash('error', __('products.persona_required_for_funnel_scripts'));
            return;
        }

        $this->isGenerating = true;

        try {
            // Get selected persona
            $persona = $this->product->personas->find($this->selectedPersonaId);

            // Prepare stages array
            $stages = [];
            if ($this->unawareCount > 0) {
                $stages[] = ['stage' => 'unaware', 'count' => $this->unawareCount];
            }
            if ($this->problemAwareCount > 0) {
                $stages[] = ['stage' => 'problem_aware', 'count' => $this->problemAwareCount];
            }
            if ($this->solutionAwareCount > 0) {
                $stages[] = ['stage' => 'solution_aware', 'count' => $this->solutionAwareCount];
            }
            if ($this->productAwareCount > 0) {
                $stages[] = ['stage' => 'product_aware', 'count' => $this->productAwareCount];
            }
            if ($this->mostAwareCount > 0) {
                $stages[] = ['stage' => 'most_aware', 'count' => $this->mostAwareCount];
            }

            // Prepare webhook payload
            $webhookPayload = [
                'product_id' => $this->product->id,
                'product_analysis_id' => $this->product->analysis->id,
                'product_persona_id' => $persona->id,
                'user_id' => Auth::id(),
                'language' => $this->language,
                'stages' => $stages,
                'formulas' => $this->selectedFormulas,
                'product_data' => [
                    'name' => $this->product->name,
                    'description_user' => $this->product->description_user,
                    'description_ai' => $this->product->description_ai,
                    'type' => $this->product->type,
                ],
                'persona_data' => [
                    'title' => $persona->title,
                    'description' => $persona->description,
                    'main_problem' => $persona->main_problem,
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
                'app_url' => config('app.url'),
            ];

            // Log the payload for debugging
            Log::info('Sending funnel scripts generation request to N8N', $webhookPayload);

            // Send to N8N webhook
            $response = Http::timeout(30)->post(
                config('app.n8n_base_url') . 'generate-persona-funnel-scripts',
                $webhookPayload
            );

            if ($response->successful()) {
                session()->flash('message', __('products.funnel_scripts_generation_started'));
            } else {
                session()->flash('error', __('products.funnel_scripts_generation_failed'));
                Log::error('N8N funnel scripts generation failed', [
                    'response' => $response->body(),
                    'status' => $response->status(),
                ]);
            }
        } catch (\Exception $e) {
            session()->flash('error', __('products.funnel_scripts_generation_failed'));
            Log::error('Funnel scripts generation error: ' . $e->getMessage());
        } finally {
            $this->isGenerating = false;
        }
    }

    public function incrementStageCount(string $stage)
    {
        $property = $stage . 'Count';
        if ($this->{$property} < 10) {
            $this->{$property}++;
        }
    }

    public function decrementStageCount(string $stage)
    {
        $property = $stage . 'Count';
        if ($this->{$property} > 0) {
            $this->{$property}--;
        }
    }

    public function toggleExpansion(int $scriptId)
    {
        $this->expandedScriptId = $this->expandedScriptId === $scriptId ? null : $scriptId;
    }

    public function updateScript(int $scriptId)
    {
        $script = ProductPersonaFunnelScript::find($scriptId);

        if (!$script || $script->user_id !== Auth::id()) {
            abort(403);
        }

        $this->validate([
            "scriptContents.{$scriptId}" => ['required', 'string'],
        ]);

        $script->update([
            'content' => $this->scriptContents[$scriptId],
        ]);

        session()->flash('message', __('products.funnel_script_updated'));
        $this->product->load(['funnelScripts.productPersona']);
    }

    public function deleteScript(int $scriptId)
    {
        $script = ProductPersonaFunnelScript::find($scriptId);

        if (!$script || $script->user_id !== Auth::id()) {
            abort(403);
        }

        $script->delete();

        unset($this->scriptContents[$scriptId]);

        session()->flash('message', __('products.funnel_script_deleted'));
        $this->product->load(['funnelScripts.productPersona']);
    }

    public function render()
    {
        return view('livewire.products.funnel-scripts');
    }
}
