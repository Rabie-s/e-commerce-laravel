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
        // ── Product Image (single main image) ─────────
        $productImagePath = array_values($this->data['uploaded_images'] ?? [])[0] ?? null;

        if ($productImagePath) {
            $this->record->images()->create([
                'path' => $productImagePath,
                'is_main' => true,
                'sort_order' => 0,
            ]);
        }

        // ── Variants ──────────────────────────────────
        $variants = $this->data['variants'] ?? [];

        // Find the first variant marked as default
        $firstDefaultIndex = null;
        foreach ($variants as $index => $variantData) {
            if (!empty($variantData['is_default'])) {
                $firstDefaultIndex = $index;
                break;
            }
        }

        foreach ($variants as $index => $variantData) {
            // Only set is_default to true for the first one, all others false
            $isDefault = ($index === $firstDefaultIndex);

            $variant = $this->record->variants()->create([
                'sku' => $variantData['sku'],
                'price' => $variantData['price'],
                'is_default' => $isDefault,
            ]);

            // ── Variant Images ──────────────────────────
            $variantImages = array_values($variantData['variant_images'] ?? []);

            foreach ($variantImages as $imgIndex => $path) {
                $variant->images()->create([
                    'path' => $path,
                    'is_main' => $imgIndex === 0,
                    'sort_order' => $imgIndex,
                ]);
            }

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
