<?php

use App\Http\Controllers\ImageUploadController;
use App\Livewire\Dashboard;
use App\Livewire\Products\Analysis;
use App\Livewire\Products\Copywriting;
use App\Livewire\Products\Create;
use App\Livewire\Products\Edit;
use App\Livewire\Products\Images;
use App\Livewire\Products\Index;
use App\Livewire\Products\Show;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\ApiTokens;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\Settings\TwoFactor;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return redirect()->route('login');
})->name('home');

Route::get('dashboard', Dashboard::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');
    Route::get('settings/api-tokens', ApiTokens::class)->name('settings.api-tokens');

    Route::get('settings/two-factor', TwoFactor::class)
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');

    // Products routes
    Route::get('products', Index::class)->name('products.index');
    Route::get('products/create', Create::class)->name('products.create');
    Route::get('products/{product}', Show::class)->name('products.show');
    Route::get('products/{product}/edit', Edit::class)->name('products.edit');
    Route::get('products/{product}/analysis', Analysis::class)->name('products.analysis');
    Route::get('products/{product}/copywriting', Copywriting::class)->name('products.copywriting');
    Route::get('products/{product}/images', Images::class)->name('products.images');

    // Image upload routes (AJAX)
    Route::post('upload-main-image', [ImageUploadController::class, 'uploadMainImage'])->name('upload.main-image');
    Route::post('products/{product}/upload-images', [ImageUploadController::class, 'uploadProductImages'])->name('upload.product-images');
});

require __DIR__.'/auth.php';
