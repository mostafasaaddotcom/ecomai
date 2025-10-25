<?php

namespace App\Livewire\Products;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.app')]
class Images extends Component
{
    use WithFileUploads;

    public Product $product;

    public bool $isGenerating = false;

    // Form fields
    public string $ad_country = '';

    public string $promptLanguage = 'english';

    public string $aspect_ratio = '9:16';

    public int $productOnlyCount = 0;

    public int $lifestyleCount = 0;

    public int $ugcSceneCount = 0;

    public int $expertCount = 0;

    // Upload fields
    public $uploadedImages = [];

    public string $uploadType = 'other';

    // Image prompts for editing
    public array $imagePrompts = [];

    // Available options
    public array $countries = [
        'eg' => 'مصر',
        'sa' => 'السعودية',
        'dz' => 'الجزائر',
        'ma' => 'المغرب',
        'jo' => 'الاردن',
    ];

    public array $languages = [
        'english' => 'English',
        'arabic' => 'Arabic',
    ];

    public array $imageTypes = [
        'product_only' => 'Product Only',
        'lifestyle' => 'Lifestyle',
        'ugc_scene' => 'UGC Scene',
        'expert' => 'Expert',
        'other' => 'Other',
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

        $this->product = $product->load(['images', 'analysis']);

        // Initialize image prompts
        foreach ($this->product->images as $image) {
            $this->imagePrompts[$image->id] = $image->prompt;
        }
    }

    /**
     * Increment image type count.
     */
    public function incrementCount(string $type): void
    {
        match ($type) {
            'product_only' => $this->productOnlyCount = min($this->productOnlyCount + 1, 10),
            'lifestyle' => $this->lifestyleCount = min($this->lifestyleCount + 1, 10),
            'ugc_scene' => $this->ugcSceneCount = min($this->ugcSceneCount + 1, 10),
            'expert' => $this->expertCount = min($this->expertCount + 1, 10),
            default => null,
        };
    }

    /**
     * Decrement image type count.
     */
    public function decrementCount(string $type): void
    {
        match ($type) {
            'product_only' => $this->productOnlyCount = max($this->productOnlyCount - 1, 0),
            'lifestyle' => $this->lifestyleCount = max($this->lifestyleCount - 1, 0),
            'ugc_scene' => $this->ugcSceneCount = max($this->ugcSceneCount - 1, 0),
            'expert' => $this->expertCount = max($this->expertCount - 1, 0),
            default => null,
        };
    }

