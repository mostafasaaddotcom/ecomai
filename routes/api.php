<?php

use App\Http\Controllers\Api\V1\ApiServiceKeyController;
use App\Http\Controllers\Api\V1\ProductAnalysisController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\ProductCopyController;
use App\Http\Controllers\Api\V1\ProductImageController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    // API Service Keys
    Route::get('api-service-keys', [ApiServiceKeyController::class, 'show']);

    Route::get('products/{product}', [ProductController::class, 'show']);
    Route::put('products/{product}', [ProductController::class, 'update']);

    // Product Analysis
    Route::get('products/{product}/analysis', [ProductAnalysisController::class, 'show']);
    Route::put('product-analyses/{productAnalysis}', [ProductAnalysisController::class, 'update']);

    // Product Copies
    Route::get('products/{product}/copies', [ProductCopyController::class, 'index']);
    Route::post('products/{product}/copies', [ProductCopyController::class, 'store']);
    Route::put('product-copies/{productCopy}', [ProductCopyController::class, 'update']);
    Route::delete('product-copies/{productCopy}', [ProductCopyController::class, 'destroy']);

    // Product Images
    Route::get('products/{product}/images', [ProductImageController::class, 'index']);
    Route::post('products/{product}/images', [ProductImageController::class, 'store']);
    Route::get('product-images/reference/{referenceId}', [ProductImageController::class, 'getByReferenceId']);
    Route::put('product-images/{productImage}', [ProductImageController::class, 'update']);
    Route::post('product-images/webhook', [ProductImageController::class, 'webhook']);
});
