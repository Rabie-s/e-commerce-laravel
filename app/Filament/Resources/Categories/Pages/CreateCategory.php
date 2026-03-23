<?php

namespace App\Filament\Resources\Categories\Pages;

use App\Filament\Resources\Categories\CategoryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCategory extends CreateRecord
{
    protected static string $resource = CategoryResource::class;

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
