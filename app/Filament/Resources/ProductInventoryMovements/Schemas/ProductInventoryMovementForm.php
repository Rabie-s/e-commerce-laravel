<?php

namespace App\Filament\Resources\ProductInventoryMovements\Schemas;

use App\Enums\MovementType;
use App\Models\ProductVariant;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProductInventoryMovementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([

                // ── Variant Select ─────────────────────────
                Section::make('Select Variant')
                    ->icon('heroicon-o-squares-2x2')
                    ->schema([
                        Select::make('product_variant_id')
                            ->label('Product Variant')
                            ->options(function () {
                                return ProductVariant::with('product')
                                    ->get()
                                    ->mapWithKeys(fn ($v) => [
                                        $v->id => "{$v->product->name} — SKU: {$v->sku}",
                                    ]);
                            })
                            ->searchable()
                            ->required()
                            ->prefixIcon('heroicon-o-squares-2x2')
                            ->live()
                            ->columnSpanFull(),
                    ]),

                // ── Movement Details ───────────────────────
                Section::make('Movement Details')
                    ->icon('heroicon-o-archive-box')
                    ->columns(3)
                    ->schema([
                        Placeholder::make('current_stock')
                            ->label('Current Stock')
                            ->content(function ($get) {
                                $variantId = $get('product_variant_id');
                                if (! $variantId) {
                                    return '— units';
                                }
                                $variant = ProductVariant::find($variantId);

                                return $variant ? "{$variant->stock} units" : '— units';
                            }),

                        Select::make('type')
                            ->label('Movement Type')
                            ->options(MovementType::class)
                            ->required()
                            ->live()
                            ->helperText(fn ($state) => match ($state) {
                                MovementType::Purchase->value => '+ Stock in from supplier.',
                                MovementType::Return->value => '+ Customer returned items.',
                                MovementType::Sale->value => '- Stock out due to sale.',
                                MovementType::Damaged->value => '- Stock removed, damaged.',
                                default => 'Select a type.',
                            }),

                        TextInput::make('quantity')
                            ->label('Quantity')
                            ->numeric()
                            ->integer()
                            ->minValue(1)
                            ->required()
                            ->suffix('units'),
                    ]),

            ]);
    }
}
