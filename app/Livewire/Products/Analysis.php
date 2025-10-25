<?php

namespace App\Livewire\Products;

use App\Models\Product;
use App\Models\ProductAnalysis;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Analysis extends Component
{
    public Product $product;

    public ?ProductAnalysis $analysis = null;

    public bool $isGenerating = false;

    public string $core_function_and_use = '';

    public array $features = [];

    public array $benefits = [];

    public array $problems = [];

    public array $goals = [];

    public array $emotions = [];

    public array $objections = [];

    public array $faqs = [];

    // Editing state tracking
    public ?int $editingFeatureIndex = null;
    public ?int $editingBenefitIndex = null;
    public ?int $editingProblemIndex = null;
    public ?int $editingGoalIndex = null;
    public ?int $editingEmotionIndex = null;
    public ?int $editingObjectionIndex = null;
    public ?int $editingFaqIndex = null;

    /**
     * Mount the component.
     */
    public function mount(Product $product): void
    {
        // Ensure the product belongs to the authenticated user
        if ($product->user_id !== Auth::id()) {
            abort(403);
        }

        $this->product = $product;

        // Load existing analysis if it exists
        $this->analysis = $product->analysis;

        if ($this->analysis) {
            $this->loadAnalysisData();
        }
    }

    /**
     * Load analysis data into component properties.
     */
    protected function loadAnalysisData(): void
    {
        $this->core_function_and_use = $this->analysis->core_function_and_use ?? '';
        $this->features = $this->analysis->features ?? [];
        $this->benefits = $this->analysis->benefits ?? [];
        $this->problems = $this->analysis->problems ?? [];
        $this->goals = $this->analysis->goals ?? [];
        $this->emotions = $this->analysis->emotions ?? [];
        $this->objections = $this->analysis->objections ?? [];
        $this->faqs = $this->analysis->faqs ?? [];
    }

    /**
     * Generate or regenerate product analysis.
     */
    public function generateAnalysis(): void
    {
        $this->isGenerating = true;

        try {
            // Create or update the analysis record
            if ($this->analysis) {
                // Regenerate - clear existing data
                $this->analysis->update([
                    'core_function_and_use' => null,
                    'features' => null,
                    'benefits' => null,
                    'problems' => null,
                    'goals' => null,
                    'emotions' => null,
                    'objections' => null,
                    'faqs' => null,
                ]);
            } else {
                // Create new analysis record
                $this->analysis = ProductAnalysis::create([
                    'user_id' => Auth::id(),
                    'product_id' => $this->product->id,
                ]);
            }

            // Send webhook to n8n
            $response = Http::timeout(30)->post(config('app.n8n_base_url') . 'generate-product-analysis', [
                'product_analysis_id' => $this->analysis->id,
                'product_id' => $this->product->id,
                'name' => $this->product->name,
                'description_user' => $this->product->description_user,
                'description_ai' => $this->product->description_ai,
                'type' => $this->product->type,
                'main_image_url' => $this->product->main_image_url,
                'app_url' => config('app.url')
            ]);

            if ($response->successful()) {
                session()->flash('message', 'Analysis generation started successfully. Please refresh the page in a moment to see the results.');
            } else {
                Log::error('Webhook failed for product analysis generation', [
                    'product_id' => $this->product->id,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                session()->flash('error', 'Failed to generate analysis. Please try again.');
            }
        } catch (\Exception $e) {
            Log::error('Error generating product analysis', [
                'product_id' => $this->product->id,
                'error' => $e->getMessage(),
            ]);
            session()->flash('error', 'An error occurred while generating analysis. Please try again.');
        } finally {
            $this->isGenerating = false;
        }
    }

    // Features management
    public function addFeature(): void
    {
        $this->features[] = '';
        $this->editingFeatureIndex = count($this->features) - 1;
    }

    public function updateFeature(int $index, string $value): void
    {
        if (isset($this->features[$index])) {
            $this->features[$index] = $value;
        }
    }

    public function removeFeature(int $index): void
    {
        if (isset($this->features[$index])) {
            array_splice($this->features, $index, 1);
            $this->editingFeatureIndex = null;
        }
    }

    public function startEditingFeature(int $index): void
    {
        $this->editingFeatureIndex = $index;
    }

    public function stopEditingFeature(): void
    {
        $this->editingFeatureIndex = null;
    }

    // Benefits management
    public function addBenefit(): void
    {
        $this->benefits[] = '';
        $this->editingBenefitIndex = count($this->benefits) - 1;
    }

    public function updateBenefit(int $index, string $value): void
    {
        if (isset($this->benefits[$index])) {
            $this->benefits[$index] = $value;
        }
    }

    public function removeBenefit(int $index): void
    {
        if (isset($this->benefits[$index])) {
            array_splice($this->benefits, $index, 1);
            $this->editingBenefitIndex = null;
        }
    }

    public function startEditingBenefit(int $index): void
    {
        $this->editingBenefitIndex = $index;
    }

    public function stopEditingBenefit(): void
    {
        $this->editingBenefitIndex = null;
    }

    // Problems management
    public function addProblem(): void
    {
        $this->problems[] = '';
        $this->editingProblemIndex = count($this->problems) - 1;
    }

    public function updateProblem(int $index, string $value): void
    {
        if (isset($this->problems[$index])) {
            $this->problems[$index] = $value;
        }
    }

    public function removeProblem(int $index): void
    {
        if (isset($this->problems[$index])) {
            array_splice($this->problems, $index, 1);
            $this->editingProblemIndex = null;
        }
    }

    public function startEditingProblem(int $index): void
    {
        $this->editingProblemIndex = $index;
    }

    public function stopEditingProblem(): void
    {
        $this->editingProblemIndex = null;
    }

    // Goals management
    public function addGoal(): void
    {
        $this->goals[] = '';
        $this->editingGoalIndex = count($this->goals) - 1;
    }

    public function updateGoal(int $index, string $value): void
    {
        if (isset($this->goals[$index])) {
            $this->goals[$index] = $value;
        }
    }

    public function removeGoal(int $index): void
    {
        if (isset($this->goals[$index])) {
            array_splice($this->goals, $index, 1);
            $this->editingGoalIndex = null;
        }
    }

    public function startEditingGoal(int $index): void
    {
        $this->editingGoalIndex = $index;
    }

    public function stopEditingGoal(): void
    {
        $this->editingGoalIndex = null;
    }

    // Emotions management
    public function addEmotion(): void
    {
        $this->emotions[] = '';
        $this->editingEmotionIndex = count($this->emotions) - 1;
    }

    public function updateEmotion(int $index, string $value): void
    {
        if (isset($this->emotions[$index])) {
            $this->emotions[$index] = $value;
        }
    }

    public function removeEmotion(int $index): void
    {
        if (isset($this->emotions[$index])) {
            array_splice($this->emotions, $index, 1);
            $this->editingEmotionIndex = null;
        }
    }

    public function startEditingEmotion(int $index): void
    {
        $this->editingEmotionIndex = $index;
    }

    public function stopEditingEmotion(): void
    {
        $this->editingEmotionIndex = null;
    }

    // Objections management
    public function addObjection(): void
    {
        $this->objections[] = '';
        $this->editingObjectionIndex = count($this->objections) - 1;
    }

    public function updateObjection(int $index, string $value): void
    {
        if (isset($this->objections[$index])) {
            $this->objections[$index] = $value;
        }
    }

    public function removeObjection(int $index): void
    {
        if (isset($this->objections[$index])) {
            array_splice($this->objections, $index, 1);
            $this->editingObjectionIndex = null;
        }
    }

    public function startEditingObjection(int $index): void
    {
        $this->editingObjectionIndex = $index;
    }

    public function stopEditingObjection(): void
    {
        $this->editingObjectionIndex = null;
    }

    // FAQs management
    public function addFaq(): void
    {
        $this->faqs[] = ['question' => '', 'answer' => ''];
        $this->editingFaqIndex = count($this->faqs) - 1;
    }

    public function updateFaqQuestion(int $index, string $value): void
    {
        if (isset($this->faqs[$index])) {
            $this->faqs[$index]['question'] = $value;
        }
    }

    public function updateFaqAnswer(int $index, string $value): void
    {
        if (isset($this->faqs[$index])) {
            $this->faqs[$index]['answer'] = $value;
        }
    }

    public function removeFaq(int $index): void
    {
        if (isset($this->faqs[$index])) {
            array_splice($this->faqs, $index, 1);
            $this->editingFaqIndex = null;
        }
    }

    public function startEditingFaq(int $index): void
    {
        $this->editingFaqIndex = $index;
    }

    public function stopEditingFaq(): void
    {
        $this->editingFaqIndex = null;
    }

    /**
     * Save the analysis data.
     */
    public function save(): void
    {
        if (! $this->analysis) {
            session()->flash('error', 'No analysis exists to save.');

            return;
        }

        $validated = $this->validate([
            'core_function_and_use' => ['nullable', 'string'],
            'features' => ['nullable', 'array'],
            'benefits' => ['nullable', 'array'],
            'problems' => ['nullable', 'array'],
            'goals' => ['nullable', 'array'],
            'emotions' => ['nullable', 'array'],
            'objections' => ['nullable', 'array'],
            'faqs' => ['nullable', 'array'],
        ]);

        $this->analysis->update($validated);

        session()->flash('message', 'Analysis saved successfully.');
    }

    public function render()
    {
        return view('livewire.products.analysis');
    }
}
