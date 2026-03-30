<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class BrandResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'main_image' => $this->whenLoaded('mainImage', fn() => $this->mainImage?->path ? Storage::url($this->mainImage->path) : null),
        ];
    }
}
