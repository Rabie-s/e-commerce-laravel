<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use App\Filament\Resources\Products\Schemas\ProductForm;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Schema;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    public function form(Schema $schema): Schema
    {
        return ProductForm::configure($schema);
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // ── Load Product Image (single main image) ────
        $data['uploaded_images'] = $this->record->images()
            ->where('is_main', true)
            ->pluck('path')
            ->toArray();

        // ── Load Variants ──────────────────────────────
        $data['variants'] = $this->record->variants
            ->map(fn ($variant) => [
                'id'         => $variant->id,
                'sku'        => $variant->sku,
                'price'      => $variant->price,
                'is_default' => $variant->is_default,
                'variant_images' => $variant->images
                    ->pluck('path')
                    ->toArray(),
                'attribute_values' => $variant->attributeValues
                    ->map(fn ($av) => [
                        'attribute_value_id' => $av->id,
                    ])
                    ->toArray(),
            ])
            ->toArray();

        return $data;
    }


    protected function afterSave(): void
    {
        // ── Product Image (single main image) ─────────
        $this->record->images()->delete();

        $productImagePath = array_values($this->data['uploaded_images'] ?? [])[0] ?? null;

        if ($productImagePath) {
            $this->record->images()->create([
                'path' => $productImagePath,
                'is_main' => true,
                'sort_order' => 0,
            ]);
        }

        // ── Variants ───────────────────────────────────
        $variants = $this->data['variants'] ?? [];
        $submittedIds = collect($variants)->pluck('id')->filter()->toArray();

        // delete removed variants
        $this->record->variants()
            ->whereNotIn('id', $submittedIds)
            ->delete();

        // First, reset ALL variants to is_default = false
        $this->record->variants()->update(['is_default' => false]);

        // Find the first variant marked as default in the form
        $defaultVariantId = null;
        foreach ($variants as $variantData) {
            if (!empty($variantData['is_default'])) {
                // Use existing ID if available, otherwise we'll set it after creation
                $defaultVariantId = $variantData['id'] ?? null;
                break;
            }
        }

        foreach ($variants as $index => $variantData) {
            $variant = isset($variantData['id'])
                ? $this->record->variants()->find($variantData['id'])
                : null;

            $shouldBeDefault = false;

            if ($variant) {
                // update existing
                $shouldBeDefault = ($variant->id == $defaultVariantId);

                $variant->update([
                    'sku'       => $variantData['sku'],
                    'price'     => $variantData['price'],
                    'is_default' => $shouldBeDefault,
                ]);

                // ── Variant Images (update existing) ────
                $variant->images()->delete();

                $variantImages = array_values($variantData['variant_images'] ?? []);

                foreach ($variantImages as $index => $path) {
                    $variant->images()->create([
                        'path' => $path,
                        'is_main' => $index === 0,
                        'sort_order' => $index,
                    ]);
                }
            } else {
                // create new - check if this should be default (when no ID exists yet)
                $shouldBeDefault = ($defaultVariantId === null && !empty($variantData['is_default']));

                $variant = $this->record->variants()->create([
                    'sku'       => $variantData['sku'],
                    'price'     => $variantData['price'],
                    'is_default' => $shouldBeDefault,
                ]);

                // ── Variant Images (for new variant) ────
                $variantImages = array_values($variantData['variant_images'] ?? []);

                foreach ($variantImages as $index => $path) {
                    $variant->images()->create([
                        'path' => $path,
                        'is_main' => $index === 0,
                        'sort_order' => $index,
                    ]);
                }
            }

            // sync attribute values
            $attributeValues = collect($variantData['attribute_values'] ?? [])
                ->pluck('attribute_value_id')
                ->filter()
                ->toArray();

            $variant->attributeValues()->sync($attributeValues);
        }
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        unset($data['uploaded_images']);
        unset($data['variants']);

        return $data;
    }

}
