<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::whereNull('parent_id')
            ->with(['children.mainImage', 'mainImage'])
            ->get();

        return CategoryResource::collection($categories);
    }

    public function show(Category $category)
    {
        $category->load(['children.mainImage', 'mainImage']);

        return CategoryResource::make($category);
    }
}
