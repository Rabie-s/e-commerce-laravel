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
        // ── Load Images ────────────────────────────────
        $data['uploaded_images'] = $this->record->images
            ->pluck('path')
            ->toArray();

        // ── Load Variants ──────────────────────────────
        $data['variants'] = $this->record->variants
            ->map(fn ($variant) => [
                'id'         => $variant->id,
                'sku'        => $variant->sku,
                'price'      => $variant->price,
                'is_default' => $variant->is_default,
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
        // ── Images ─────────────────────────────────────
        $this->record->images()->delete();

        $paths = array_values($this->data['uploaded_images'] ?? []);

        foreach ($paths as $index => $path) {
            $this->record->images()->create([
                'path'       => $path,
                'is_main'    => $index === 0,
                'sort_order' => $index,
            ]);
        }

        // ── Variants ───────────────────────────────────
        $variants = $this->data['variants'] ?? [];
        $submittedIds = collect($variants)->pluck('id')->filter()->toArray();

        // delete removed variants
        $this->record->variants()
            ->whereNotIn('id', $submittedIds)
            ->delete();

        foreach ($variants as $variantData) {
            $variant = isset($variantData['id'])
                ? $this->record->variants()->find($variantData['id'])
                : null;

            if ($variant) {
                // update existing
                $variant->update([
                    'sku'       => $variantData['sku'],
                    'price'     => $variantData['price'],
                    'is_active' => $variantData['is_active'] ?? true,
                ]);
            } else {
                // create new
                $variant = $this->record->variants()->create([
                    'sku'       => $variantData['sku'],
                    'price'     => $variantData['price'],
                    'is_default' => $variantData['is_default'] ?? false,
                ]);
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
