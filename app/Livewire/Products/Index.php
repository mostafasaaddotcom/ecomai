<?php

namespace App\Livewire\Products;

use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
class Index extends Component
{
    use WithPagination;

    /**
     * Delete a product.
     */
    public function delete(Product $product): void
    {
        // Ensure the product belongs to the authenticated user
        if ($product->user_id !== Auth::id()) {
            abort(403);
        }

        // Delete the image file if it exists
        if ($product->main_image_url) {
            Storage::disk('public')->delete($product->main_image_url);
        }

        $product->delete();

        session()->flash('message', 'Product deleted successfully.');
    }

    public function render()
    {
        return view('livewire.products.index', [
            'products' => Auth::user()->products()->latest()->paginate(10),
        ]);
    }
}
