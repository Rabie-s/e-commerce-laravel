<?php

namespace App\Filament\Resources\Brands\Pages;

use App\Filament\Resources\Brands\BrandResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;

class EditBrand extends EditRecord
{
    protected static string $resource = BrandResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $imageUpload = $this->data['image_upload'] ?? null;

        if ($imageUpload) {
            // get the path value from the array
            $path = array_values($imageUpload)[0];
            if ($this->record->mainImage && Storage::disk('public')->exists($this->record->mainImage->path)) {
                Storage::disk('public')->delete($this->record->mainImage->path);
            }

            $this->record->images()->delete();
            $this->record->images()->create([
                'path'       => $path,
                'is_main'    => true,
                'sort_order' => 1,
            ]);
        }
    }
}
