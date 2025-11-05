<?php

namespace App\Livewire\Products;

use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.app')]
class Edit extends Component
{
    use WithFileUploads;

    public Product $product;

    public string $name = '';

    public string $description_user = '';

    public string $description_ai = '';

    public $main_image;

    public string $type = 'physical';

    public string $store_link_url = '';

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
        $this->name = $product->name;
        $this->description_user = $product->description_user ?? '';
        $this->description_ai = $product->description_ai ?? '';
        $this->type = $product->type;
        $this->store_link_url = $product->store_link_url ?? '';
    }

    /**
     * Update the product.
     */
    public function update(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'description_user' => ['nullable', 'string'],
            'description_ai' => ['nullable', 'string'],
            'main_image' => ['nullable', 'image', 'max:2048'], // 2MB Max
            'type' => ['required', 'in:physical,digital'],
            'store_link_url' => ['nullable', 'url'],
        ]);

        // Handle new file upload
        if ($this->main_image) {
            // Delete old image if exists
            if ($this->product->main_image_url) {
                Storage::disk('public')->delete($this->product->main_image_url);
            }

            $validated['main_image_url'] = $this->main_image->store('products', 'public');
        }

        $this->product->update([
            'name' => $validated['name'],
            'description_user' => $validated['description_user'],
            'description_ai' => $validated['description_ai'],
            'main_image_url' => $validated['main_image_url'] ?? $this->product->main_image_url,
            'type' => $validated['type'],
            'store_link_url' => $validated['store_link_url'],
        ]);

        session()->flash('message', 'Product updated successfully.');

        $this->redirect(route('products.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.products.edit');
    }
}
