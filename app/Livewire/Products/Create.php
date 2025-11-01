<?php

namespace App\Livewire\Products;

use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Create extends Component
{
    public string $name = '';

    public string $description_user = '';

    public string $description_ai = '';

    public string $main_image = '';

    public string $type = 'physical';

    /**
     * Create a new product.
     */
    public function save(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'description_user' => ['nullable', 'string'],
            'description_ai' => ['nullable', 'string'],
            'main_image' => ['nullable', 'string'], // Path from AJAX upload
            'type' => ['required', 'in:physical,digital'],
        ]);

        $product = Product::create([
            'user_id' => Auth::id(),
            'name' => $validated['name'],
            'description_user' => $validated['description_user'],
            'description_ai' => $validated['description_ai'],
            'main_image_url' => $validated['main_image'] ?: null,
            'type' => $validated['type'],
        ]);

        // Send webhook notification
        try {
            Http::post(config('app.n8n_base_url') . 'update-description-using-ai', [
                'product_id' => $product->id,
                'user_id' => Auth::id(),
                'name' => $product->name,
                'description_user' => $product->description_user,
                'description_ai' => $product->description_ai,
                'type' => $product->type,
                'main_image_url' => $product->main_image_url,
                'app_url' => config('app.url')
            ]);
        } catch (\Exception $e) {
            // Log the error but don't fail the product creation
            \Log::error('Webhook notification failed: ' . $e->getMessage());
        }

        session()->flash('message', 'Product created successfully.');

        $this->redirect(route('products.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.products.create');
    }
}
