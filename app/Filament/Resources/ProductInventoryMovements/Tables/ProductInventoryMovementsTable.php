<?php

namespace App\Filament\Resources\ProductInventoryMovements\Tables;

use App\Enums\MovementType;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ProductInventoryMovementsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('variant.product.name')
                    ->label('Product')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('variant.sku')
                    ->label('SKU')
                    ->badge()
                    ->color('gray')
                    ->searchable(),

                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color(fn ($state) => $state instanceof MovementType ? $state->color() : 'gray')
                    ->sortable(),

                TextColumn::make('quantity')
                    ->label('Quantity')
                    ->suffix(' units')
                    ->sortable(),

                TextColumn::make('variant.stock')
                    ->label('Current Stock')
                    ->badge()
                    ->color(fn ($state) => $state > 0 ? 'success' : 'danger'),

                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Movement Type')
                    ->options([
                        MovementType::Purchase->value => MovementType::Purchase->label(),
                        MovementType::Return->value => MovementType::Return->label(),
                        MovementType::Sale->value => MovementType::Sale->label(),
                        MovementType::Damaged->value => MovementType::Damaged->label(),
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
