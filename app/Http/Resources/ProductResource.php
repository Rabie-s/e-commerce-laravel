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
            'main_image' => $this->whenLoaded('mainImage', fn () => $this->mainImage?->path ? Storage::url($this->mainImage->path) : null),
            'category' => $this->whenLoaded('category', fn () => [
                'id' => $this->category->id,
                'name' => $this->category->name,
            ]),
            'brand' => $this->whenLoaded('brand', fn () => [
                'id' => $this->brand->id,
                'name' => $this->brand->name,
            ]),
            'default_variant' => $this->whenLoaded(
                'defaultVariant',
                fn () => $this->defaultVariant
                    ? ProductVariantResource::make($this->defaultVariant)
                    : null
            ),
            'variants' => ProductVariantResource::collection($this->whenLoaded('variants')),
        ];
    }
}
