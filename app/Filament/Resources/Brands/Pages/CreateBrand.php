<?php

namespace App\Filament\Resources\Brands\Pages;

use App\Filament\Resources\Brands\BrandResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBrand extends CreateRecord
{
    protected static string $resource = BrandResource::class;

    protected function afterCreate(): void
    {
        $imageUpload = $this->data['image_upload'] ?? null;

        if ($imageUpload) {
            // get the path value from the array
            $path = array_values($imageUpload)[0];
            $this->record->images()->delete();
            $this->record->images()->create([
                'path'       =>$path,
                'is_main'    => true,
                'sort_order' => 1,
            ]);
        }
    }
}
