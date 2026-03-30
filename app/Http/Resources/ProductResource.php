<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'status' => $this->status,
            'main_image' => $this->whenLoaded('mainImage', fn() => $this->mainImage?->path ? Storage::url($this->mainImage->path) : null),
            'images' => $this->whenLoaded('images', fn() => $this->images->map(fn($image) => [
                'id' => $image->id,
                'path' => $image->path ? Storage::url($image->path) : null,
                'is_main' => $image->is_main,
                'sort_order' => $image->sort_order,
            ])),
            'category' => $this->whenLoaded('category', fn() => [
                'id' => $this->category->id,
                'name' => $this->category->name,
            ]),
            'brand' => $this->whenLoaded('brand', fn() => [
                'id' => $this->brand->id,
                'name' => $this->brand->name,
            ]),
            'default_variant' => ProductVariantResource::make($this->whenLoaded('defaultVariant')),
            'variants' => ProductVariantResource::collection($this->whenLoaded('variants')),
        ];
    }
}
