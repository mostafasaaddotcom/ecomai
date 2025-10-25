<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\ProductCopy;
use App\Models\ProductImage;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Dashboard extends Component
{
    public $totalProducts = 0;
    public $totalImages = 0;
    public $totalCopies = 0;
    public $recentProducts;

    public function mount()
    {
        $userId = Auth::id();

        // Get statistics
        $this->totalProducts = Product::where('user_id', $userId)->count();
        $this->totalImages = ProductImage::where('user_id', $userId)->count();
        $this->totalCopies = ProductCopy::where('user_id', $userId)->count();

        // Get recent products
        $this->recentProducts = Product::where('user_id', $userId)
            ->with(['images', 'copies'])
            ->latest()
            ->take(5)
            ->get();
    }

    public function deleteProduct($productId)
    {
        $product = Product::where('user_id', Auth::id())->findOrFail($productId);
        $product->delete();

        // Refresh data
        $this->mount();

        session()->flash('message', 'Product deleted successfully.');
    }

    public function render()
    {
        return view('dashboard');
    }
}
