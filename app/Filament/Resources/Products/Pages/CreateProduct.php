<?php

namespace App\Filament\Resources\Products\Pages;

use App\Enums\MovementType;
use App\Filament\Resources\Products\ProductResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    protected function afterCreate(): void
    {
        // ── Images ────────────────────────────────────
        $paths = array_values($this->data['uploaded_images'] ?? []);

        foreach ($paths as $index => $path) {
            $this->record->images()->create([
                'path' => $path,
                'is_main' => $index === 0,
                'sort_order' => $index,
            ]);

        }

        // ── Variants ──────────────────────────────────
        $variants = $this->data['variants'] ?? [];

        foreach ($variants as $variantData) {
            $variant = $this->record->variants()->create([
                'sku' => $variantData['sku'],
                'price' => $variantData['price'],
                'is_default' => $variantData['is_default'] ?? false,
            ]);

            // ── Initial Stock ──────────────────────────────
            $initialStock = (int) ($variantData['initial_stock'] ?? 0);

            if ($initialStock > 0) {
                $variant->movements()->create([
                    'type' => MovementType::Purchase,
                    'quantity' => $initialStock,
                ]);
            }

            // ── Attribute Values ───────────────────────
            $attributeValues = array_values($variantData['attribute_values'] ?? []);

            foreach ($attributeValues as $av) {
                $variant->attributeValues()->attach($av['attribute_value_id']);
            }
        }
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        unset($data['uploaded_images']);
        unset($data['variants']);

        return $data;
    }
}
