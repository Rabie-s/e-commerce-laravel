<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class HomeController extends Controller
{
    /**
     * Get the latest 8 products
     */
    public function latestProducts(): JsonResponse
    {
        $products = Product::query()
            ->with(['mainImage', 'defaultVariant', 'variants'])
            ->where('status', true)
            ->latest()
            ->take(4)
            ->get();

        return response()->json([
            'data' => $products->map(function (Product $product) {
                // Get price from default variant, or min price from all variants
                $price = $product->defaultVariant?->price
                    ?? $product->variants->min('price');

                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $price,
                    'main_image' => $product->mainImage,
                ];
            }),
        ]);
    }

    /**
     * Get the latest 8 categories
     */
    public function latestCategories(): JsonResponse
    {
        $categories = Category::query()
            ->with('mainImage')
            ->latest()
            ->take(4)
            ->get();

        return response()->json([
            'data' => $categories->map(function (Category $category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'main_image' => $category->mainImage ? Storage::url($category->mainImage->path) : null,
                ];
            }),
        ]);
    }

    /**
     * Get the latest 8 brands
     */
    public function latestBrands(): JsonResponse
    {
        $brands = Brand::query()
            ->with('mainImage')
            ->latest()
            ->take(8)
            ->get();

        return response()->json([
            'data' => $brands->map(function (Brand $brand) {
                return [
                    'id' => $brand->id,
                    'name' => $brand->name,
                    'description' => $brand->description,
                    'main_image' => $brand->mainImage ? Storage::url($brand->mainImage->path) : null,
                ];
            }),
        ]);
    }
}