    /**
     * Generate images via webhook.
     */
    public function generateImages(): void
    {
        // Validate
        $this->validate([
            'ad_country' => ['required', 'string'],
            'promptLanguage' => ['required', 'string'],
        ]);

        if ($this->productOnlyCount + $this->lifestyleCount + $this->ugcSceneCount + $this->expertCount === 0) {
            session()->flash('error', 'Please select at least one image type with quantity greater than 0.');

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
            if ($this->productOnlyCount > 0) {
                $types[] = ['type' => 'product_only', 'count' => $this->productOnlyCount];
            }
            if ($this->lifestyleCount > 0) {
                $types[] = ['type' => 'lifestyle', 'count' => $this->lifestyleCount];
            }
            if ($this->ugcSceneCount > 0) {
                $types[] = ['type' => 'ugc_scene', 'count' => $this->ugcSceneCount];
            }
            if ($this->expertCount > 0) {
                $types[] = ['type' => 'expert', 'count' => $this->expertCount];
            }

            // Send webhook to n8n
            $response = Http::timeout(30)->post(config('app.n8n_base_url') . 'generate-product-images', [
                'product_id' => $this->product->id,
                'product_analysis_id' => $this->product->analysis->id,
                'user_id' => Auth::id(),
                'ad_country' => $this->countries[$this->ad_country] ?? $this->ad_country,
                'prompt_language' => $this->promptLanguage,
                'aspect_ratio' => $this->aspect_ratio,
                'types' => $types,
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
            ]);

            if ($response->successful()) {
                session()->flash('message', 'Image generation started successfully. Please refresh the page in a moment to see the results.');
                // Refresh images
                $this->product->load('images');

                // Reset form
                $this->reset(['productOnlyCount', 'lifestyleCount', 'ugcSceneCount', 'expertCount', 'ad_country', 'promptLanguage', 'aspect_ratio']);
                $this->promptLanguage = 'english';
                $this->aspect_ratio = '9:16';
            } else {
                Log::error('Webhook failed for product image generation', [
                    'product_id' => $this->product->id,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                session()->flash('error', 'Failed to generate images. Please try again.');
            }
        } catch (\Exception $e) {
            Log::error('Error generating product images', [
                'product_id' => $this->product->id,
                'error' => $e->getMessage(),
            ]);
            session()->flash('error', 'An error occurred while generating images. Please try again.');
        } finally {
            $this->isGenerating = false;
        }
    }

    /**
     * Upload images manually.
     */
    public function uploadImages(): void
    {
        $this->validate([
            'uploadedImages.*' => ['required', 'image', 'max:10240'], // Max 10MB
            'uploadType' => ['required', 'string'],
        ]);

        try {
            foreach ($this->uploadedImages as $image) {
                // Store the image
                $path = $image->store('product-images', 'public');
                $url = Storage::url($path);

                // Create product image record
                ProductImage::create([
                    'user_id' => Auth::id(),
                    'product_id' => $this->product->id,
                    'type' => $this->uploadType,
                    'image_url' => $url,
                    'is_ai_generated' => false,
                    'status' => 'completed',
                ]);
            }

            session()->flash('message', 'Images uploaded successfully.');

            // Refresh images
            $this->product->load('images');

            // Reset upload form
            $this->reset(['uploadedImages', 'uploadType']);
            $this->uploadType = 'other';
        } catch (\Exception $e) {
            Log::error('Error uploading product images', [
                'product_id' => $this->product->id,
                'error' => $e->getMessage(),
            ]);
            session()->flash('error', 'An error occurred while uploading images. Please try again.');
        }
    }

    /**
     * Update image prompt and regenerate.
     */
    public function updatePrompt(int $imageId): void
    {
        $image = ProductImage::findOrFail($imageId);

        // Ensure the image belongs to the authenticated user
        if ($image->user_id !== Auth::id()) {
            abort(403);
        }

        $image->update([
            'prompt' => $this->imagePrompts[$imageId] ?? '',
        ]);

        session()->flash('message', 'Prompt updated successfully.');

        // Reload images to reflect changes
        $this->product->load('images');
    }

    /**
     * Regenerate image with updated prompt.
     */
    public function regenerateImage(int $imageId): void
    {
        $image = ProductImage::findOrFail($imageId);

        // Ensure the image belongs to the authenticated user
        if ($image->user_id !== Auth::id()) {
            abort(403);
        }

        try {
            // Update status to image_generating
            $image->update([
                'status' => 'image_generating',
                'prompt' => $this->imagePrompts[$imageId] ?? $image->prompt,
            ]);

            // Send webhook to regenerate
            $response = Http::timeout(30)->post(config('app.n8n_base_url') . 'regenerate-product-image', [
                'image_id' => $image->id,
                'product_id' => $this->product->id,
                'user_id' => Auth::id(),
                'prompt' => $image->prompt,
                'type' => $image->type,
                'reference_id' => $image->reference_id,
                'aspect_ratio' => $image->aspect_ratio,
                'app_url' => config('app.url')
            ]);

            if ($response->successful()) {
                session()->flash('message', 'Image regeneration started successfully.');
                $this->product->load('images');
            } else {
                Log::error('Webhook failed for image regeneration', [
                    'image_id' => $image->id,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                session()->flash('error', 'Failed to regenerate image. Please try again.');
            }
        } catch (\Exception $e) {
            Log::error('Error regenerating image', [
                'image_id' => $image->id,
                'error' => $e->getMessage(),
            ]);
            session()->flash('error', 'An error occurred while regenerating image. Please try again.');
        }
    }

    /**
     * Delete an image.
     */
    public function deleteImage(int $imageId): void
    {
        $image = ProductImage::findOrFail($imageId);

        // Ensure the image belongs to the authenticated user
        if ($image->user_id !== Auth::id()) {
            abort(403);
        }

        // Delete file from storage if it's an uploaded image
        if (! $image->is_ai_generated && $image->image_url) {
            $path = str_replace('/storage/', '', $image->image_url);
            Storage::disk('public')->delete($path);
        }

        $image->delete();

        // Remove from imagePrompts array
        unset($this->imagePrompts[$imageId]);

        // Reload images
        $this->product->load('images');

        session()->flash('message', 'Image deleted successfully.');
    }

    /**
     * Get total images to be generated.
     */
    public function getTotalImagesProperty(): int
    {
        return $this->productOnlyCount + $this->lifestyleCount + $this->ugcSceneCount + $this->expertCount;
    }

    public function render()
    {
        return view('livewire.products.images');
    }
}
