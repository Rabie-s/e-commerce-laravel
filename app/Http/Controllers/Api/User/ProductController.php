<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductCollection;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query()
            ->with(['mainImage', 'category', 'brand', 'defaultVariant' => function ($query) {
                $query->with(['attributeValues.type']);
            }])
            ->where('status', true);

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }

        if ($request->has('search')) {
            $query->where('name', 'like', '%'.$request->search.'%');
        }

        $products = $query->paginate(12);

        return new ProductCollection($products);
    }

    public function show(Product $product)
    {
        $product->load(['mainImage', 'images', 'category', 'brand', 'variants' => function ($query) {
            $query->with(['mainImage', 'attributeValues.type']);
        }]);

        return ProductResource::make($product);
    }
}
