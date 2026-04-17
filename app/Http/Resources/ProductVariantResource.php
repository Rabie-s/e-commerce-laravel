<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ProductVariantResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'sku' => $this->sku,
            'price' => $this->price,
            'is_default' => $this->is_default,
            'stock' => $this->stock,
            'main_image' => $this->whenLoaded('mainImage', fn () => $this->mainImage?->path ? Storage::url($this->mainImage->path) : null),
            'attribute_values' => $this->whenLoaded(
                'attributeValues',
                fn () => $this->attributeValues->map(fn ($value) => [
                    'id' => $value->id,
                    'value' => $value->value,
                    'type' => $value->relationLoaded('type') && $value->type
                        ? ['id' => $value->type->id, 'name' => $value->type->name]
                        : null,
                ]),
                []  // ← default to empty array instead of omitting the key entirely
            ),
        ];
    }
}
