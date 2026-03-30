<?php

use App\Http\Controllers\Api\User\BrandController;
use App\Http\Controllers\Api\User\CategoryController;
use App\Http\Controllers\Api\User\ProductController;
use App\Http\Controllers\Api\User\OrderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')->group(function () {
    Route::apiResource('categories', CategoryController::class)
        ->only(['index', 'show']);

    Route::apiResource('brands', BrandController::class)
        ->only(['index', 'show']);

    Route::apiResource('products', ProductController::class)
        ->only(['index', 'show']);

    Route::apiResource('orders', OrderController::class);
});
