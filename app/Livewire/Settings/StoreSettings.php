<?php

namespace App\Livewire\Settings;

use App\Models\UserStore;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class StoreSettings extends Component
{
    public string $platform = 'woocommerce';
    public string $name = '';
    public string $store_link = '';
    public string $consumer_key = '';
    public string $consumer_secret = '';
    public string $api_key = '';

    public $stores = [];

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->loadStores();
    }

    /**
     * Load user's stores from database.
     */
    public function loadStores(): void
    {
        $this->stores = Auth::user()->stores()->orderBy('created_at', 'desc')->get();
    }

    /**
     * Save a new store based on selected platform.
     */
    public function save(): void
    {
        if ($this->platform === 'woocommerce') {
            $this->storeWoocommerce();
        } elseif ($this->platform === 'easyorders') {
            $this->storeEasyorders();
        } elseif ($this->platform === 'shopify') {
            session()->flash('error', 'Shopify integration is coming soon.');
        }
    }

    /**
     * Create a new WooCommerce store.
     */
    public function storeWoocommerce(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'store_link' => ['required', 'string', 'url', 'max:255'],
            'consumer_key' => ['required', 'string', 'max:255'],
            'consumer_secret' => ['required', 'string', 'max:255'],
        ]);

        UserStore::create([
            'user_id' => Auth::id(),
            'name' => $validated['name'],
            'store_link' => $validated['store_link'],
            'platform' => 'woocommerce',
            'credentials' => [
                'consumer_key' => $validated['consumer_key'],
                'consumer_secret' => $validated['consumer_secret'],
            ],
        ]);

        // Reset form
        $this->reset(['name', 'store_link', 'consumer_key', 'consumer_secret', 'api_key']);

        $this->loadStores();
        session()->flash('message', 'WooCommerce store added successfully.');
    }

    /**
     * Create a new EasyOrders store.
     */
    public function storeEasyorders(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'store_link' => ['required', 'string', 'url', 'max:255'],
            'api_key' => ['required', 'string', 'max:255'],
        ]);

        UserStore::create([
            'user_id' => Auth::id(),
            'name' => $validated['name'],
            'store_link' => $validated['store_link'],
            'platform' => 'easyorders',
            'credentials' => [
                'api_key' => $validated['api_key'],
            ],
        ]);

        // Reset form
        $this->reset(['name', 'store_link', 'consumer_key', 'consumer_secret', 'api_key']);

        $this->loadStores();
        session()->flash('message', 'EasyOrders store added successfully.');
    }

    /**
     * Delete a store.
     */
    public function deleteStore(int $storeId): void
    {
        $store = UserStore::where('id', $storeId)
            ->where('user_id', Auth::id())
            ->first();

        if (!$store) {
            session()->flash('error', 'Store not found.');
            return;
        }

        $store->delete();

        $this->loadStores();
        session()->flash('message', 'Store deleted successfully.');
    }

    /**
     * Render the component.
     */
    public function render()
    {
        return view('livewire.settings.store-settings');
    }
}
